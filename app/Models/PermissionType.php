<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermissionType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'policy_days',
        'uses_annual_balance',
        'excess_can_use_annual_leave',
        'requires_doctor_letter',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'policy_days' => 'integer',
            'uses_annual_balance' => 'boolean',
            'excess_can_use_annual_leave' => 'boolean',
            'requires_doctor_letter' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
