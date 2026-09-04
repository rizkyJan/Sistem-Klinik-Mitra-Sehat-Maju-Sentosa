@extends('layouts.admin')

@section('title', 'Data Admin')
@section('page-title', 'Data Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Admin</h1>
            <p class="mt-1 text-sm text-slate-500">
                Kelola identitas, biodata, bidang, dan status Administrator SIMI-MS.
            </p>
        </div>

        <a
            href="{{ route('admin.admins.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
            + Tambah Admin
        </a>
    </div>

    <x-toast-notification />

    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <ul class="list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5">
            <form method="GET" class="flex flex-col gap-3 sm:flex-row">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, NIP, NIK KTP, email, bidang..."
                    class="w-full max-w-xl rounded-lg border-slate-300">

                <button class="rounded-lg bg-slate-800 px-5 py-2.5 text-sm font-medium text-white">
                    Cari
                </button>

                @if(request('search'))
                <a
                    href="{{ route('admin.admins.index') }}"
                    class="rounded-lg border border-slate-300 px-5 py-2.5 text-center text-sm text-slate-600">
                    Reset
                </a>
                @endif
            </form>
        </div>

        {{-- MOBILE --}}
        <div class="divide-y divide-slate-200 md:hidden">
            @forelse($admins as $item)
            <div class="p-4">
                <div class="flex items-start gap-3">
                    @if($item->formal_photo_path)
                    <img
                        src="{{ route('admin.admins.photo', $item) }}"
                        alt="Foto {{ $item->name }}"
                        class="h-12 w-12 shrink-0 rounded-full border border-slate-200 object-cover">
                    @else
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-violet-100 font-semibold text-violet-700">
                        {{ strtoupper(substr($item->name, 0, 1)) }}
                    </div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-slate-800">{{ $item->name }}</p>
                            @if(auth()->id() === $item->id)
                            <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700">Akun Anda</span>
                            @endif
                        </div>
                        <p class="break-all text-xs text-slate-500">{{ $item->email }}</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3 text-sm">
                    <div>
                        <p class="text-xs text-slate-400">NIP</p>
                        <p class="mt-1 font-medium text-slate-700">{{ $item->nip ?? $item->nik ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Bidang</p>
                        <p class="mt-1 font-medium text-slate-700">{{ $item->department?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Mulai Kerja</p>
                        <p class="mt-1 font-medium text-slate-700">{{ $item->join_date?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">WhatsApp</p>
                        <p class="mt-1 font-medium text-slate-700">{{ $item->whatsapp ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-3">
                    @if($item->is_active)
                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                    @else
                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Nonaktif</span>
                    @endif
                </div>

                <div class="mt-4 flex gap-2">
                    <a
                        href="{{ route('admin.admins.edit', $item) }}"
                        class="flex-1 rounded-lg bg-amber-50 px-3 py-2.5 text-center text-sm font-medium text-amber-700 hover:bg-amber-100">
                        Edit
                    </a>

                    @if(auth()->id() !== $item->id)
                    <form
                        action="{{ route('admin.admins.destroy', $item) }}"
                        method="POST"
                        class="flex-1"
                        data-confirm
                        data-confirm-tone="danger"
                        data-confirm-title="Hapus Admin?"
                        data-confirm-message="Akun Admin {{ $item->name }} akan dihapus."
                        data-confirm-button="Ya, Hapus">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-lg bg-red-50 px-3 py-2.5 text-sm font-medium text-red-700 hover:bg-red-100">
                            Hapus
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-10 text-center text-sm text-slate-500">Data Admin tidak ditemukan.</div>
            @endforelse
        </div>

        {{-- DESKTOP --}}
        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">No</th>
                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">Admin</th>
                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">NIP</th>
                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">Bidang</th>
                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">Mulai Kerja</th>
                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">WhatsApp</th>
                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">Status</th>
                        <th class="px-6 py-3 text-right text-xs uppercase text-slate-500">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @forelse($admins as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $admins->firstItem() + $loop->index }}</td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($item->formal_photo_path)
                                <img
                                    src="{{ route('admin.admins.photo', $item) }}"
                                    alt="Foto {{ $item->name }}"
                                    class="h-10 w-10 shrink-0 rounded-full border border-slate-200 object-cover">
                                @else
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-violet-100 font-semibold text-violet-700">
                                    {{ strtoupper(substr($item->name, 0, 1)) }}
                                </div>
                                @endif

                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-slate-800">{{ $item->name }}</p>
                                        @if(auth()->id() === $item->id)
                                        <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700">Anda</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500">{{ $item->email }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm">{{ $item->nip ?? $item->nik ?? '-' }}</td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">
                                {{ $item->department?->name ?? '-' }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">
                            {{ $item->join_date?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">{{ $item->whatsapp ?? '-' }}</td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @if($item->is_active)
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                            @else
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Nonaktif</span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                            <a href="{{ route('admin.admins.edit', $item) }}" class="font-medium text-amber-600 hover:text-amber-700">Edit</a>

                            @if(auth()->id() !== $item->id)
                            <form
                                action="{{ route('admin.admins.destroy', $item) }}"
                                method="POST"
                                class="ml-3 inline"
                                data-confirm
                                data-confirm-tone="danger"
                                data-confirm-title="Hapus Admin?"
                                data-confirm-message="Akun Admin {{ $item->name }} akan dihapus."
                                data-confirm-button="Ya, Hapus">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-medium text-red-600 hover:text-red-700">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-sm text-slate-500">Data Admin tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($admins->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">
            {{ $admins->links() }}
        </div>
        @endif
    </div>
</div>
@endsection