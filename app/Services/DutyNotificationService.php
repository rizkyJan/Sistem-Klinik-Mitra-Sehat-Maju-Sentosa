<?php

namespace App\Services;

use App\Models\DutyAssignment;
use App\Models\DutyLetter;
use App\Models\User;
use App\Notifications\DutyActivityNotification;
use Illuminate\Support\Facades\Notification;
use Throwable;

class DutyNotificationService
{
    /**
     * Kirim satu notifikasi Surat Dinas kepada pemilik assignment.
     *
     * Notifikasi tidak boleh menggagalkan proses utama Surat Dinas.
     * Jika tabel notifications / proses penyimpanan bermasalah, error tetap
     * dilaporkan ke log tetapi transaksi bisnis yang sudah berhasil tidak
     * dipaksa menjadi gagal pada halaman pengguna.
     */
    public static function notifyAssignment(
        DutyAssignment $assignment,
        string $event,
        string $title,
        string $message
    ): void {
        try {
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
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    /**
     * Kirim notifikasi ke seluruh penerima Surat Dinas.
     */
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

    /**
     * Kirim notifikasi ke seluruh Admin aktif.
     */
    public static function notifyAdmins(
        DutyAssignment $assignment,
        string $event,
        string $title,
        string $message
    ): void {
        try {
            $assignment->loadMissing('dutyLetter');

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
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
