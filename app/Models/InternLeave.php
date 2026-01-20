<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternLeave extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'leave_date',
        'half_day',
        'leave_days',
        'status',
        'reason'
    ];

    protected $casts = [
        'leave_date' => 'datetime',
        'leave_days' => 'decimal:1'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class, 'leave_id');
    }
}
