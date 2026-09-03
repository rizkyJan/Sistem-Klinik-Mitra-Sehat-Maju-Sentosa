<?php

namespace App\Services;

use App\Models\DutyAssignment;
use App\Models\DutyLetter;
use App\Models\User;
use App\Notifications\DutyActivityNotification;
use Illuminate\Support\Facades\Notification;

class DutyNotificationService
{
    public static function notifyAssignment(
        DutyAssignment $assignment,
        string $event,
        string $title,
        string $message
    ): void {
        $assignment->loadMissing(['user', 'dutyLetter']);

        if ($assignment->user) {
            $assignment->user->notify(
                new DutyActivityNotification(
                    $event,
                    $title,
                    $message,
                    $assignment
                )
            );
        }
    }

    public static function notifyLetterAssignees(
        DutyLetter $dutyLetter,
        string $event,
        string $title,
        string $message
    ): void {
        $dutyLetter->loadMissing('assignments.user');

        foreach ($dutyLetter->assignments as $assignment) {
            self::notifyAssignment(
                $assignment,
                $event,
                $title,
                $message
            );
        }
    }

    public static function notifyAdmins(
        DutyAssignment $assignment,
        string $event,
        string $title,
        string $message
    ): void {
        $admins = User::query()
            ->where('role', 'admin')
            ->where('is_active', true)
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send(
            $admins,
            new DutyActivityNotification(
                $event,
                $title,
                $message,
                $assignment
            )
        );
    }
}
