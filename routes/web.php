<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\InternLeave;
use App\Http\Controllers\LeaveTypeController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        if (auth()->user()->isSupervisor()) {
            return redirect()->route('supervisor.dashboard');
        }
        return app(AttendanceController::class)->dashboard();
    })->name('dashboard');

    // Attendance (Intern)
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password.change');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Intern Leave Routes
    Route::middleware('auth')->prefix('intern/leave')->name('intern.leave.')->group(function () {

        // Show all leaves for the logged-in intern
        Route::get('/', [LeaveController::class, 'internIndex'])->name('index');

        // Show form to request leave
        Route::get('/create', [LeaveController::class, 'create'])->name('create');

        // Store new leave request
        Route::post('/', [LeaveController::class, 'store'])->name('store');

        // Delete Leave
        Route::delete('/{leave}', [LeaveController::class, 'destroy'])->name('destroy');
    });

    // Supervisor Routes
    Route::middleware('can:isSupervisor')
        ->prefix('supervisor')
        ->name('supervisor.')
        ->group(function () {

        Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');

        // Interns
        Route::get('/interns', [SupervisorController::class, 'index'])->name('interns.index');
        Route::get('/interns/create', [SupervisorController::class, 'create'])->name('interns.create');
        Route::post('/interns', [SupervisorController::class, 'store'])->name('interns.store');
        Route::get('/interns/{user}', [SupervisorController::class, 'showIntern'])->name('interns.show');

        // update intern details (EDIT)
        Route::put('/interns/{employee_id}', [SupervisorController::class, 'update'])->name('interns.update');

        // delete intern (DELETE)
        Route::delete('/interns/{user}', [SupervisorController::class, 'destroy'])->name('interns.destroy');

        // Attendance (Supervisor)
        Route::get('/attendance', [SupervisorController::class, 'attendance'])->name('attendance.index');
        // Route::get('attendance/update',[SupervisorController::class, 'updateAttendance'])->name('attendance.update');
        Route::patch('/attendance/{attendance}/update', [SupervisorController::class, 'updateAttendance'])->name('attendance.update');
        Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

        // Supervisor Profile
        Route::get('/profile', [SupervisorController::class, 'profile'])->name('profile');

        // Leave Module
        Route::get('/leave', [LeaveController::class, 'index'])->name('leave.index');
        Route::post('/leave/{leave}/approve', [LeaveController::class, 'approve'])->name('leave.approve');
        Route::post('/leave/{leave}/reject', [LeaveController::class, 'reject'])->name('leave.reject');
        Route::get('/leave/create', [LeaveController::class, 'createSupervisor'])->name('leave.create');
        Route::post('/leave', [LeaveController::class, 'storeSupervisor'])->name('leave.store');
        Route::delete('/leave/{leave}', [LeaveController::class, 'destroyBySupervisor'])->name('leave.destroy');

        // Leave Type Management
        Route::get('/leave-type', [LeaveTypeController::class, 'index'])->name('leave.leaveType');
        Route::post('/leave-type', [LeaveTypeController::class, 'store'])->name('leaveType.store');
        Route::put('/leave-type/{leaveType}', [LeaveTypeController::class, 'update'])->name('leaveType.update');
        Route::delete('/leave-type/{leaveType}', [LeaveTypeController::class, 'destroy'])->name('leaveType.destroy');
        

    });
});
