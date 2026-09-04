<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $admins = User::query()
            ->with('department')
            ->where('role', 'admin')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nip', 'like', '%' . $search . '%')
                        ->orWhere('nik_ktp', 'like', '%' . $search . '%')
                        ->orWhere('nik', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('whatsapp', 'like', '%' . $search . '%')
                        ->orWhere('bank_account_number', 'like', '%' . $search . '%')
                        ->orWhereHas(
                            'department',
                            fn($query) => $query->where('name', 'like', '%' . $search . '%')
                        );
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.admins.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            $this->rules(),
            $this->messages()
        );

        $photoPath = $request->file('formal_photo')
            ->store('admin-photos', 'local');

        try {
            User::create([
                'name' => $validated['name'],
                'nik' => $validated['nip'],
                'nip' => $validated['nip'],
                'nik_ktp' => $validated['nik_ktp'],
                'email' => strtolower($validated['email']),
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
                'role' => 'admin',
                'is_active' => (bool) $validated['is_active'],
                'email_verified_at' => now(),
                'approval_status' => 'approved',
                'approval_rejection_reason' => null,
                'profile_completed_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Data Admin berhasil ditambahkan lengkap.');
    }

    public function edit(User $adminUser): View
    {
        $this->ensureAdmin($adminUser);

        $departments = Department::query()
            ->orderBy('name')
            ->get();

        return view(
            'admin.admins.edit',
            compact('adminUser', 'departments')
        );
    }

    public function update(Request $request, User $adminUser): RedirectResponse
    {
        $this->ensureAdmin($adminUser);

        $validated = $request->validate(
            $this->rules($adminUser),
            $this->messages($adminUser)
        );

        if (
            Auth::id() === $adminUser->id
            && ! (bool) $validated['is_active']
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'is_active' => 'Akun Admin yang sedang digunakan tidak dapat dinonaktifkan.',
                ]);
        }

        $oldPhotoPath = $adminUser->formal_photo_path;
        $newPhotoPath = null;

        if ($request->hasFile('formal_photo')) {
            $newPhotoPath = $request->file('formal_photo')
                ->store('admin-photos', 'local');
        }

        $data = [
            'name' => $validated['name'],
            'nik' => $validated['nip'],
            'nip' => $validated['nip'],
            'nik_ktp' => $validated['nik_ktp'],
            'email' => strtolower($validated['email']),
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
            'role' => 'admin',
            'is_active' => (bool) $validated['is_active'],
            'approval_status' => 'approved',
            'approval_rejection_reason' => null,
            'profile_completed_at' => now(),
        ];

        if ($newPhotoPath) {
            $data['formal_photo_path'] = $newPhotoPath;
        }

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        try {
            $adminUser->update($data);
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
            ->route('admin.admins.index')
            ->with('success', 'Data Admin berhasil diperbarui.');
    }

    public function destroy(User $adminUser): RedirectResponse
    {
        $this->ensureAdmin($adminUser);

        if (Auth::id() === $adminUser->id) {
            return redirect()
                ->route('admin.admins.index')
                ->with('error', 'Akun Admin yang sedang digunakan tidak dapat dihapus.');
        }

        if (User::query()->where('role', 'admin')->count() <= 1) {
            return redirect()
                ->route('admin.admins.index')
                ->with('error', 'Minimal harus ada satu akun Admin di sistem.');
        }

        $photoPath = $adminUser->formal_photo_path;

        try {
            $adminUser->delete();
        } catch (QueryException $exception) {
            report($exception);

            return redirect()
                ->route('admin.admins.index')
                ->with(
                    'error',
                    'Data Admin tidak dapat dihapus karena masih digunakan oleh data lain. Nonaktifkan akun melalui menu Edit.'
                );
        }

        if ($photoPath) {
            Storage::disk('local')->delete($photoPath);
        }

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Data Admin berhasil dihapus.');
    }

    public function photo(User $adminUser): StreamedResponse
    {
        $this->ensureAdmin($adminUser);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_unless(
            $adminUser->formal_photo_path
                && $disk->exists($adminUser->formal_photo_path),
            404
        );

        return $disk->response(
            $adminUser->formal_photo_path,
            basename($adminUser->formal_photo_path),
            [
                'Content-Disposition' => 'inline',
                'Cache-Control' => 'private, max-age=3600',
            ]
        );
    }

    private function rules(?User $adminUser = null): array
    {
        $ignoreId = $adminUser?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'nip' => [
                'required',
                'string',
                'max:50',
                $ignoreId
                    ? Rule::unique('users', 'nip')->ignore($ignoreId)
                    : Rule::unique('users', 'nip'),
                $ignoreId
                    ? Rule::unique('users', 'nik')->ignore($ignoreId)
                    : Rule::unique('users', 'nik'),
            ],
            'nik_ktp' => [
                'required',
                'string',
                'regex:/^[0-9]{16}$/',
                $ignoreId
                    ? Rule::unique('users', 'nik_ktp')->ignore($ignoreId)
                    : Rule::unique('users', 'nik_ktp'),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                $ignoreId
                    ? Rule::unique('users', 'email')->ignore($ignoreId)
                    : Rule::unique('users', 'email'),
            ],
            'whatsapp' => ['required', 'string', 'max:20'],
            'join_date' => ['required', 'date', 'before_or_equal:today'],
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
                Rule::requiredIf($adminUser && blank($adminUser->formal_photo_path)),
                $adminUser ? 'nullable' : 'required',
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
            'bank_account_name' => ['required', 'string', 'max:150'],
            'password' => [
                $adminUser ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'is_active' => ['required', 'boolean'],
        ];
    }

    private function messages(?User $adminUser = null): array
    {
        return [
            'name.required' => 'Nama Admin wajib diisi.',
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
            'formal_photo.required' => $adminUser
                ? 'Pas foto wajib diunggah karena data Admin lama belum memiliki foto.'
                : 'Pas foto formal wajib diunggah.',
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
            'is_active.required' => 'Status Admin wajib dipilih.',
        ];
    }

    private function ensureAdmin(User $user): void
    {
        abort_unless($user->role === 'admin', 404);
    }
}
