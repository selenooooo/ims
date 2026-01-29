<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = ['code', 'name', 'affects_al_balance', 'intern_allowed_apply',];

    protected $casts = [
        'affects_al_balance' => 'boolean',
        'intern_allowed_apply' => 'boolean',
    ];

    public function internLeaves()
    {
        return $this->hasMany(InternLeave::class, 'leave_type_id');
    }

}
