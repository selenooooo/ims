<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    /**
     * Scope to exclude leave types that should not appear in dropdowns.
     */
    public function scopeSelectable($query)
    {
        return $query->where('code', '<>', 'IOD'); // exclude Intern Off Day
    }
}
