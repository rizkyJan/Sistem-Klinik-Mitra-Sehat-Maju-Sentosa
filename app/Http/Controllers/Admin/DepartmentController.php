<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $departments = Department::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->withCount([
                'users' => function ($query) {
                    $query->whereIn('role', ['karyawan', 'kabid']);
                }
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.departments.index',
            compact('departments')
        );
    }

    public function create(): View
    {
        return view('admin.departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:departments,name',
                ],

                'description' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
            ],
            [
                'name.required' => 'Nama bidang wajib diisi.',
                'name.unique' => 'Nama bidang sudah tersedia.',
                'name.max' => 'Nama bidang maksimal 255 karakter.',
                'description.max' => 'Deskripsi maksimal 1000 karakter.',
            ]
        );

        $validated['is_active'] = true;

        Department::create($validated);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Bidang berhasil ditambahkan.');
    }

    public function edit(Department $department): View
    {
        return view(
            'admin.departments.edit',
            compact('department')
        );
    }

    public function update(
        Request $request,
        Department $department
    ): RedirectResponse {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('departments', 'name')
                        ->ignore($department),
                ],

                'description' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],

                'is_active' => [
                    'required',
                    'boolean',
                ],
            ],
            [
                'name.required' => 'Nama bidang wajib diisi.',
                'name.unique' => 'Nama bidang sudah tersedia.',
            ]
        );

        $department->update($validated);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Bidang berhasil diperbarui.');
    }

    public function destroy(
        Department $department
    ): RedirectResponse {
        if ($department->users()->exists()) {
            return redirect()
                ->route('admin.departments.index')
                ->with(
                    'error',
                    'Bidang tidak dapat dihapus karena masih digunakan oleh user.'
                );
        }

        $department->delete();

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Bidang berhasil dihapus.');
    }
}
