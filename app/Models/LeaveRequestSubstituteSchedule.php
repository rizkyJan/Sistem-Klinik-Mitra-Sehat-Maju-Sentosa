<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequestSubstituteSchedule extends Model
{
    protected $fillable = [
        'leave_request_id',

        'schedule_date',

        'has_substitute',

        'substitute_name',
        'substitute_whatsapp',
        'substitute_fee_payer',
        'substitute_address',

        'substitute_bank_name',
        'substitute_bank_account_number',
        'substitute_bank_account_holder',

        'schedule_type',

        'work_shift_id',

        'start_time',
        'end_time',
    ];


    protected function casts(): array
    {
        return [
            'schedule_date' => 'date',

            'has_substitute' => 'boolean',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Pengajuan
    |--------------------------------------------------------------------------
    */

    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(
            LeaveRequest::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Shift
    |--------------------------------------------------------------------------
    */

    public function workShift(): BelongsTo
    {
        return $this->belongsTo(
            WorkShift::class
        );
    }
}
