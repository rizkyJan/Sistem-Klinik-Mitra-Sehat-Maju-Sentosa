@props([
'count' => 0,
])

@php
$notificationCount = (int) $count;
@endphp

@if($notificationCount > 0)
<span
    {{ $attributes->merge([
            'class' =>
                'ml-auto inline-flex min-w-[22px] items-center justify-center
                 rounded-full bg-red-500 px-1.5 py-0.5
                 text-[11px] font-bold leading-4 text-white
                 shadow-sm ring-2 ring-slate-900'
        ]) }}
    title="{{ $notificationCount }} data menunggu diproses">
    {{ $notificationCount > 99 ? '99+' : $notificationCount }}
</span>
@endif