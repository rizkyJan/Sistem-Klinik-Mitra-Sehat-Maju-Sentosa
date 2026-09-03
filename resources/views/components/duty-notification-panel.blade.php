@props([
'notifications' => collect(),
'role',
])

@php
$items = collect($notifications);
$unreadCount = $items->whereNull('read_at')->count();
@endphp

<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="font-semibold text-slate-800">Notifikasi Surat Dinas</h2>
                @if($unreadCount > 0)
                <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-red-500 px-2 py-0.5 text-[11px] font-bold text-white">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }} baru
                </span>
                @endif
            </div>
            <p class="mt-1 text-xs text-slate-500">
                Perubahan penting pada penugasan dan laporan dinas.
            </p>
        </div>

        @if($unreadCount > 0)
        <form method="POST" action="{{ route($role . '.notifications.read-all') }}">
            @csrf
            @method('PATCH')
            <button
                type="submit"
                class="inline-flex w-full items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 sm:w-auto">
                Tandai semua dibaca
            </button>
        </form>
        @endif
    </div>

    <div class="divide-y divide-slate-100">
        @forelse($items as $notification)
        @php
        $data = $notification->data;
        $isUnread = $notification->read_at === null;
        @endphp

        <a
            href="{{ route($role . '.notifications.open', $notification) }}"
            class="block px-4 py-4 transition hover:bg-slate-50 sm:px-5 {{ $isUnread ? 'bg-blue-50/50' : 'bg-white' }}">
            <div class="flex items-start gap-3">
                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ $isUnread ? 'bg-blue-500' : 'bg-slate-300' }}"></span>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                        <p class="break-words text-sm font-semibold {{ $isUnread ? 'text-slate-900' : 'text-slate-700' }}">
                            {{ $data['title'] ?? 'Informasi Surat Dinas' }}
                        </p>

                        <span class="shrink-0 text-[11px] text-slate-400">
                            {{ $notification->created_at?->diffForHumans() }}
                        </span>
                    </div>

                    <p class="mt-1 break-words text-sm leading-relaxed text-slate-600">
                        {{ $data['message'] ?? '-' }}
                    </p>
                </div>
            </div>
        </a>
        @empty
        <div class="px-5 py-10 text-center text-sm text-slate-500">
            Belum ada notifikasi Surat Dinas.
        </div>
        @endforelse
    </div>
</div>