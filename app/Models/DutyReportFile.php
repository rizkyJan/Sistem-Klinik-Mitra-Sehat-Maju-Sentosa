<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DutyReportFile extends Model
{
    protected $fillable = [
        'duty_report_id',
        'file_path',
        'original_name',
        'mime',
        'size',
        'caption',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(DutyReport::class, 'duty_report_id');
    }
}
