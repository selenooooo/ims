<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternLeave;
use App\Models\LeaveType;
use App\Models\Attendance;
use App\Models\User;

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

        // Validate request
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'leave_date' => 'required|date',
            'half_day' => 'required|in:full,am,pm',
            'reason' => 'nullable|string'
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $intern = auth()->user()->intern;

        // Determine leave_days based on half_day
        $leave_days = $request->half_day === 'full' ? 1.0 : 0.5;

        // Only check AL balance if leave type is Annual Leave
        if ($leaveType->code === 'AL' && $intern->al_balance < $leave_days) {
            return back()->withErrors([
                'leave_type_id' => 'Insufficient Annual Leave balance.'
            ]);
        }

        // Create leave record
        $leave = InternLeave::create([
            'user_id' => auth()->id(),
            'leave_type_id' => $request->leave_type_id,
            'leave_date' => $request->leave_date,
            'half_day' => $request->half_day,
            'leave_days' => $leave_days,
            'reason' => $request->reason,
            'status' => $leaveType->code === 'AL' ? 'approved' : 'pending', // auto-approve AL, others pending
        ]);

        // Deduct AL balance if Annual Leave
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

        $query = InternLeave::where('user_id', auth()->id())
            ->with('leaveType');

        if ($request->filled('filter') && in_array($request->filter, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->filter);
        }

        $leaves = $query->orderBy('leave_date', 'desc')->get();

        // AL BALANCE LOGIC
        $intern = $user->intern;
        $alTotal = $intern->intern_duration;

        $alUsed = InternLeave::where('user_id', $user->id)
            ->whereHas('leaveType', fn($q) => $q->where('code', 'AL'))
            ->where('status', 'approved')
            ->sum('leave_days');

        $alRemaining = max($alTotal - $alUsed, 0);

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

        return view('supervisor.leave.index', compact('leaves'));
    }

    public function approve(InternLeave $leave)
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        if ($leave->status === 'approved') {
            return back();
        }

        $leave->update(['status' => 'approved']);

        // Deduct AL only if Annual Leave
        if ($leave->leaveType->code === 'AL') {
            $intern = $leave->user->intern;
            $deduction = $leave->half_day === 'full' ? 1.0 : 0.5;

            if ($intern && $intern->al_balance >= $deduction) {
                $intern->decrement('al_balance', $deduction);
            }
        }

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
        $leave->update(['status' => 'rejected']);
        return back();
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
        abort_if(auth()->user()->role !== 'supervisor', 403);

        $request->validate([
            'leave_date' => 'required|date',
            'leave_type_id' => 'required|exists:leave_types,id',
            'half_day' => 'required|in:full,am,pm',
            'user_id' => 'required', // can be 'all' or an intern ID
            'reason' => 'nullable|string',
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $leave_days = $request->half_day === 'full' ? 1.0 : 0.5;

        // Function to create leave + attendance + deduct AL
        $createLeave = function(User $intern) use ($request, $leaveType, $leave_days) {
            if ($leaveType->code === 'AL' && $intern->intern->al_balance >= $leave_days) {
                $intern->intern->decrement('al_balance', $leave_days);
            }

            $leave = InternLeave::create([
                'user_id' => $intern->id,
                'leave_type_id' => $request->leave_type_id,
                'leave_date' => $request->leave_date,
                'half_day' => $request->half_day,
                'leave_days' => $leave_days,
                'reason' => $request->reason,
                'status' => 'approved',
            ]);

            Attendance::updateOrCreate(
                [
                    'user_id' => $intern->id,
                    'attendance_date' => $request->leave_date
                ],
                [
                    'leave_id' => $leave->id,
                    'status' => 'on leave',
                    'check_in' => null,
                    'check_out' => null,
                    'total_hours' => 0
                ]
            );
        };

        if ($request->user_id === 'all') {
            User::where('role', 'intern')->get()->each($createLeave);
        } else {
            $intern = User::findOrFail($request->user_id);
            $createLeave($intern);
        }

        return redirect()
            ->route('supervisor.leave.index')
            ->with('success', 'Leave added successfully.');
    }
}
