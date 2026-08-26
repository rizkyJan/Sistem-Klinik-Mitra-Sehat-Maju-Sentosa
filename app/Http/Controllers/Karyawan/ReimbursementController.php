<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReimbursementController extends Controller
{
    /**
     * Menampilkan daftar reimburse milik karyawan.
     */
    public function index(Request $request): View
    {
        $this->ensureKaryawan();

        /** @var User $user */
        $user = $request->user();

        $status = $request->input('status');

        $reimbursements = $user->reimbursements()
            ->when(
                $status,
                fn($query, $status) => $query->where('status', $status)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $pendingCount = $user->reimbursements()
            ->where('status', Reimbursement::STATUS_PENDING)
            ->count();

        $approvedCount = $user->reimbursements()
            ->where('status', Reimbursement::STATUS_APPROVED)
            ->count();

        $rejectedCount = $user->reimbursements()
            ->where('status', Reimbursement::STATUS_REJECTED)
            ->count();

        $paidTotal = (int) $user->reimbursements()
            ->where('status', Reimbursement::STATUS_PAID)
            ->sum('amount');

        return view(
            'karyawan.reimbursements.index',
            compact(
                'reimbursements',
                'pendingCount',
                'approvedCount',
                'rejectedCount',
                'paidTotal'
            )
        );
    }


    /**
     * Menampilkan form pengajuan reimburse.
     */
    public function create(): View
    {
        $this->ensureKaryawan();

        return view('karyawan.reimbursements.create', [
            'categories' => Reimbursement::CATEGORY_LABELS,
        ]);
    }


    /**
     * Menyimpan pengajuan reimburse baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->ensureKaryawan();

        /** @var User $user */
        $user = $request->user();

        $validated = $this->validateForm(
            $request,
            receiptRequired: true
        );

        $receipt = $request->file('receipt');

        if (! $receipt instanceof UploadedFile) {
            return back()
                ->withInput()
                ->withErrors([
                    'receipt' => 'Bukti pembelian wajib diunggah.',
                ]);
        }

        $receiptPath = $this->storeReceipt(
            $receipt,
            $user->id
        );

        try {
            $reimbursement = Reimbursement::create([
                'user_id' => $user->id,
                'code' => $this->generateCode(),

                'purchase_date' => $validated['purchase_date'],
                'category' => $validated['category'],
                'merchant_name' => $validated['merchant_name'] ?? null,
                'item_name' => $validated['item_name'],
                'purpose' => $validated['purpose'],
                'amount' => $validated['amount'],

                'bank_name' => $validated['bank_name'],
                'account_number' => $validated['account_number'],
                'account_holder_name' => $validated['account_holder_name'],

                'receipt_path' => $receiptPath,
                'receipt_original_name' => $receipt->getClientOriginalName(),
                'receipt_mime' => $receipt->getMimeType(),
                'receipt_size' => $receipt->getSize(),

                'status' => Reimbursement::STATUS_PENDING,
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($receiptPath);

            throw $exception;
        }

        return redirect()
            ->route(
                'karyawan.reimbursements.show',
                $reimbursement
            )
            ->with(
                'success',
                'Pengajuan reimburse berhasil dikirim dan menunggu pemeriksaan admin.'
            );
    }


    /**
     * Menampilkan detail reimburse.
     */
    public function show(
        Reimbursement $reimbursement
    ): View {
        $this->ensureOwner($reimbursement);

        $reimbursement->load('reviewer');

        return view(
            'karyawan.reimbursements.show',
            compact('reimbursement')
        );
    }


    /**
     * Menampilkan form edit reimburse.
     */
    public function edit(
        Reimbursement $reimbursement
    ): View {
        $this->ensureOwner($reimbursement);
        $this->ensurePending($reimbursement);

        return view(
            'karyawan.reimbursements.edit',
            [
                'reimbursement' => $reimbursement,
                'categories' => Reimbursement::CATEGORY_LABELS,
            ]
        );
    }


    /**
     * Memperbarui pengajuan reimburse.
     */
    public function update(
        Request $request,
        Reimbursement $reimbursement
    ): RedirectResponse {
        $this->ensureOwner($reimbursement);
        $this->ensurePending($reimbursement);

        /** @var User $user */
        $user = $request->user();

        $validated = $this->validateForm(
            $request,
            receiptRequired: false
        );

        $oldPath = $reimbursement->receipt_path;

        $newPath = null;

        $receipt = $request->file('receipt');

        if ($receipt instanceof UploadedFile) {
            $newPath = $this->storeReceipt(
                $receipt,
                $user->id
            );
        }

        try {
            $reimbursement->update([
                'purchase_date' => $validated['purchase_date'],
                'category' => $validated['category'],
                'merchant_name' => $validated['merchant_name'] ?? null,
                'item_name' => $validated['item_name'],
                'purpose' => $validated['purpose'],
                'amount' => $validated['amount'],

                'bank_name' => $validated['bank_name'],
                'account_number' => $validated['account_number'],
                'account_holder_name' => $validated['account_holder_name'],

                'receipt_path' => $newPath
                    ?: $reimbursement->receipt_path,

                'receipt_original_name' => $receipt instanceof UploadedFile
                    ? $receipt->getClientOriginalName()
                    : $reimbursement->receipt_original_name,

                'receipt_mime' => $receipt instanceof UploadedFile
                    ? $receipt->getMimeType()
                    : $reimbursement->receipt_mime,

                'receipt_size' => $receipt instanceof UploadedFile
                    ? $receipt->getSize()
                    : $reimbursement->receipt_size,
            ]);
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }

            throw $exception;
        }

        if (
            $newPath
            && $oldPath
            && $oldPath !== $newPath
        ) {
            Storage::disk('local')->delete($oldPath);
        }

        return redirect()
            ->route(
                'karyawan.reimbursements.show',
                $reimbursement
            )
            ->with(
                'success',
                'Pengajuan reimburse berhasil diperbarui.'
            );
    }


    /**
     * Membatalkan pengajuan reimburse.
     */
    public function destroy(
        Reimbursement $reimbursement
    ): RedirectResponse {
        $this->ensureOwner($reimbursement);
        $this->ensurePending($reimbursement);

        $receiptPath = $reimbursement->receipt_path;

        $reimbursement->delete();

        if ($receiptPath) {
            Storage::disk('local')->delete($receiptPath);
        }

        return redirect()
            ->route('karyawan.reimbursements.index')
            ->with(
                'success',
                'Pengajuan reimburse berhasil dibatalkan.'
            );
    }


    /**
     * Menampilkan bukti transaksi.
     */
    public function receipt(
        Reimbursement $reimbursement
    ): StreamedResponse {
        $this->ensureOwner($reimbursement);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_unless(
            ! empty($reimbursement->receipt_path)
                && $disk->exists($reimbursement->receipt_path),
            404
        );

        return $disk->response(
            $reimbursement->receipt_path,
            $reimbursement->receipt_original_name,
            [
                'Content-Type' => $reimbursement->receipt_mime
                    ?: 'application/octet-stream',

                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline'
        );
    }


    /**
     * Validasi form reimburse.
     */
    private function validateForm(
        Request $request,
        bool $receiptRequired
    ): array {
        $this->normalizeAmount($request);
        $this->normalizeAccountNumber($request);

        $receiptRules = $receiptRequired
            ? [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120',
            ]
            : [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120',
            ];

        return $request->validate(
            [
                'purchase_date' => [
                    'required',
                    'date',
                    'before_or_equal:today',
                ],

                'category' => [
                    'required',
                    Rule::in(
                        array_keys(
                            Reimbursement::CATEGORY_LABELS
                        )
                    ),
                ],

                'merchant_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'item_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'purpose' => [
                    'required',
                    'string',
                    'max:2000',
                ],

                'amount' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:999999999999',
                ],

                'bank_name' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'account_number' => [
                    'required',
                    'string',
                    'regex:/^[0-9]{6,30}$/',
                ],

                'account_holder_name' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'receipt' => $receiptRules,
            ],
            [
                'purchase_date.required' =>
                'Tanggal pembelian wajib diisi.',

                'purchase_date.date' =>
                'Format tanggal pembelian tidak valid.',

                'purchase_date.before_or_equal' =>
                'Tanggal pembelian tidak boleh melewati hari ini.',

                'category.required' =>
                'Kategori pembelian wajib dipilih.',

                'category.in' =>
                'Kategori pembelian tidak valid.',

                'item_name.required' =>
                'Nama barang/keperluan wajib diisi.',

                'purpose.required' =>
                'Keterangan penggunaan wajib diisi.',

                'amount.required' =>
                'Nominal reimburse wajib diisi.',

                'amount.integer' =>
                'Nominal reimburse harus berupa angka bulat Rupiah.',

                'amount.min' =>
                'Nominal reimburse minimal Rp1.',

                'amount.max' =>
                'Nominal reimburse terlalu besar.',

                'bank_name.required' =>
                'Nama bank wajib diisi.',

                'account_number.required' =>
                'Nomor rekening wajib diisi.',

                'account_number.regex' =>
                'Nomor rekening harus terdiri dari 6 sampai 30 digit angka.',

                'account_holder_name.required' =>
                'Nama pemilik rekening wajib diisi.',

                'receipt.required' =>
                'Bukti pembelian wajib diunggah.',

                'receipt.file' =>
                'Bukti pembelian harus berupa file.',

                'receipt.mimes' =>
                'Bukti hanya boleh JPG, JPEG, PNG, WEBP, atau PDF.',

                'receipt.max' =>
                'Ukuran bukti maksimal 5 MB.',
            ]
        );
    }


    /**
     * Membersihkan format Rupiah sebelum disimpan.
     *
     * Contoh:
     * 15000  -> 15000
     * 15.000 -> 15000
     * 15 000 -> 15000
     */
    private function normalizeAmount(
        Request $request
    ): void {
        $amount = $request->input('amount');

        if (! is_string($amount)) {
            return;
        }

        $normalized = str_replace(
            ['.', ' '],
            '',
            trim($amount)
        );

        $request->merge([
            'amount' => $normalized,
        ]);
    }


    /**
     * Membersihkan nomor rekening.
     *
     * Tetap STRING agar angka 0 di depan
     * tidak hilang.
     *
     * Contoh:
     * 1234 5678 90 -> 1234567890
     * 1234-5678-90 -> 1234567890
     */
    private function normalizeAccountNumber(
        Request $request
    ): void {
        $accountNumber = $request->input(
            'account_number'
        );

        if (! is_string($accountNumber)) {
            return;
        }

        $normalized = str_replace(
            [' ', '-'],
            '',
            trim($accountNumber)
        );

        $request->merge([
            'account_number' => $normalized,
        ]);
    }


    /**
     * Menyimpan file bukti ke storage local.
     */
    private function storeReceipt(
        UploadedFile $receipt,
        int $userId
    ): string {
        $extension = $receipt->extension();

        $filename =
            (string) Str::uuid()
            . '.'
            . $extension;

        return $receipt->storeAs(
            'reimbursements/' . $userId,
            $filename,
            'local'
        );
    }


    /**
     * Membuat kode reimburse unik.
     */
    private function generateCode(): string
    {
        do {
            $code =
                'RMB-'
                . now()->format('Ymd')
                . '-'
                . Str::upper(
                    Str::random(6)
                );
        } while (
            Reimbursement::query()
            ->where('code', $code)
            ->exists()
        );

        return $code;
    }


    /**
     * Memastikan user adalah karyawan.
     */
    private function ensureKaryawan(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless(
            $user !== null
                && $user->role === 'karyawan',
            403
        );
    }


    /**
     * Memastikan reimburse milik user login.
     */
    private function ensureOwner(
        Reimbursement $reimbursement
    ): void {
        $this->ensureKaryawan();

        abort_unless(
            (int) $reimbursement->user_id
                === (int) Auth::id(),
            403
        );
    }


    /**
     * Memastikan pengajuan masih pending.
     */
    private function ensurePending(
        Reimbursement $reimbursement
    ): void {
        abort_unless(
            $reimbursement->isPending(),
            422,
            'Pengajuan yang sudah diproses admin tidak dapat diubah atau dibatalkan.'
        );
    }
}
