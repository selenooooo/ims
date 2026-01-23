<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\InternLeave;
use Carbon\Carbon;

class MarkAbsentIntern extends Command
{
    protected $signature = 'attendance:mark-absent';
    protected $description = 'Automatically mark interns absent if no check-in and no approved leave';

    public function handle()
    {
        $today = Carbon::today();

        // Ignore weekend
        if ($today->isWeekend()) {
            $this->info('Weekend detected, no action taken.');
            return 0;
        }

        $interns = User::where('role', 'intern')
            ->whereHas('intern', function ($q) {
                $q->where('status', 'active');
            })
            ->get();

        foreach ($interns as $user) {

            $attendanceExists = Attendance::where('user_id', $user->id)
                ->whereDate('attendance_date', $today)
                ->exists();

            if ($attendanceExists) {
                continue;
            }

            $hasLeave = InternLeave::where('user_id', $user->id)
                ->whereDate('leave_date', $today)
                ->where('status', 'approved')
                ->exists();

            if ($hasLeave) {
                continue;
            }

            Attendance::create([
                'user_id' => $user->id,
                'attendance_date' => $today,
                'status' => 'absent',
                'check_in' => null,
                'check_out' => null,
                'total_hours' => 0
            ]);
        }

        return 0;
    }
}
