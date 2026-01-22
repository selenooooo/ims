<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Intern;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SupervisorController extends Controller
{
    // List all interns
    public function index()
    {
        // List interns
        $interns = User::with('intern')
        ->where('role', 'intern')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        $activeTodayCount = Intern::where('status', 'active')->count();

        $oldInterns = Intern::where('status', 'completed')->count();
        

        return view('supervisor.interns.index', compact(
            'interns',
            'activeTodayCount',
            'oldInterns'
        ));
    }

    // Show form to register a new intern
    public function create()
    {
        return view('supervisor.interns.create');
    }

    public function updateAttendance(Request $request, Attendance $attendance)
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        $request->validate([
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after_or_equal:check_in', // strictly check
        ], [
            'check_out.after_or_equal' => 'Check-out time cannot be earlier than check-in time.',
        ]);

        $checkIn = $request->check_in;
        $checkOut = $request->check_out;

        $cutoffLate = '09:00';

        if ($checkIn > $cutoffLate) {
            $status = 'late';
        } else {
            $status = 'present';
        }

        // If the day is a leave day
        if ($attendance->leave_id) {
            $status = 'on leave';
        }

        // Update attendance
        $attendance->update([
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => $status,
            // if no check out time, 0 total hrs 
            'total_hours' => ($checkIn && $checkOut) ? round((strtotime($checkOut) - strtotime($checkIn)) / 3600, 2) : 0,
        ]);

        return back()->with('success', 'Attendance updated successfully.');
    }

    // Store new intern
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string|unique:users,employee_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'report_date' => 'required|date',
            'intern_duration' => 'required|integer|min:1|max:12',
            'end_date'=> 'required|date',
            'password' => 'required|string|min:6|confirmed',
        ]);

        DB::transaction(function() use ($request) {
            $alBalance = $request->intern_duration;
            
            $user = User::create([
                'employee_id' => $request->employee_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'intern',
            ]);

            Intern::create([
                'user_id' => $user->id,
                'employee_id' => $request->employee_id,
                'report_date' => $request->report_date,
                'intern_duration' => $request->intern_duration,
                'end_date' => $request->end_date,
                'al_balance' => $alBalance,
            ]);
        });

        return redirect()->route('supervisor.interns.index')
            ->with('success', 'Intern registered successfully.');
    }

    // View all attendance of interns
    public function attendance(Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);

        // Fetch active interns for the dropdown
        $interns = User::where('role', 'intern')
            ->whereHas('intern', fn($q) => $q->where('status', 'active'))
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $showAll = $request->has('show_all'); // check if "Show All Attendance" is clicked

        $year = !$showAll ? ($request->year ?? now()->year) : null;
        $month = !$showAll ? ($request->month ?? now()->month) : null;

        $attendances = Attendance::with('user')
            ->whereDate('attendance_date', '<=', Carbon::today())
            ->when($request->intern_id, fn($q) => $q->where('user_id', $request->intern_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when(!$showAll && $year, fn($q) => $q->whereYear('attendance_date', $year))
            ->when(!$showAll && $month, fn($q) => $q->whereMonth('attendance_date', $month))
            ->when($request->date_range, function ($q) use ($request) {
                [$start, $end] = explode(' to ', $request->date_range);
                $q->whereBetween('attendance_date', [Carbon::parse($start), Carbon::parse($end)->min(Carbon::today())]);
            })
            ->orderBy('attendance_date', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('supervisor.attendance.index', compact(
            'attendances',
            'interns',
            'year',
            'month',
            'showAll'
        ));
    }

    public function dashboard()
    {
        // Get all interns
        $internIds = User::where('role', 'intern')->pluck('id');

        // Get today's attendance for all interns
        $today = Carbon::today()->toDateString();
        $todayAttendances = Attendance::with('user')
            ->whereIn('user_id', $internIds)
            ->where('attendance_date', $today)
            ->where('status', '!=', 'on leave')
            ->get();

        // Today leave list
        $todayLeaves = Attendance::with([
            'user',
            'approvedLeave.leaveType'
        ])
        ->whereDate('attendance_date', today())
        ->whereNotNull('leave_id')
        ->get();


        return view('supervisor.dashboard', compact('todayAttendances','todayLeaves'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function showIntern(User $user)
    {
        $user->load('intern'); // load interns table

        return response()->json([
            'id' => $user->id,
            'employee_id' => $user->employee_id,
            'name' => $user->name,
            'email' => $user->email,

            // data from interns table
            'report_date' => optional($user->intern)->report_date,
            'intern_duration' => optional($user->intern)->intern_duration,
            'end_date' => optional($user->intern)->end_date,
        ]);
    }

    public function update(Request $request, $employee_id)
    {
        $intern = Intern::where('employee_id', $employee_id)->firstOrFail();

        $intern->update([
            'report_date'     => $request->report_date,
            'intern_duration' => $request->intern_duration,
            'end_date'        => $request->end_date,
        ]);

        return redirect()
            ->route('supervisor.interns.index')
            ->with('success', 'Intern updated successfully.');
    }

    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            $user->attendances()->delete();
            $user->leaves()->delete();
            $user->intern()->delete();
            $user->delete();
        });

        return redirect()
            ->route('supervisor.interns.index')
            ->with('success', 'Intern deleted successfully');
    }

    public function destroyBySupervisor(Leave $leave)
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        if ($leave->status === 'rejected') {
            return back()->with('error', 'Rejected leave cannot be deleted.');
        }

        // Optional: remove related attendance
        if ($leave->attendance) {
            $leave->attendance->delete();
        }

        $leave->delete();

        return back()->with('success', 'Leave record deleted successfully.');
    }
  
}
