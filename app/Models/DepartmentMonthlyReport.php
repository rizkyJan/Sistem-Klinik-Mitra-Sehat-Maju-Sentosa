<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentMonthlyReport extends Model
{
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_REVISION = 'revision';
    public const STATUS_VERIFIED = 'verified';

    protected $fillable = [
        'department_id',
        'period',
        'submitted_by',
        'file_path',
        'original_name',
        'mime',
        'size',
        'employee_note',
        'status',
        'revision_note',
        'admin_note',
        'submitted_at',
        'verified_at',
        'verified_by',
        'admin_edited_at',
        'admin_edited_by',
    ];

    protected function casts(): array
    {
        return [
            'period' => 'date',
            'size' => 'integer',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'admin_edited_at' => 'datetime',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_SUBMITTED => 'Menunggu Verifikasi',
            self::STATUS_REVISION => 'Perlu Revisi',
            self::STATUS_VERIFIED => 'Diverifikasi',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? ucfirst($this->status);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function adminEditedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_edited_by');
    }
}
