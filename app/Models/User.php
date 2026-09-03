<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    /*
     * `nik` adalah kolom LEGACY sementara.
     *
     * NIP / ID Pegawai => `nip`
     * NIK KTP          => `nik_ktp`
     */
    'nik',
    'nip',
    'nik_ktp',

    'name',
    'email',
    'whatsapp',
    'join_date',

    'birth_place',
    'birth_date',
    'ktp_address',
    'domicile_address',
    'blood_type',
    'religion',

    'sip_number',
    'sip_valid_from',
    'sip_valid_until',

    'formal_photo_path',

    'bank_name',
    'bank_account_number',
    'bank_account_name',

    'password',
    'role',
    'is_active',
    'department_id',

    'google_id',
    'google_avatar',

    'approval_status',
    'approval_rejection_reason',
    'profile_completed_at',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const BANK_BSI = 'Bank Syariah Indonesia (BSI)';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'join_date' => 'date',
            'birth_date' => 'date',
            'sip_valid_from' => 'date',
            'sip_valid_until' => 'date',
            'profile_completed_at' => 'datetime',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function approvedLeaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'approved_by');
    }

    public function reimbursements(): HasMany
    {
        return $this->hasMany(Reimbursement::class);
    }

    public function reviewedReimbursements(): HasMany
    {
        return $this->hasMany(Reimbursement::class, 'reviewed_by');
    }

    public function createdDutyLetters(): HasMany
    {
        return $this->hasMany(DutyLetter::class, 'created_by');
    }

    public function dutyAssignments(): HasMany
    {
        return $this->hasMany(DutyAssignment::class, 'user_id');
    }

    public function verifiedDutyAssignments(): HasMany
    {
        return $this->hasMany(DutyAssignment::class, 'report_verified_by');
    }

    public function feeConfirmedDutyAssignments(): HasMany
    {
        return $this->hasMany(DutyAssignment::class, 'fee_confirmed_by');
    }

    public function profileUpdateRequests(): HasMany
    {
        return $this->hasMany(EmployeeProfileUpdateRequest::class, 'user_id');
    }

    public function reviewedProfileUpdateRequests(): HasMany
    {
        return $this->hasMany(EmployeeProfileUpdateRequest::class, 'reviewed_by');
    }

    public function pendingProfileUpdateRequests(): HasMany
    {
        return $this->hasMany(EmployeeProfileUpdateRequest::class, 'user_id')
            ->where('status', EmployeeProfileUpdateRequest::STATUS_PENDING);
    }
}
