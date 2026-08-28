<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveRequest extends Model
{
    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | Pengaju
        |--------------------------------------------------------------------------
        */

        'user_id',
        'permission_type_id',
        'leave_balance_id',


        /*
        |--------------------------------------------------------------------------
        | Periode
        |--------------------------------------------------------------------------
        */

        'start_date',
        'end_date',
        'total_days',

        'reason',


        /*
        |--------------------------------------------------------------------------
        | Kebijakan
        |--------------------------------------------------------------------------
        */

        'policy_covered_days',
        'excess_days',
        'excess_handling',
        'annual_leave_deducted_days',


        /*
        |--------------------------------------------------------------------------
        | Dokumen
        |--------------------------------------------------------------------------
        */

        'supporting_document',


        /*
        |--------------------------------------------------------------------------
        | Melahirkan
        |--------------------------------------------------------------------------
        */

        'expected_delivery_date',
        'maternity_salary_status',


        /*
        |--------------------------------------------------------------------------
        | Pengganti
        |--------------------------------------------------------------------------
        |
        | Data ORANG tetap di leave_requests.
        |
        | Jadwal per tanggal dipindahkan ke:
        | leave_request_substitute_schedules.
        |
        */

        'has_substitute',

        'substitute_name',
        'substitute_whatsapp',
        'substitute_address',

        'substitute_bank_name',
        'substitute_bank_account_number',
        'substitute_bank_account_holder',


        /*
        |--------------------------------------------------------------------------
        | Approval
        |--------------------------------------------------------------------------
        */

        'status',

        'approved_by',
        'approved_at',

        'rejected_at',
        'rejection_reason',

        'unpaid_days',

        'self_replacement_days',
        'self_replacement_consent',
        'self_replacement_consent_at',

        'salary_deduction_consent',
        'salary_deduction_consent_at',
    ];


    protected function casts(): array
    {
        return [
            'start_date' => 'date',

            'end_date' => 'date',

            'expected_delivery_date' => 'date',

            'total_days' => 'integer',

            'policy_covered_days' => 'integer',

            'excess_days' => 'integer',

            'annual_leave_deducted_days' => 'integer',

            'has_substitute' => 'boolean',

            'approved_at' => 'datetime',

            'rejected_at' => 'datetime',
            'unpaid_days' => 'integer',

            'self_replacement_days' => 'integer',
            'self_replacement_consent' => 'boolean',
            'self_replacement_consent_at' => 'datetime',

            'salary_deduction_consent' => 'boolean',

            'salary_deduction_consent_at' => 'datetime',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Karyawan
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Jenis Perizinan
    |--------------------------------------------------------------------------
    */

    public function permissionType(): BelongsTo
    {
        return $this->belongsTo(
            PermissionType::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Saldo Cuti Tahunan
    |--------------------------------------------------------------------------
    */

    public function leaveBalance(): BelongsTo
    {
        return $this->belongsTo(
            LeaveBalance::class
        );
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
        )->orderBy(
            'schedule_date'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Approver
    |--------------------------------------------------------------------------
    */

    public function approver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }
}
