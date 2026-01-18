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
            'code' => 'required|string|max:10|unique:leave_types,code,' . $leaveType->id,
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

        // Optional: prevent deletion of IOD
        if ($leaveType->code === 'IOD') {
            return back()->with('error', 'Intern Off Day cannot be deleted.');
        }

        $leaveType->delete();

        return back()->with('success', 'Leave type deleted successfully.');
    }
}
