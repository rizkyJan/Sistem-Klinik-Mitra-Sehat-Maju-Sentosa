<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareKaryawanSidebarCounts
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $unreadDutyNotificationCount = 0;

        if ($user && $user->role === 'karyawan') {
            $unreadDutyNotificationCount = $user->unreadNotifications()
                ->get()
                ->filter(
                    fn($notification) => ($notification->data['module'] ?? null) === 'duty'
                )
                ->count();
        }

        View::share(
            'unreadDutyNotificationCount',
            $unreadDutyNotificationCount
        );

        return $next($request);
    }
}
