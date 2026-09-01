<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $kabid */
        $kabid = $request->user();

        abort_unless(
            $kabid->role === 'kabid',
            403
        );

        $search = trim(
            (string) $request->input(
                'search',
                ''
            )
        );

        $status = $request->input(
            'status'
        );

        /*
        |--------------------------------------------------------------------------
        | Query dasar anggota
        |--------------------------------------------------------------------------
        |
        | Anggota Kabid adalah:
        | - role = karyawan
        | - sudah disetujui Admin
        | - department_id sama dengan Kabid login
        |
        | Jika Kabid belum memiliki department_id, query sengaja dibuat kosong
        | agar tidak pernah menampilkan karyawan dari department NULL.
        |
        */
        $baseQuery = User::query()
            ->where(
                'role',
                'karyawan'
            )
            ->where(
                'approval_status',
                'approved'
            );

        if ($kabid->department_id) {
            $baseQuery->where(
                'department_id',
                $kabid->department_id
            );
        } else {
            $baseQuery->whereRaw(
                '1 = 0'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */
        $totalMembers = (clone $baseQuery)
            ->count();

        $activeMembers = (clone $baseQuery)
            ->where(
                'is_active',
                true
            )
            ->count();

        $inactiveMembers = (clone $baseQuery)
            ->where(
                'is_active',
                false
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Daftar anggota + filter
        |--------------------------------------------------------------------------
        */
        $members = (clone $baseQuery)
            ->with(
                'department'
            )
            ->when(
                $status === 'active',
                fn($query) =>
                $query->where(
                    'is_active',
                    true
                )
            )
            ->when(
                $status === 'inactive',
                fn($query) =>
                $query->where(
                    'is_active',
                    false
                )
            )
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
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
                                )
                                ->orWhere(
                                    'whatsapp',
                                    'like',
                                    '%' . $search . '%'
                                );
                        }
                    );
                }
            )
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view(
            'kabid.members.index',
            compact(
                'kabid',
                'members',
                'totalMembers',
                'activeMembers',
                'inactiveMembers',
                'search',
                'status'
            )
        );
    }
}
