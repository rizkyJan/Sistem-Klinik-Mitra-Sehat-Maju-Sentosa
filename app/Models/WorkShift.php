<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkShift extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'is_active',
    ];


    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Jadwal Pengganti
    |--------------------------------------------------------------------------
    */

    public function substituteSchedules(): HasMany
    {
        return $this->hasMany(
            LeaveRequestSubstituteSchedule::class
        );
    }
}
