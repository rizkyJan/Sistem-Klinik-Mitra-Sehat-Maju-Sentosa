<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KaryawanController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $approvalStatus = $request->input('approval_status');

        $karyawan = User::query()
            ->with('department')
            ->where('role', 'karyawan')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nip', 'like', '%' . $search . '%')
                        ->orWhere('nik', 'like', '%' . $search . '%')
                        ->orWhere('nik_ktp', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('whatsapp', 'like', '%' . $search . '%')
                        ->orWhere('bank_account_number', 'like', '%' . $search . '%')
                        ->orWhereHas('department', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($approvalStatus, function ($query, $approvalStatus) {
                $query->where('approval_status', $approvalStatus);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.karyawan.index', compact(
            'karyawan',
            'search',
            'approvalStatus'
        ));
    }

    public function create(): View
    {
        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.karyawan.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'nip' => [
                    'required',
                    'string',
                    'max:50',
                    'unique:users,nip',
                    Rule::unique('users', 'nik'),
                ],
                'nik_ktp' => [
                    'required',
                    'string',
                    'regex:/^[0-9]{16}$/',
                    'unique:users,nik_ktp',
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],
                'whatsapp' => ['required', 'string', 'max:20'],
                'join_date' => [
                    'required',
                    'date',
                    'before_or_equal:today',
                ],
                'department_id' => [
                    'required',
                    Rule::exists('departments', 'id')
                        ->where(fn($query) => $query->where('is_active', true)),
                ],
                'birth_place' => ['required', 'string', 'max:100'],
                'birth_date' => ['required', 'date', 'before:today'],
                'ktp_address' => ['required', 'string', 'max:3000'],
                'domicile_address' => ['required', 'string', 'max:3000'],
                'blood_type' => ['required', Rule::in(['A', 'B', 'AB', 'O'])],
                'religion' => [
                    'required',
                    Rule::in([
                        'Islam',
                        'Kristen Protestan',
                        'Katolik',
                        'Hindu',
                        'Buddha',
                        'Konghucu',
                        'Kepercayaan',
                    ]),
                ],
                'sip_number' => [
                    'nullable',
                    'required_with:sip_valid_from,sip_valid_until',
                    'string',
                    'max:100',
                ],
                'sip_valid_from' => [
                    'nullable',
                    'required_with:sip_number,sip_valid_until',
                    'date',
                ],
                'sip_valid_until' => [
                    'nullable',
                    'required_with:sip_number,sip_valid_from',
                    'date',
                    'after_or_equal:sip_valid_from',
                ],
                'formal_photo' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'dimensions:ratio=1/1',
                    'max:2048',
                ],
                'bank_account_number' => [
                    'required',
                    'string',
                    'regex:/^[0-9]{8,20}$/',
                ],
                'bank_account_name' => [
                    'required',
                    'string',
                    'max:150',
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                ],
                'is_active' => ['required', 'boolean'],
            ],
            [
                'name.required' => 'Nama Karyawan wajib diisi.',
                'nip.required' => 'NIP / ID Pegawai wajib diisi.',
                'nip.unique' => 'NIP / ID Pegawai sudah digunakan.',
                'nik_ktp.required' => 'NIK KTP wajib diisi.',
                'nik_ktp.regex' => 'NIK KTP harus tepat 16 digit angka.',
                'nik_ktp.unique' => 'NIK KTP sudah digunakan.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan.',
                'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
                'join_date.required' => 'Tanggal mulai kerja wajib diisi.',
                'join_date.before_or_equal' => 'Tanggal mulai kerja tidak boleh melewati hari ini.',
                'department_id.required' => 'Bidang wajib dipilih.',
                'department_id.exists' => 'Bidang yang dipilih tidak tersedia.',
                'birth_place.required' => 'Tempat lahir wajib diisi.',
                'birth_date.required' => 'Tanggal lahir wajib diisi.',
                'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',
                'ktp_address.required' => 'Alamat KTP wajib diisi.',
                'domicile_address.required' => 'Alamat domisili wajib diisi.',
                'blood_type.required' => 'Golongan darah wajib dipilih.',
                'religion.required' => 'Agama wajib dipilih.',
                'sip_number.required_with' => 'Nomor SIP wajib diisi jika masa berlaku SIP diisi.',
                'sip_valid_from.required_with' => 'Tanggal mulai SIP wajib diisi jika data SIP digunakan.',
                'sip_valid_until.required_with' => 'Tanggal berakhir SIP wajib diisi jika data SIP digunakan.',
                'sip_valid_until.after_or_equal' => 'Tanggal berakhir SIP tidak boleh sebelum tanggal mulai SIP.',
                'formal_photo.required' => 'Pas foto formal wajib diunggah.',
                'formal_photo.image' => 'Pas foto harus berupa file gambar.',
                'formal_photo.mimes' => 'Pas foto harus JPG, JPEG, PNG, atau WEBP.',
                'formal_photo.dimensions' => 'Pas foto harus sudah dicrop menjadi rasio 1:1.',
                'formal_photo.max' => 'Ukuran pas foto maksimal 2 MB.',
                'bank_account_number.required' => 'Nomor rekening BSI wajib diisi.',
                'bank_account_number.regex' => 'Nomor rekening BSI harus berupa 8-20 digit angka.',
                'bank_account_name.required' => 'Nama pemilik rekening BSI wajib diisi.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak sama.',
                'is_active.required' => 'Status Karyawan wajib dipilih.',
            ]
        );

        $photoPath = $request->file('formal_photo')
            ->store('employee-photos', 'local');

        try {
            User::create([
                'name' => $validated['name'],
                'nik' => $validated['nip'],
                'nip' => $validated['nip'],
                'nik_ktp' => $validated['nik_ktp'],
                'email' => $validated['email'],
                'whatsapp' => $validated['whatsapp'],
                'join_date' => $validated['join_date'],
                'department_id' => $validated['department_id'],
                'birth_place' => $validated['birth_place'],
                'birth_date' => $validated['birth_date'],
                'ktp_address' => $validated['ktp_address'],
                'domicile_address' => $validated['domicile_address'],
                'blood_type' => $validated['blood_type'],
                'religion' => $validated['religion'],
                'sip_number' => $validated['sip_number'] ?? null,
                'sip_valid_from' => $validated['sip_valid_from'] ?? null,
                'sip_valid_until' => $validated['sip_valid_until'] ?? null,
                'formal_photo_path' => $photoPath,
                'bank_name' => User::BANK_BSI,
                'bank_account_number' => $validated['bank_account_number'],
                'bank_account_name' => $validated['bank_account_name'],
                'password' => Hash::make($validated['password']),
                'role' => 'karyawan',
                'is_active' => (bool) $validated['is_active'],
                'approval_status' => 'approved',
                'approval_rejection_reason' => null,
                'profile_completed_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        return redirect()
            ->route('admin.karyawan.index')
            ->with('success', 'Data Karyawan berhasil ditambahkan.');
    }

    public function edit(User $karyawan): View
    {
        $this->ensureKaryawan($karyawan);

        $departments = Department::query()
            ->orderBy('name')
            ->get();

        return view('admin.karyawan.edit', compact(
            'karyawan',
            'departments'
        ));
    }

    public function update(Request $request, User $karyawan): RedirectResponse
    {
        $this->ensureKaryawan($karyawan);

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'nip' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('users', 'nip')->ignore($karyawan->id),
                    Rule::unique('users', 'nik')->ignore($karyawan->id),
                ],
                'nik_ktp' => [
                    'required',
                    'string',
                    'regex:/^[0-9]{16}$/',
                    Rule::unique('users', 'nik_ktp')->ignore($karyawan->id),
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($karyawan->id),
                ],
                'whatsapp' => ['required', 'string', 'max:20'],
                'join_date' => ['required', 'date', 'before_or_equal:today'],
                'department_id' => ['required', 'exists:departments,id'],
                'birth_place' => ['required', 'string', 'max:100'],
                'birth_date' => ['required', 'date', 'before:today'],
                'ktp_address' => ['required', 'string', 'max:3000'],
                'domicile_address' => ['required', 'string', 'max:3000'],
                'blood_type' => ['required', Rule::in(['A', 'B', 'AB', 'O'])],
                'religion' => [
                    'required',
                    Rule::in([
                        'Islam',
                        'Kristen Protestan',
                        'Katolik',
                        'Hindu',
                        'Buddha',
                        'Konghucu',
                        'Kepercayaan',
                    ]),
                ],
                'sip_number' => [
                    'nullable',
                    'required_with:sip_valid_from,sip_valid_until',
                    'string',
                    'max:100',
                ],
                'sip_valid_from' => [
                    'nullable',
                    'required_with:sip_number,sip_valid_until',
                    'date',
                ],
                'sip_valid_until' => [
                    'nullable',
                    'required_with:sip_number,sip_valid_from',
                    'date',
                    'after_or_equal:sip_valid_from',
                ],
                'formal_photo' => [
                    Rule::requiredIf(blank($karyawan->formal_photo_path)),
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'dimensions:ratio=1/1',
                    'max:2048',
                ],
                'bank_account_number' => [
                    'required',
                    'string',
                    'regex:/^[0-9]{8,20}$/',
                ],
                'bank_account_name' => [
                    'required',
                    'string',
                    'max:150',
                ],
                'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'confirmed',
                ],
                'is_active' => ['required', 'boolean'],
            ],
            [
                'name.required' => 'Nama Karyawan wajib diisi.',
                'nip.required' => 'NIP / ID Pegawai wajib diisi.',
                'nip.unique' => 'NIP / ID Pegawai sudah digunakan.',
                'nik_ktp.required' => 'NIK KTP wajib diisi.',
                'nik_ktp.regex' => 'NIK KTP harus tepat 16 digit angka.',
                'nik_ktp.unique' => 'NIK KTP sudah digunakan.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan.',
                'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
                'join_date.required' => 'Tanggal mulai kerja wajib diisi.',
                'join_date.before_or_equal' => 'Tanggal mulai kerja tidak boleh melewati hari ini.',
                'department_id.required' => 'Bidang wajib dipilih.',
                'department_id.exists' => 'Bidang yang dipilih tidak tersedia.',
                'birth_place.required' => 'Tempat lahir wajib diisi.',
                'birth_date.required' => 'Tanggal lahir wajib diisi.',
                'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',
                'ktp_address.required' => 'Alamat KTP wajib diisi.',
                'domicile_address.required' => 'Alamat domisili wajib diisi.',
                'blood_type.required' => 'Golongan darah wajib dipilih.',
                'religion.required' => 'Agama wajib dipilih.',
                'sip_number.required_with' => 'Nomor SIP wajib diisi jika masa berlaku SIP diisi.',
                'sip_valid_from.required_with' => 'Tanggal mulai SIP wajib diisi jika data SIP digunakan.',
                'sip_valid_until.required_with' => 'Tanggal berakhir SIP wajib diisi jika data SIP digunakan.',
                'sip_valid_until.after_or_equal' => 'Tanggal berakhir SIP tidak boleh sebelum tanggal mulai SIP.',
                'formal_photo.required' => 'Pas foto formal wajib diunggah karena data lama belum memiliki foto.',
                'formal_photo.image' => 'Pas foto harus berupa file gambar.',
                'formal_photo.mimes' => 'Pas foto harus JPG, JPEG, PNG, atau WEBP.',
                'formal_photo.dimensions' => 'Pas foto harus sudah dicrop menjadi rasio 1:1.',
                'formal_photo.max' => 'Ukuran pas foto maksimal 2 MB.',
                'bank_account_number.required' => 'Nomor rekening BSI wajib diisi.',
                'bank_account_number.regex' => 'Nomor rekening BSI harus berupa 8-20 digit angka.',
                'bank_account_name.required' => 'Nama pemilik rekening BSI wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak sama.',
            ]
        );

        $oldPhotoPath = $karyawan->formal_photo_path;
        $newPhotoPath = null;

        if ($request->hasFile('formal_photo')) {
            $newPhotoPath = $request->file('formal_photo')
                ->store('employee-photos', 'local');
        }

        $data = [
            'name' => $validated['name'],
            'nik' => $validated['nip'],
            'nip' => $validated['nip'],
            'nik_ktp' => $validated['nik_ktp'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'],
            'join_date' => $validated['join_date'],
            'department_id' => $validated['department_id'],
            'birth_place' => $validated['birth_place'],
            'birth_date' => $validated['birth_date'],
            'ktp_address' => $validated['ktp_address'],
            'domicile_address' => $validated['domicile_address'],
            'blood_type' => $validated['blood_type'],
            'religion' => $validated['religion'],
            'sip_number' => $validated['sip_number'] ?? null,
            'sip_valid_from' => $validated['sip_valid_from'] ?? null,
            'sip_valid_until' => $validated['sip_valid_until'] ?? null,
            'bank_name' => User::BANK_BSI,
            'bank_account_number' => $validated['bank_account_number'],
            'bank_account_name' => $validated['bank_account_name'],
            'is_active' => (bool) $validated['is_active'],
            'role' => 'karyawan',
        ];

        if ($newPhotoPath) {
            $data['formal_photo_path'] = $newPhotoPath;
        }

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        try {
            $karyawan->update($data);
        } catch (\Throwable $exception) {
            if ($newPhotoPath) {
                Storage::disk('local')->delete($newPhotoPath);
            }
            throw $exception;
        }

        if (
            $newPhotoPath
            && $oldPhotoPath
            && $oldPhotoPath !== $newPhotoPath
        ) {
            Storage::disk('local')->delete($oldPhotoPath);
        }

        return redirect()
            ->route('admin.karyawan.index')
            ->with('success', 'Data Karyawan berhasil diperbarui.');
    }

    public function destroy(User $karyawan): RedirectResponse
    {
        $this->ensureKaryawan($karyawan);

        $leaveRequestCount = $karyawan->leaveRequests()->count();
        $reimbursementCount = $karyawan->reimbursements()->count();

        if ($leaveRequestCount > 0 || $reimbursementCount > 0) {
            $relatedData = [];

            if ($leaveRequestCount > 0) {
                $relatedData[] = $leaveRequestCount . ' riwayat perizinan/cuti';
            }

            if ($reimbursementCount > 0) {
                $relatedData[] = $reimbursementCount . ' riwayat reimbursement';
            }

            return redirect()
                ->route('admin.karyawan.index')
                ->with(
                    'error',
                    'Data Karyawan "' . $karyawan->name . '" tidak dapat dihapus karena masih memiliki '
                        . implode(' dan ', $relatedData)
                        . '. Nonaktifkan akun melalui menu Edit agar riwayat tetap aman.'
                );
        }

        $photoPath = $karyawan->formal_photo_path;

        try {
            $karyawan->delete();
        } catch (QueryException $exception) {
            report($exception);

            return redirect()
                ->route('admin.karyawan.index')
                ->with(
                    'error',
                    'Data Karyawan tidak dapat dihapus karena masih digunakan oleh data lain. Silakan nonaktifkan akun melalui menu Edit.'
                );
        }

        if ($photoPath) {
            Storage::disk('local')->delete($photoPath);
        }

        return redirect()
            ->route('admin.karyawan.index')
            ->with('success', 'Data Karyawan berhasil dihapus.');
    }

    public function approve(User $karyawan): RedirectResponse
    {
        $this->ensureKaryawan($karyawan);

        if (! $karyawan->profile_completed_at) {
            return redirect()
                ->route('admin.karyawan.index')
                ->withErrors([
                    'approval' => 'Data Karyawan belum lengkap dan belum dapat di-ACC.',
                ]);
        }

        if (! $karyawan->department_id) {
            return redirect()
                ->route('admin.karyawan.index')
                ->withErrors([
                    'approval' => 'Karyawan belum memiliki bidang dan belum dapat di-ACC.',
                ]);
        }

        $karyawan->update([
            'approval_status' => 'approved',
            'approval_rejection_reason' => null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.karyawan.index')
            ->with(
                'success',
                'Karyawan ' . $karyawan->name . ' berhasil di-ACC. Akun sekarang sudah aktif.'
            );
    }

    public function reject(Request $request, User $karyawan): RedirectResponse
    {
        $this->ensureKaryawan($karyawan);

        $validated = $request->validate(
            [
                'reason' => ['required', 'string', 'max:1000'],
            ],
            [
                'reason.required' => 'Alasan penolakan wajib diisi.',
            ]
        );

        $karyawan->update([
            'approval_status' => 'rejected',
            'approval_rejection_reason' => $validated['reason'],
            'is_active' => false,
        ]);

        return redirect()
            ->route('admin.karyawan.index')
            ->with(
                'success',
                'Data Karyawan ' . $karyawan->name . ' ditolak dan dikembalikan untuk diperbaiki.'
            );
    }

    public function photo(User $karyawan): StreamedResponse
    {
        $this->ensureKaryawan($karyawan);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_unless(
            $karyawan->formal_photo_path
                && $disk->exists($karyawan->formal_photo_path),
            404
        );

        return $disk->response(
            $karyawan->formal_photo_path,
            basename($karyawan->formal_photo_path),
            [
                'Content-Disposition' => 'inline',
                'Cache-Control' => 'private, max-age=3600',
            ]
        );
    }

    private function ensureKaryawan(User $user): void
    {
        abort_unless($user->role === 'karyawan', 404);
    }
}
