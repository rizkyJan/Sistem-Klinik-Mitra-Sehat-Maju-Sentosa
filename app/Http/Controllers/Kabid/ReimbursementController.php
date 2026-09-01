<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReimbursementController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureKabid();

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
            'kabid.reimbursements.index',
            compact(
                'reimbursements',
                'pendingCount',
                'approvedCount',
                'rejectedCount',
                'paidTotal'
            )
        );
    }

    public function create(): View
    {
        $this->ensureKabid();

        return view('kabid.reimbursements.create', [
            'categories' => Reimbursement::CATEGORY_LABELS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureKabid();

        /** @var User $user */
        $user = $request->user();

        $validated = $this->validateForm($request, receiptRequired: true);

        $receipt = $request->file('receipt');
        $receiptPath = $this->storeReceipt($receipt, $user->id);

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
            ->route('kabid.reimbursements.show', $reimbursement)
            ->with('success', 'Pengajuan reimburse berhasil dikirim dan menunggu pemeriksaan admin.');
    }

    public function show(Reimbursement $reimbursement): View
    {
        $this->ensureOwner($reimbursement);

        $reimbursement->load('reviewer');

        return view(
            'kabid.reimbursements.show',
            compact('reimbursement')
        );
    }

    public function edit(Reimbursement $reimbursement): View
    {
        $this->ensureOwner($reimbursement);
        $this->ensurePending($reimbursement);

        return view('kabid.reimbursements.edit', [
            'reimbursement' => $reimbursement,
            'categories' => Reimbursement::CATEGORY_LABELS,
        ]);
    }

    public function update(
        Request $request,
        Reimbursement $reimbursement
    ): RedirectResponse {
        $this->ensureOwner($reimbursement);
        $this->ensurePending($reimbursement);

        /** @var User $user */
        $user = $request->user();

        $validated = $this->validateForm($request, receiptRequired: false);

        $oldPath = $reimbursement->receipt_path;
        $newPath = null;
        $receipt = $request->file('receipt');

        if ($receipt) {
            $newPath = $this->storeReceipt($receipt, $user->id);
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
                'receipt_path' => $newPath ?: $reimbursement->receipt_path,
                'receipt_original_name' => $receipt
                    ? $receipt->getClientOriginalName()
                    : $reimbursement->receipt_original_name,
                'receipt_mime' => $receipt
                    ? $receipt->getMimeType()
                    : $reimbursement->receipt_mime,
                'receipt_size' => $receipt
                    ? $receipt->getSize()
                    : $reimbursement->receipt_size,
            ]);
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }

            throw $exception;
        }

        if ($newPath && $oldPath !== $newPath) {
            Storage::disk('local')->delete($oldPath);
        }

        return redirect()
            ->route('kabid.reimbursements.show', $reimbursement)
            ->with('success', 'Pengajuan reimburse berhasil diperbarui.');
    }

    public function destroy(Reimbursement $reimbursement): RedirectResponse
    {
        $this->ensureOwner($reimbursement);
        $this->ensurePending($reimbursement);

        $receiptPath = $reimbursement->receipt_path;

        $reimbursement->delete();

        Storage::disk('local')->delete($receiptPath);

        return redirect()
            ->route('kabid.reimbursements.index')
            ->with('success', 'Pengajuan reimburse berhasil dibatalkan.');
    }

    public function receipt(Reimbursement $reimbursement): StreamedResponse
    {
        $this->ensureOwner($reimbursement);

        /**
         * Storage::disk() secara runtime mengembalikan FilesystemAdapter,
         * tetapi Intelephense kadang hanya membaca interface Filesystem
         * sehingga method response() dianggap tidak ada.
         *
         * Type hint ini membuat Intelephense mengenali method response()
         * tanpa mengubah logika penyimpanan file.
         *
         * @var FilesystemAdapter $disk
         */
        $disk = Storage::disk('local');

        abort_unless(
            $disk->exists($reimbursement->receipt_path),
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

    private function validateForm(
        Request $request,
        bool $receiptRequired
    ): array {
        $this->normalizeAmount($request);
        $this->normalizeAccountNumber($request);

        $receiptRules = $receiptRequired
            ? ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120']
            : ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'];

        return $request->validate(
            [
                'purchase_date' => [
                    'required',
                    'date',
                    'before_or_equal:today',
                ],
                'category' => [
                    'required',
                    Rule::in(array_keys(Reimbursement::CATEGORY_LABELS)),
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
                'purchase_date.required' => 'Tanggal pembelian wajib diisi.',
                'purchase_date.before_or_equal' => 'Tanggal pembelian tidak boleh melewati hari ini.',
                'category.required' => 'Kategori pembelian wajib dipilih.',
                'category.in' => 'Kategori pembelian tidak valid.',
                'item_name.required' => 'Nama barang/keperluan wajib diisi.',
                'purpose.required' => 'Keterangan penggunaan wajib diisi.',
                'amount.required' => 'Nominal reimburse wajib diisi.',
                'amount.integer' => 'Nominal reimburse harus berupa angka bulat Rupiah.',
                'amount.min' => 'Nominal reimburse minimal Rp1.',
                'bank_name.required' => 'Nama bank wajib diisi.',
                'account_number.required' => 'Nomor rekening wajib diisi.',
                'account_number.regex' => 'Nomor rekening harus 6 sampai 30 digit angka.',
                'account_holder_name.required' => 'Nama pemilik rekening wajib diisi.',
                'receipt.required' => 'Bukti pembelian wajib diunggah.',
                'receipt.mimes' => 'Bukti hanya boleh JPG, JPEG, PNG, WEBP, atau PDF.',
                'receipt.max' => 'Ukuran bukti maksimal 5 MB.',
            ]
        );
    }

    private function normalizeAmount(Request $request): void
    {
        $amount = $request->input('amount');

        if (! is_string($amount)) {
            return;
        }

        /*
         * Format Rupiah yang diterima:
         * 15000
         * 15.000
         * 15 000
         *
         * Hanya pemisah ribuan yang dibersihkan.
         * Karakter lain tetap akan ditolak oleh validasi integer.
         */
        $normalized = str_replace(['.', ' '], '', trim($amount));

        $request->merge([
            'amount' => $normalized,
        ]);
    }


    private function normalizeAccountNumber(Request $request): void
    {
        $accountNumber = $request->input('account_number');

        if (! is_string($accountNumber)) {
            return;
        }

        /*
         * Nomor rekening tetap disimpan sebagai STRING.
         * Ini penting agar angka nol di depan tidak hilang.
         *
         * Spasi dan tanda "-" dibersihkan agar user boleh mengetik:
         * 1234 5678 90
         * 1234-5678-90
         */
        $normalized = str_replace(
            [' ', '-'],
            '',
            trim($accountNumber)
        );

        $request->merge([
            'account_number' => $normalized,
        ]);
    }


    private function storeReceipt($receipt, int $userId): string
    {
        $filename = (string) Str::uuid() . '.' . $receipt->extension();

        return $receipt->storeAs(
            'reimbursements/' . $userId,
            $filename,
            'local'
        );
    }

    private function generateCode(): string
    {
        do {
            $code = 'RMB-'
                . now()->format('Ymd')
                . '-'
                . Str::upper(Str::random(6));
        } while (
            Reimbursement::query()
            ->where('code', $code)
            ->exists()
        );

        return $code;
    }

    private function ensureKabid(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        /*
         * Area reimburse ini khusus Kabid.
         * Middleware route juga sudah memakai role:kabid,
         * tetapi pengecekan di controller tetap dipertahankan
         * sebagai proteksi berlapis.
         */
        abort_unless(
            $user !== null && $user->role === 'kabid',
            403
        );
    }

    private function ensureOwner(Reimbursement $reimbursement): void
    {
        $this->ensureKabid();

        abort_unless(
            $reimbursement->user_id === Auth::id(),
            403
        );
    }

    private function ensurePending(Reimbursement $reimbursement): void
    {
        abort_unless(
            $reimbursement->isPending(),
            422,
            'Pengajuan yang sudah diproses admin tidak dapat diubah atau dibatalkan.'
        );
    }
}
