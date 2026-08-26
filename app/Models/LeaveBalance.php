<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'quota_days',
        'used_days',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'quota_days' => 'integer',
            'used_days' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRemainingDaysAttribute(): int
    {
        return max(
            0,
            $this->quota_days - $this->used_days
        );
    }
}
