<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReimbursementController extends Controller
{
    /**
     * Menampilkan daftar seluruh pengajuan reimburse.
     */
    public function index(Request $request): View
    {
        $this->ensureAdmin();

        $search = trim(
            (string) $request->input('search', '')
        );

        $status = $request->input('status');

        $reimbursements = Reimbursement::query()
            ->with([
                'user.department',
                'reviewer',
            ])
            ->when(
                $status,
                fn($query, $status) =>
                $query->where('status', $status)
            )
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->where(
                                    'code',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'item_name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'merchant_name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'bank_name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'account_number',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'account_holder_name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereHas(
                                    'user',
                                    function ($query) use ($search) {
                                        $query
                                            ->where(
                                                'name',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhere(
                                                'nik',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhere(
                                                'email',
                                                'like',
                                                '%' . $search . '%'
                                            );
                                    }
                                );
                        }
                    );
                }
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $pendingCount = Reimbursement::query()
            ->where(
                'status',
                Reimbursement::STATUS_PENDING
            )
            ->count();

        $approvedCount = Reimbursement::query()
            ->where(
                'status',
                Reimbursement::STATUS_APPROVED
            )
            ->count();

        $rejectedCount = Reimbursement::query()
            ->where(
                'status',
                Reimbursement::STATUS_REJECTED
            )
            ->count();

        $paidThisMonth = (int) Reimbursement::query()
            ->where(
                'status',
                Reimbursement::STATUS_PAID
            )
            ->whereYear(
                'paid_at',
                now()->year
            )
            ->whereMonth(
                'paid_at',
                now()->month
            )
            ->sum('amount');

        return view(
            'admin.reimbursements.index',
            compact(
                'reimbursements',
                'pendingCount',
                'approvedCount',
                'rejectedCount',
                'paidThisMonth'
            )
        );
    }


    /**
     * Menampilkan detail pengajuan reimburse.
     */
    public function show(
        Reimbursement $reimbursement
    ): View {
        $this->ensureAdmin();

        $reimbursement->load([
            'user.department',
            'reviewer',
        ]);

        return view(
            'admin.reimbursements.show',
            compact('reimbursement')
        );
    }


    /**
     * Menyetujui pengajuan reimburse.
     */
    public function approve(
        Request $request,
        Reimbursement $reimbursement
    ): RedirectResponse {
        $this->ensureAdmin();

        $validated = $request->validate(
            [
                'review_note' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]
        );

        try {
            DB::transaction(
                function () use (
                    $reimbursement,
                    $validated
                ) {
                    $locked = Reimbursement::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $reimbursement->id
                        );

                    if (! $locked->isPending()) {
                        throw new \RuntimeException(
                            'Pengajuan ini sudah diproses sebelumnya.'
                        );
                    }

                    $locked->update([
                        'status' =>
                        Reimbursement::STATUS_APPROVED,

                        'review_note' =>
                        $validated['review_note'] ?? null,

                        'reviewed_by' =>
                        Auth::id(),

                        'reviewed_at' =>
                        now(),

                        'paid_at' =>
                        null,
                    ]);
                }
            );
        } catch (\RuntimeException $exception) {
            return back()->with(
                'error',
                $exception->getMessage()
            );
        }

        return redirect()
            ->route(
                'admin.reimbursements.show',
                $reimbursement
            )
            ->with(
                'success',
                'Pengajuan reimburse berhasil disetujui.'
            );
    }


    /**
     * Menolak pengajuan reimburse.
     */
    public function reject(
        Request $request,
        Reimbursement $reimbursement
    ): RedirectResponse {
        $this->ensureAdmin();

        $validated = $request->validate(
            [
                'review_note' => [
                    'required',
                    'string',
                    'max:2000',
                ],
            ],
            [
                'review_note.required' =>
                'Alasan penolakan wajib diisi.',
            ]
        );

        try {
            DB::transaction(
                function () use (
                    $reimbursement,
                    $validated
                ) {
                    $locked = Reimbursement::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $reimbursement->id
                        );

                    if (! $locked->isPending()) {
                        throw new \RuntimeException(
                            'Pengajuan ini sudah diproses sebelumnya.'
                        );
                    }

                    $locked->update([
                        'status' =>
                        Reimbursement::STATUS_REJECTED,

                        'review_note' =>
                        $validated['review_note'],

                        'reviewed_by' =>
                        Auth::id(),

                        'reviewed_at' =>
                        now(),

                        'paid_at' =>
                        null,
                    ]);
                }
            );
        } catch (\RuntimeException $exception) {
            return back()->with(
                'error',
                $exception->getMessage()
            );
        }

        return redirect()
            ->route(
                'admin.reimbursements.show',
                $reimbursement
            )
            ->with(
                'success',
                'Pengajuan reimburse berhasil ditolak.'
            );
    }


    /**
     * Menandai reimburse sudah dibayar.
     */
    public function markPaid(
        Reimbursement $reimbursement
    ): RedirectResponse {
        $this->ensureAdmin();

        try {
            DB::transaction(
                function () use ($reimbursement) {
                    $locked = Reimbursement::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $reimbursement->id
                        );

                    if (! $locked->isApproved()) {
                        throw new \RuntimeException(
                            'Hanya reimburse yang sudah disetujui '
                                . 'yang dapat ditandai sudah dibayar.'
                        );
                    }

                    /*
                     * Pastikan data rekening tujuan transfer lengkap.
                     */
                    if (
                        blank($locked->bank_name)
                        || blank($locked->account_number)
                        || blank($locked->account_holder_name)
                    ) {
                        throw new \RuntimeException(
                            'Data rekening tujuan transfer belum lengkap.'
                        );
                    }

                    $locked->update([
                        'status' =>
                        Reimbursement::STATUS_PAID,

                        'paid_at' =>
                        now(),
                    ]);
                }
            );
        } catch (\RuntimeException $exception) {
            return back()->with(
                'error',
                $exception->getMessage()
            );
        }

        return redirect()
            ->route(
                'admin.reimbursements.show',
                $reimbursement
            )
            ->with(
                'success',
                'Reimburse berhasil ditandai sudah dibayar.'
            );
    }


    /**
     * Menampilkan bukti pembelian.
     */
    public function receipt(
        Reimbursement $reimbursement
    ): StreamedResponse {
        $this->ensureAdmin();

        /**
         * Memberitahu Intelephense bahwa hasil
         * Storage::disk() adalah FilesystemAdapter.
         *
         * Dengan begitu method response()
         * dapat dikenali VS Code.
         *
         * @var FilesystemAdapter $disk
         */
        $disk = Storage::disk('local');

        abort_unless(
            ! empty($reimbursement->receipt_path)
                && $disk->exists(
                    $reimbursement->receipt_path
                ),
            404
        );

        return $disk->response(
            $reimbursement->receipt_path,
            $reimbursement->receipt_original_name,
            [
                'Content-Type' =>
                $reimbursement->receipt_mime
                    ?: 'application/octet-stream',

                'X-Content-Type-Options' =>
                'nosniff',
            ],
            'inline'
        );
    }


    /**
     * Memastikan user yang login adalah admin.
     */
    private function ensureAdmin(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless(
            $user !== null
                && $user->role === 'admin',
            403
        );
    }
}
