<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DutyLetter extends Model
{
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'letter_number',
        'title',
        'organizer',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'location_name',
        'location_address',
        'maps_url',
        'letter_path',
        'letter_original_name',
        'letter_mime',
        'letter_size',
        'status',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'letter_size' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(DutyAssignment::class);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => 'Diterbitkan',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }
}
