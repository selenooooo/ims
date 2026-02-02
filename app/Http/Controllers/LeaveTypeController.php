<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveType;

class LeaveTypeController extends Controller
{
    public function index()
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        $leaveTypes = LeaveType::orderBy('code')->get();

        return view('supervisor.leave.manage-leave', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        $request->validate([
            'code' => 'required|string|max:10|unique:leave_types,code',
            'name' => 'required|string|max:255',
        ]);

        LeaveType::create($request->only('code', 'name'));

        return back()->with('success', 'Leave type added successfully.');
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        $request->validate([
            'code' => 'required|string|max:10|unique:leave_types,code,' . $leaveType->id,
            'name' => 'required|string|max:255',
        ]);

        $leaveType->update($request->only('code', 'name'));

        return back()->with('success', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType)
    {
        abort_if(auth()->user()->role !== 'supervisor', 403);

        if ($leaveType->internLeaves()->count() > 0) {
            return back()->with('error', 'Cannot delete leave type. It is used in existing leave records.');
        }

        $leaveType->delete();

        return back()->with('success', 'Leave type deleted successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        foreach ($request->leaveTypes as $id => $data) {
            LeaveType::where('id', $id)->update([
                'code' => $data['code'],
                'name' => $data['name'],
                'intern_allowed_apply' => $data['intern_allowed_apply'] ?? 0,
                'affects_al_balance' => $data['affects_al_balance'] ?? 0,
            ]);
        }

        return redirect()->back()->with('success', 'Leave types updated successfully.');
    }

}
