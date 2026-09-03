<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DutyAssignment extends Model
{
    public const REPORT_PENDING = 'pending';
    public const REPORT_SUBMITTED = 'submitted';
    public const REPORT_REVISION = 'revision';
    public const REPORT_VERIFIED = 'verified';

    public const FEE_UNPAID = 'unpaid';
    public const FEE_PAID = 'paid';

    protected $fillable = [
        'duty_letter_id',
        'user_id',
        'assignee_name',
        'assignee_role',
        'assignee_department',
        'assigned_at',
        'report_status',
        'report_submitted_at',
        'report_verified_at',
        'report_verified_by',
        'revision_note',
        'fee_status',
        'fee_paid_at',
        'fee_confirmed_by',
        'fee_payment_note',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'report_submitted_at' => 'datetime',
            'report_verified_at' => 'datetime',
            'fee_paid_at' => 'datetime',
        ];
    }

    public function dutyLetter(): BelongsTo
    {
        return $this->belongsTo(DutyLetter::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(DutyReport::class);
    }

    public function reportVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'report_verified_by');
    }

    public function feeConfirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fee_confirmed_by');
    }

    public function isReportPending(): bool
    {
        return $this->report_status === self::REPORT_PENDING;
    }

    public function isReportSubmitted(): bool
    {
        return $this->report_status === self::REPORT_SUBMITTED;
    }

    public function needsRevision(): bool
    {
        return $this->report_status === self::REPORT_REVISION;
    }

    public function isReportVerified(): bool
    {
        return $this->report_status === self::REPORT_VERIFIED;
    }

    public function isFeePaid(): bool
    {
        return $this->fee_status === self::FEE_PAID;
    }

    public function getReportStatusLabelAttribute(): string
    {
        return match ($this->report_status) {
            self::REPORT_PENDING => 'Belum Ada Laporan',
            self::REPORT_SUBMITTED => 'Menunggu Verifikasi',
            self::REPORT_REVISION => 'Perlu Perbaikan',
            self::REPORT_VERIFIED => 'Diverifikasi',
            default => ucfirst($this->report_status),
        };
    }

    public function getFeeStatusLabelAttribute(): string
    {
        return match ($this->fee_status) {
            self::FEE_UNPAID => 'Belum Dibayar',
            self::FEE_PAID => 'Sudah Dibayar',
            default => ucfirst($this->fee_status),
        };
    }
}
