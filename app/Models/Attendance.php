<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'check_in',
        'check_out',
        'total_hours',
        'status',
        'leave_id',
    ];

    public function leave()
    {
        return $this->belongsTo(InternLeave::class,'leave_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function approvedLeave()
    {
        return $this->belongsTo(InternLeave::class,'leave_id','id')->where('status', 'approved');
    }
}
