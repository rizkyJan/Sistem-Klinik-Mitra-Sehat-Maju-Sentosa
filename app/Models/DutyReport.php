<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DutyReport extends Model
{
    protected $fillable = [
        'duty_assignment_id',
        'discussion_summary',
        'result_summary',
        'follow_up',
        'additional_notes',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(DutyAssignment::class, 'duty_assignment_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(DutyReportFile::class);
    }
}
