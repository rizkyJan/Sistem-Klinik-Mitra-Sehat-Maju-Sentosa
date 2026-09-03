<?php

namespace App\Http\Controllers;

use App\Models\DutyAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function open(
        Request $request,
        DatabaseNotification $notification
    ): RedirectResponse {
        $user = $request->user();

        abort_unless($user !== null, 403);
        abort_unless(
            (string) $notification->notifiable_id === (string) $user->id
                && $notification->notifiable_type === $user::class,
            403
        );

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        $data = $notification->data;

        if (($data['module'] ?? null) !== 'duty') {
            return redirect()->route($user->role . '.dashboard');
        }

        $event = (string) ($data['event'] ?? '');
        $assignmentId = (int) ($data['duty_assignment_id'] ?? 0);

        $assignment = DutyAssignment::query()
            ->with('dutyLetter')
            ->find($assignmentId);

        if (! $assignment || ! $assignment->dutyLetter) {
            if ($event === 'duty_removed') {
                return redirect()
                    ->route($user->role . '.dashboard')
                    ->with('info', 'Penugasan Surat Dinas tersebut sudah dihapus dari akun Anda.');
            }

            return redirect()
                ->route($user->role . '.dashboard')
                ->with('warning', 'Data Surat Dinas pada notifikasi sudah tidak tersedia.');
        }

        if ($user->role === 'admin') {
            if (in_array($event, ['report_submitted', 'report_resubmitted'], true)) {
                return redirect()->route(
                    'admin.duty-reports.show',
                    [$assignment->dutyLetter, $assignment]
                );
            }

            return redirect()->route(
                'admin.duty-letters.show',
                $assignment->dutyLetter
            );
        }

        if (in_array($user->role, ['karyawan', 'kabid'], true)) {
            abort_unless($assignment->user_id === $user->id, 403);

            return redirect()->route(
                $user->role . '.duty-letters.show',
                $assignment
            );
        }

        abort(403);
    }

    public function readAll(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user !== null, 403);

        $user->unreadNotifications
            ->filter(
                fn(DatabaseNotification $notification) => ($notification->data['module'] ?? null) === 'duty'
            )
            ->each
            ->markAsRead();

        return back()->with(
            'success',
            'Semua notifikasi Surat Dinas sudah ditandai dibaca.'
        );
    }
}
