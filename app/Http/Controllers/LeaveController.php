<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternLeave;
use App\Models\LeaveType;
use App\Models\Attendance;

class LeaveController extends Controller
{
    /* ================= INTERN ================= */

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

        InternLeave::create([
            'user_id' => auth()->id(),
            'leave_type_id' => $request->leave_type_id,
            'leave_date' => $request->leave_date,
            'half_day' => $request->half_day,
            'reason' => $request->reason,
        ]);

       return redirect()
        ->route('intern.leave.index', ['filter' => 'pending'])
        ->with('success', 'Leave applied');

    }

    public function internIndex(Request $request)
    {
        $query = InternLeave::where('user_id', auth()->id())
            ->with('leaveType');

        // Apply filter if exists
        if ($request->has('filter')) {
            $filter = $request->input('filter');
            if (in_array($filter, ['pending', 'approved', 'rejected'])) {
                $query->where('status', $filter);
            }
        }

        $leaves = $query->orderBy('leave_date', 'desc')->get();

        return view('intern.leave.index', compact('leaves'));
    }

    public function destroy(InternLeave $leave)
    {
        abort_if(auth()->user()->role !== 'intern', 403);
        abort_if($leave->leaveType->code === 'IOD', 403); // Cannot delete IOD leave

        $leave->delete();

        return back()->with('success', 'Leave cancelled successfully.');
    }


    /* ================= SUPERVISOR ================= */

    public function index(Request $request)
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        $query = InternLeave::with(['user', 'leaveType']);

        // Apply filter (same logic as intern)
        if ($request->has('filter')) {
            $filter = $request->input('filter');

            if (in_array($filter, ['pending', 'approved', 'rejected'])) {
                $query->where('status', $filter);
            }
        }

        $leaves = $query
            ->orderBy('leave_date', 'desc')
            ->get();

        return view('supervisor.leave.index', compact('leaves'));
    }


    public function approve(InternLeave $leave)
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        $leave->update(['status' => 'approved']);

        // sync attendance
        Attendance::updateOrCreate(
            [
            'user_id' => $leave->user_id,
            'attendance_date' => $leave->leave_date, 
            ],
            [
                'leave_id' => $leave->id,
                'status' => 'on leave',
                'check_in' => null,
                'check_out'=> null,
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
            'interns' => \App\Models\User::where('role', 'intern')->get(),
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

        // If ALL INTERN selected
        if ($request->user_id === 'all') {
            $interns = \App\Models\User::where('role', 'intern')->get();
            foreach ($interns as $intern) {
                $leave = InternLeave::create([
                    'user_id' => $intern->id,
                    'leave_type_id' => $request->leave_type_id,
                    'leave_date' => $request->leave_date,
                    'half_day' => $request->half_day,
                    'reason' => $request->reason,
                    'status' => 'approved', // supervisor-added leave is auto-approved
                ]);

                // sync attendance
                Attendance::updateOrCreate(
                    [
                        'user_id' => $intern->id,
                        'attendance_date' => $request->leave_date,
                    ],
                    [
                        'leave_id' => $leave->id,
                        'status' => 'on leave',
                        'check_in' => null,
                        'check_out' => null,
                        'total_hours' => 0,
                    ]
                );
            }
        } else {
            // Single intern leave
            $leave = InternLeave::create([
                'user_id' => $request->user_id,
                'leave_type_id' => $request->leave_type_id,
                'leave_date' => $request->leave_date,
                'half_day' => $request->half_day,
                'reason' => $request->reason,
                'status' => 'approved', // supervisor-added leave is auto-approved
            ]);

            // sync attendance
            Attendance::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'attendance_date' => $request->leave_date,
                ],
                [
                    'leave_id' => $leave->id,
                    'status' => 'on leave',
                    'check_in' => null,
                    'check_out' => null,
                    'total_hours' => 0,
                ]
            );
        }

        return redirect()
            ->route('supervisor.leave.index')
            ->with('success', 'Leave added successfully.');
    }
    

}
