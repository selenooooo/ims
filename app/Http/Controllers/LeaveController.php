<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternLeave;
use App\Models\LeaveType;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Intern;

class LeaveController extends Controller
{
    /* INTERN */

    public function create()
    {
        abort_if(auth()->user()->role !== 'intern', 403);

        return view('intern.leave.create', [
            'leaveTypes' => LeaveType::all()
        ]);
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 'intern', 403);

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'leave_date' => 'required|date',
            'half_day' => 'required|in:full,am,pm',
            'reason' => 'nullable|string'
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $intern = auth()->user()->intern;
        $leave_date = $request->leave_date;
        $half_day   = $request->half_day;


        // check existing leave
        $existingLeaves = InternLeave::where('user_id', auth()->id())
            ->where('leave_date', $leave_date)
            ->whereIn('status', ['pending', 'approved'])
            ->get();

        foreach ($existingLeaves as $leave) {
            if ($leave->half_day === 'full') {
                return back()->with('warning', "Leave already applied for full day on {$leave_date}.");
            }

            if ($half_day === 'full') {
                return back()->with('warning', "Cannot apply full day leave because {$leave->half_day} leave already exists on {$leave_date}.");
            }

            if ($leave->half_day === $half_day) {
                return back()->with('warning', "Leave already applied for {$half_day} on {$leave_date}.");
            }
        }

        // check attendance 
        $attendanceExists = $intern->attendances()
            ->where('attendance_date', $leave_date)
            ->whereNotNull('check_in')
            ->exists();

        if ($attendanceExists) {
            return back()->with('warning', "Cannot apply leave. Attendance already recorded for {$leave_date}.");
        }

        $leave_days = $request->half_day === 'full' ? 1.0 : 0.5;

        // SINGLE SOURCE OF TRUTH
        if ($leaveType->code === 'AL') {

            if ($intern->al_balance < $leave_days) {
                return back()->with('warning', 
                    "Cannot apply Annual Leave. Remaining balance: {$intern->al_balance} day(s)."
                );
            }
        }

        InternLeave::create([
            'user_id' => auth()->id(),
            'leave_type_id' => $request->leave_type_id,
            'leave_date' => $request->leave_date,
            'half_day' => $request->half_day,
            'leave_days' => $leave_days,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // Safe deduction
        if ($leaveType->code === 'AL') {
            $intern->decrement('al_balance', $leave_days);
        }

        return redirect()
            ->route('intern.leave.index', ['filter' => 'pending'])
            ->with('success', 'Leave applied successfully.');
    }

    public function internIndex(Request $request)
    {
        $user = auth()->user();

        $query = InternLeave::where('user_id', $user->id)
            ->with('leaveType');

        if ($request->filled('filter') && in_array($request->filter, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->filter);
        }

        $leaves = $query->orderBy('leave_date', 'desc')->get();

        // AL DISPLAY
        $intern = $user->intern;

        $alTotal = $intern->intern_duration;   // yearly quota
        $alRemaining = $intern->al_balance;    // live balance
        $alUsed = $alTotal - $alRemaining;     // derived value

        return view('intern.leave.index', compact(
            'leaves',
            'alTotal',
            'alUsed',
            'alRemaining'
        ));
    }

    public function destroy(InternLeave $leave)
    {
        abort_if(auth()->user()->role !== 'intern', 403);
        abort_if($leave->leaveType->code === 'IOD', 403); // Cannot delete IOD leave

        // If AL, restore AL balance
        if ($leave->leaveType->code === 'AL') {
            $intern = auth()->user()->intern;
            $restore = $leave->leave_days ?? ($leave->half_day === 'full' ? 1.0 : 0.5);
            $intern->increment('al_balance', $restore);
        }

        // Remove attendance if exists
        if ($leave->attendance) {
            $leave->attendance->delete();
        }

        $leave->delete();

        return back()->with('success', 'Leave cancelled successfully.');
    }

    /* SUPERVISOR */

    public function index(Request $request)
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        $query = InternLeave::with(['user', 'leaveType']);

        if ($request->filled('filter') && in_array($request->filter, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->filter);
        }

        $leaves = $query->orderBy('leave_date', 'desc')->get();

        $internALBalances = Intern::with('user')->get();

        return view(
            'supervisor.leave.index', compact('leaves', 'internALBalances')
        );
    }

    public function approve(InternLeave $leave)
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        if ($leave->status === 'approved') {
            return back();
        }

        $leave->update(['status' => 'approved']);

        Attendance::updateOrCreate(
            [
                'user_id' => $leave->user_id,
                'attendance_date' => $leave->leave_date,
            ],
            [
                'leave_id' => $leave->id,
                'status' => 'on leave',
                'check_in' => null,
                'check_out' => null,
                'total_hours' => 0,
            ]
        );

        return back()->with('success', 'Leave approved and attendance recorded.');
    }

    public function reject(InternLeave $leave)
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        // Prevent double reject
        if ($leave->status === 'rejected') {
            return back();
        }

        // Restore AL balance if Annual Leave
        if ($leave->leaveType->code === 'AL') {
            $intern = $leave->user->intern;
            $intern->increment('al_balance', $leave->leave_days);
        }

        $leave->update(['status' => 'rejected']);

        return back()->with('success', 'Leave rejected successfully.');
    }

    public function createSupervisor()
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        return view('supervisor.leave.create', [
            'interns' => User::where('role', 'intern')->get(),
            'leaveTypes' => LeaveType::all(),
        ]);
    }

    public function storeSupervisor(Request $request)
    {
        $skippedInterns = [];
        $appliedCount = 0;

        abort_if(auth()->user()->role !== 'supervisor', 403);

        $request->validate([
            'leave_date' => 'required|date',
            'leave_type_id' => 'required|exists:leave_types,id',
            'half_day' => 'required|in:full,am,pm',
            'user_id' => 'required',
            'reason' => 'nullable|string',
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $leave_days = $request->half_day === 'full' ? 1.0 : 0.5;

        $createLeave = function (User $user) use (
            $request,
            $leaveType,
            $leave_days,
            &$skippedInterns,
            &$appliedCount
        ) {
            $intern = $user->intern;
            $leave_date = $request->leave_date;
            $half_day   = $request->half_day;

            // Check existing leaves
            $existingLeaves = InternLeave::where('user_id', $user->id)
            ->where('leave_date', $leave_date)
            ->whereIn('status', ['pending', 'approved'])
            ->get();

            foreach ($existingLeaves as $leave) {
                if ($leave->half_day === 'full') {
                    $skippedInterns[] = "{$user->name} (full-day leave already exists)";
                    return;
                }

                if ($half_day === 'full') {
                    $skippedInterns[] = "{$user->name} (cannot apply, {$leave->half_day} leave exists)";
                    return;
                }

                if ($leave->half_day === $half_day) {
                    $skippedInterns[] = "{$user->name} (leave already applied for {$half_day})";
                    return;
                }
            }

            //  Check if attendance already recorded
            $attendanceExists = $intern->attendances()
                ->where('attendance_date', $leave_date)
                ->whereNotNull('check_in')
                ->exists();

            if ($attendanceExists) {
                $skippedInterns[] = "{$user->name} (attendance already recorded)";
                return;
            }

            //  Check AL balance
            if ($leaveType->code === 'AL' && $intern->al_balance < $leave_days) {
                $skippedInterns[] = "{$user->name} (insufficient AL)";
                return;
            }

            // Create leave
            $leave = InternLeave::create([
                'user_id' => $user->id,
                'leave_type_id' => $request->leave_type_id,
                'leave_date' => $leave_date,
                'half_day' => $half_day,
                'leave_days' => $leave_days,
                'reason' => $request->reason,
                'status' => 'approved',
            ]);

            // Deduct AL
            if ($leaveType->code === 'AL') {
                $intern->decrement('al_balance', $leave_days);
            }

            // Update attendance
            Attendance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'attendance_date' => $leave_date
                ],
                [
                    'leave_id' => $leave->id,
                    'status' => 'on leave',
                    'check_in' => null,
                    'check_out' => null,
                    'total_hours' => 0
                ]
            );

            $appliedCount++;
        };

        if ($request->user_id === 'all') {
            User::where('role', 'intern')->get()->each($createLeave);
        } else {
            $user = User::findOrFail($request->user_id);
            $createLeave($user);
        }

        if ($appliedCount > 0) {
            session()->flash('success', "Leave applied successfully for {$appliedCount} intern(s).");
        }

        if (!empty($skippedInterns)) {
            session()->flash('warning', "Skipped: " . implode(', ', $skippedInterns) . ".");
        }

        return redirect()->route('supervisor.leave.index');
    }

    public function destroyBySupervisor(InternLeave $leave)
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        // UI already handles this, but backend must secured
        if ($leave->status !== 'approved') {
            return back()->with('error', 'Only approved leave can be removed.');
        }

        // Restore AL balance
        if ($leave->leaveType->code === 'AL') {
            $intern = $leave->user->intern;

            if ($intern) {
                $intern->increment('al_balance', $leave->leave_days);
            }
        }

        // Remove attendance if exists
        $leave->attendance?->delete();

        $leave->delete();

        return back()->with('success', 'Leave removed and balance restored.');
    }
}
