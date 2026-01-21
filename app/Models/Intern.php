<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intern extends Model
{
    use HasFactory;

    protected $table = 'interns'; // important!
    protected $fillable = ['user_id', 'employee_id', 'report_date', 'end_date', 'intern_duration','status','al_balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusAttribute()
    {
        return $this->end_date >= now() ? 'active' : 'completed';
    }
}
