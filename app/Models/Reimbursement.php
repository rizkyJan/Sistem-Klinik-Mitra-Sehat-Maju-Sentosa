<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reimbursement extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PAID = 'paid';

    public const CATEGORY_LABELS = [
        'obat' => 'Obat',
        'alat_medis' => 'Alat Medis',
        'operasional' => 'Operasional',
        'transportasi' => 'Transportasi',
        'konsumsi' => 'Konsumsi',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'user_id',
        'code',
        'purchase_date',
        'category',
        'merchant_name',
        'item_name',
        'purpose',
        'amount',
        'bank_name',
        'account_number',
        'account_holder_name',
        'receipt_path',
        'receipt_original_name',
        'receipt_mime',
        'receipt_size',
        'status',
        'review_note',
        'reviewed_by',
        'reviewed_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'amount' => 'integer',
            'receipt_size' => 'integer',
            'reviewed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_PAID => 'Sudah Dibayar',
            default => ucfirst($this->status),
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORY_LABELS[$this->category]
            ?? ucfirst(str_replace('_', ' ', $this->category));
    }
}
