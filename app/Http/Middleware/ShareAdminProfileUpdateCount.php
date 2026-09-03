<?php

namespace App\Http\Middleware;

use App\Models\EmployeeProfileUpdateRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareAdminProfileUpdateCount
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $pendingProfileUpdateCount = 0;

        if (
            $request->user()
            && $request->user()->role === 'admin'
        ) {
            $pendingProfileUpdateCount =
                EmployeeProfileUpdateRequest::query()
                ->where(
                    'status',
                    EmployeeProfileUpdateRequest::STATUS_PENDING
                )
                ->count();
        }

        View::share(
            'pendingProfileUpdateCount',
            $pendingProfileUpdateCount
        );

        return $next($request);
    }
}
