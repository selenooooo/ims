<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\InternLeave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date_format:H:i',
        ]);

        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Block check-in if on approved leave
        $onLeaveToday = InternLeave::where('user_id', $user->id)
            ->where('leave_date', $today)
            ->where('status', 'approved')
            ->where('half_day', 'full')
            ->exists();

        if ($onLeaveToday) {
            return back()->with('error', 'You are on approved leave today.');
        }

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'attendance_date' => $today]
        );

        if ($attendance->check_in) {
            return back()->with('error', 'You already checked in today.');
        }

        $attendance->check_in = $request->check_in;

        // Optional: mark late if after 09:00
        $checkInTime = Carbon::today()->setTimeFromTimeString($request->check_in);
        $attendance->status = $checkInTime->gt(Carbon::today()->setTime(9,0)) ? 'late' : 'present';
        $attendance->check_in = $checkInTime;

        $attendance->save();

        return back()->with('success', 'Checked in successfully!');
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'check_out' => 'required|date_format:H:i',
        ]);

        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->first();

        if (!$attendance || $attendance->check_out) {
            return back()->with('error', 'You cannot check out.');
        }

        $attendance->check_out = $request->check_out;

        // Calculate total hours
        if ($attendance->check_in) {
            $checkIn = Carbon::parse($attendance->check_in);
            $checkOut = Carbon::parse($attendance->check_out);
            $attendance->total_hours = round($checkIn->floatDiffInHours($checkOut), 2);
        }

        $attendance->save();

        return back()->with('success', 'Checked out successfully!');
    }

    // Attendance history
    public function history(Request $request)
    {
        $user = Auth::user();
        $showAll = $request->has('show_all');

        $query = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', '<=', Carbon::today());

        // If date range is selected → ignore year & month
        if ($request->date_range) {

            $dates = explode(' to ', $request->date_range);
            $start = $dates[0];
            $end = $dates[1] ?? $dates[0];

            $end = Carbon::parse($end)->min(Carbon::today());

            $query->whereBetween('attendance_date', [$start, $end]);

        } else {

            if (!$showAll) {
                $query->whereYear('attendance_date', $request->year ?? now()->year);
            }

            if (!$showAll && $request->month) {
                $query->whereMonth('attendance_date', $request->month);
            }
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $attendances = $query
            ->orderBy('attendance_date', 'desc')
            ->get();

        return view('intern.attendance.history', compact('attendances'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Get today's attendance if exists
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->first();

        // Check if user's APPROVED leave today
        $onLeaveToday = InternLeave::where('user_id', $user->id)
            ->where('leave_date', $today)
            ->where('status', 'approved')
            ->where('half_day', 'full')
            ->exists();

        return view('intern.dashboard', compact(
            'todayAttendance',
            'onLeaveToday'
        ));
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return back()->with('success', 'Attendance record deleted successfully.');
    }

    public function calendar(Request $request)
    {
        $interns = User::whereHas('intern')->get(); // adjust if needed
        return view('supervisor.attendance.calendar', compact('interns'));
    }
  
    public function calendarEvents(Request $request)
    {
        // Get all active interns
        // $interns = User::whereHas('intern')->get();

        // Build query for attendance
        $query = Attendance::with('user');

        // Optional filter: specific intern
        if ($request->intern_id) {
            $query->where('user_id', $request->intern_id);
        }

        // Optional filter: specific date (from calendar click)
        if ($request->date) {
            $query->where('attendance_date', $request->date);
        }

        // Optional filter: date range
        if ($request->date_range) {
            $dates = explode(' to ', $request->date_range);
            $start = $dates[0];
            $end = $dates[1] ?? $dates[0];
            $end = Carbon::parse($end)->min(Carbon::today());

            $query->whereBetween('attendance_date', [$start, $end]);
        }

        // Get filtered attendances
        $attendances = $query->orderBy('attendance_date', 'desc')->get();

        // Build calendar events
        $calendarEvents = $attendances->map(function ($attendance) {
            $color = match($attendance->status) {
                'present' => '#22c55e',
                'late' => '#eab308',
                'half day' => '#f97316',
                'on leave' => '#3b82f6',
                default => '#ef4444',
            };

            $title = $attendance->user->name;
            if ($attendance->status === 'on leave') {
                $title .= ' (Leave)';
            } else {
                $title .= ' - ' . ucfirst($attendance->status);
            }

            return [
                'title' => $title,
                'start' => $attendance->attendance_date,
                'allDay' => true,
                'color' => $color,
            ];
        });

        // Return JSON for FullCalendar
        return response()->json($calendarEvents);
    }

    public function generatePdf(Request $request)
    {
        $user = Auth::user();
        $intern = $user->intern;

        if (!$intern) {
            return back()->with('error', 'Intern record not found.');
        }

        $query = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', '<=', Carbon::today());

        if ($request->year) $query->whereYear('attendance_date', $request->year);
        if ($request->month) $query->whereMonth('attendance_date', $request->month);
        if ($request->status) $query->where('status', $request->status);
        if ($request->date_range) {
            $dates = explode(' to ', $request->date_range);
            $start = $dates[0];
            $end = $dates[1] ?? $dates[0];
            $end = Carbon::parse($end)->min(Carbon::today());
            $query->whereBetween('attendance_date', [$start, $end]);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->get();

        $pdf = Pdf::loadView('intern.attendance.pdf', [
            'attendances' => $attendances,
            'user' => $user,
            'report_date' => $intern->report_date,
            'end_date' => $intern->end_date,
            'intern_duration' => $intern->intern_duration,
        ]);

        return $pdf->download('attendance-record.pdf');
    }

}
