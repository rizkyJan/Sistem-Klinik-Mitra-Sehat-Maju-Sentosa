<?php

namespace App\Http\Middleware;

use App\Services\HearYouRequirementService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHearYouCompleted
{
    public function __construct(
        private readonly HearYouRequirementService $requirements
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $this->requirements->isRequiredFor($user)) {
            return $next($request);
        }

        /*
         * Halaman yang harus tetap bisa dibuka ketika fitur lain terkunci:
         * - Dashboard, agar user tetap tahu kondisi akunnya.
         * - Hear You, agar kewajiban bisa diselesaikan.
         * - Notifikasi, agar panel notifikasi tetap berfungsi.
         */
        $routeName = $request->route()?->getName();
        $prefix = $user->role === 'kabid' ? 'kabid' : 'karyawan';

        if (
            $routeName === $prefix . '.dashboard'
            || str_starts_with((string) $routeName, $prefix . '.hear-you.')
            || str_starts_with((string) $routeName, $prefix . '.notifications.')
        ) {
            return $next($request);
        }

        $status = $this->requirements->statusFor($user);

        if (! $status['locked']) {
            return $next($request);
        }

        $messages = [];

        if (! $status['current_submitted']) {
            $messages[] = 'Hear You bulan ini belum diisi';
        }

        if ($status['due_evaluation_count'] > 0) {
            $messages[] = $status['due_evaluation_count']
                . ' evaluasi tindak lanjut belum dinilai';
        }

        return redirect()
            ->route($prefix . '.hear-you.index')
            ->with(
                'warning',
                'Selesaikan kewajiban Hear You terlebih dahulu: '
                    . implode(' dan ', $messages)
                    . '.'
            );
    }
}
