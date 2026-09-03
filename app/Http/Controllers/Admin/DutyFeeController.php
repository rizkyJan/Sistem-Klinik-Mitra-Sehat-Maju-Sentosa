<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DutyAssignment;
use App\Models\DutyLetter;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DutyFeeController extends Controller
{
    /**
     * Admin mengonfirmasi bahwa fee dinas untuk satu pegawai
     * sudah benar-benar dibayarkan.
     *
     * Nominal fee tidak disimpan pada modul Surat Dinas.
     */
    public function markPaid(
        Request $request,
        DutyLetter $dutyLetter,
        DutyAssignment $dutyAssignment
    ): RedirectResponse {
        $this->ensureAdmin();
        $this->ensureAssignmentBelongsToLetter(
            $dutyLetter,
            $dutyAssignment
        );

        $validated = $request->validate(
            [
                'fee_payment_note' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
            ],
            [
                'fee_payment_note.max' =>
                'Catatan pembayaran maksimal 1.000 karakter.',
            ]
        );

        $alreadyPaid = false;

        DB::transaction(function () use (
            $dutyAssignment,
            $validated,
            &$alreadyPaid
        ) {
            $locked = DutyAssignment::query()
                ->lockForUpdate()
                ->with('report')
                ->findOrFail($dutyAssignment->id);

            if (
                $locked->report === null
                || $locked->report_status
                !== DutyAssignment::REPORT_VERIFIED
            ) {
                abort(
                    409,
                    'Fee hanya dapat dikonfirmasi setelah laporan dinas diverifikasi.'
                );
            }

            if (
                $locked->fee_status
                === DutyAssignment::FEE_PAID
            ) {
                $alreadyPaid = true;

                return;
            }

            $note = trim(
                (string) ($validated['fee_payment_note'] ?? '')
            );

            $locked->update([
                'fee_status' =>
                DutyAssignment::FEE_PAID,
                'fee_paid_at' => now(),
                'fee_confirmed_by' => Auth::id(),
                'fee_payment_note' =>
                $note !== '' ? $note : null,
            ]);
        });

        if ($alreadyPaid) {
            return redirect()
                ->route(
                    'admin.duty-reports.show',
                    [$dutyLetter, $dutyAssignment]
                )
                ->with(
                    'info',
                    'Fee dinas pegawai tersebut sudah pernah dikonfirmasi dibayar.'
                );
        }

        return redirect()
            ->route(
                'admin.duty-reports.show',
                [$dutyLetter, $dutyAssignment]
            )
            ->with(
                'success',
                'Pembayaran fee dinas berhasil dikonfirmasi.'
            );
    }


    /**
     * Memastikan assignment memang berasal dari surat pada URL.
     */
    private function ensureAssignmentBelongsToLetter(
        DutyLetter $dutyLetter,
        DutyAssignment $dutyAssignment
    ): void {
        abort_unless(
            $dutyAssignment->duty_letter_id === $dutyLetter->id,
            404
        );
    }


    /**
     * Pertahanan tambahan selain middleware role:admin.
     */
    private function ensureAdmin(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless(
            $user !== null && $user->role === 'admin',
            403
        );
    }
}
