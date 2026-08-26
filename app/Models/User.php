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
    'nik',
    'name',
    'email',
    'whatsapp',
    'join_date',

    'password',

    'role',
    'is_active',

    'department_id',

    'google_id',
    'google_avatar',

    'approval_status',
    'approval_rejection_reason',
    'profile_completed_at',
    'google_id',
    'google_avatar',
    'approval_status',
    'approval_rejection_reason',
    'profile_completed_at'
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',

            'password' => 'hashed',

            'is_active' => 'boolean',

            'join_date' => 'date',

            'profile_completed_at' => 'datetime',
        ];
    }


    public function department(): BelongsTo
    {
        return $this->belongsTo(
            Department::class
        );
    }


    public function leaveBalances(): HasMany
    {
        return $this->hasMany(
            LeaveBalance::class
        );
    }


    public function leaveRequests(): HasMany
    {
        return $this->hasMany(
            LeaveRequest::class
        );
    }


    public function approvedLeaveRequests(): HasMany
    {
        return $this->hasMany(
            LeaveRequest::class,
            'approved_by'
        );
    }
}
