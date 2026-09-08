<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HearYouFeedback extends Model
{
    use HasFactory;
    protected $table = 'hear_you_feedbacks';
    public const CATEGORY_CRITICISM = 'criticism';
    public const CATEGORY_SUGGESTION = 'suggestion';
    public const CATEGORY_COMPLAINT = 'complaint';
    public const CATEGORY_IDEA = 'idea';
    public const CATEGORY_APPRECIATION = 'appreciation';

    public const STATUS_WAITING_RESPONSE = 'waiting_response';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_UNABLE = 'unable';

    public const EVALUATION_GOOD = 'good';
    public const EVALUATION_LESS_GOOD = 'less_good';
    public const EVALUATION_SAME = 'same';

    protected $fillable = [
        'user_id',
        'period',
        'category',
        'message',
        'admin_response',
        'follow_up_status',
        'responded_by',
        'responded_at',
        'employee_evaluation',
        'evaluation_note',
        'evaluated_at',
    ];

    protected function casts(): array
    {
        return [
            'period' => 'date',
            'responded_at' => 'datetime',
            'evaluated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function scopeForPeriod(Builder $query, string $period): Builder
    {
        return $query->whereDate('period', $period);
    }

    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_CRITICISM => 'Kritik',
            self::CATEGORY_SUGGESTION => 'Saran',
            self::CATEGORY_COMPLAINT => 'Keluhan',
            self::CATEGORY_IDEA => 'Ide / Usulan',
            self::CATEGORY_APPRECIATION => 'Apresiasi',
        ];
    }

    public static function followUpStatusOptions(): array
    {
        return [
            self::STATUS_RECEIVED => 'Diterima',
            self::STATUS_IN_PROGRESS => 'Sedang Diproses',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_UNABLE => 'Tidak Dapat Dilaksanakan',
        ];
    }

    public static function evaluationOptions(): array
    {
        return [
            self::EVALUATION_GOOD => 'Sudah terlaksana dengan baik',
            self::EVALUATION_LESS_GOOD => 'Sudah terlaksana tetapi kurang baik',
            self::EVALUATION_SAME => 'Sama saja / belum ada perubahan',
        ];
    }

    public function categoryLabel(): string
    {
        return self::categoryOptions()[$this->category] ?? ucfirst($this->category);
    }

    public function followUpStatusLabel(): string
    {
        if (! $this->admin_response) {
            return 'Menunggu Tanggapan Admin';
        }

        return self::followUpStatusOptions()[$this->follow_up_status]
            ?? ucfirst(str_replace('_', ' ', $this->follow_up_status));
    }

    public function evaluationLabel(): ?string
    {
        if (! $this->employee_evaluation) {
            return null;
        }

        return self::evaluationOptions()[$this->employee_evaluation]
            ?? ucfirst(str_replace('_', ' ', $this->employee_evaluation));
    }
}
