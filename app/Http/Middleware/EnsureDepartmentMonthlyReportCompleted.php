<?php

namespace App\Http\Middleware;

use App\Services\DepartmentMonthlyReportRequirementService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDepartmentMonthlyReportCompleted
{
    public function __construct(
        private readonly DepartmentMonthlyReportRequirementService $requirements
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $this->requirements->isRequiredFor($user)) {
            return $next($request);
        }

        $routeName = (string) $request->route()?->getName();

        /*
         * Jalur penyelesaian kewajiban tidak boleh ikut terkunci.
         * Hear You juga tetap boleh dibuka supaya kedua middleware tidak deadlock.
         */
        if (
            $routeName === 'kabid.dashboard'
            || str_starts_with($routeName, 'kabid.monthly-reports.')
            || str_starts_with($routeName, 'kabid.hear-you.')
            || str_starts_with($routeName, 'kabid.notifications.')
        ) {
            return $next($request);
        }

        $status = $this->requirements->statusFor($user);

        if (! $status['locked']) {
            return $next($request);
        }

        $message = $status['needs_revision']
            ? 'Laporan bulanan bidang perlu direvisi sesuai catatan Admin.'
            : 'Laporan bulanan bidang untuk periode ini belum diunggah.';

        return redirect()
            ->route('kabid.monthly-reports.index')
            ->with(
                'warning',
                $message . ' Selesaikan laporan terlebih dahulu agar fitur operasional dapat digunakan kembali.'
            );
    }
}
