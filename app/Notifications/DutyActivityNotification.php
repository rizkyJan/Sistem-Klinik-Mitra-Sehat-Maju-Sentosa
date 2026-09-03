<?php

namespace App\Notifications;

use App\Models\DutyAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DutyActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $event,
        private readonly string $title,
        private readonly string $message,
        private readonly DutyAssignment $assignment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->assignment->loadMissing('dutyLetter');

        return [
            'module' => 'duty',
            'event' => $this->event,
            'title' => $this->title,
            'message' => $this->message,
            'duty_letter_id' => $this->assignment->duty_letter_id,
            'duty_assignment_id' => $this->assignment->id,
            'event_date' => $this->assignment->dutyLetter?->event_date?->format('Y-m-d'),
            'created_at_label' => now()->format('d/m/Y H:i'),
        ];
    }
}
