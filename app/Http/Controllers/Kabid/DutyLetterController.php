<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
use App\Models\DutyAssignment;
use App\Models\DutyLetter;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DutyLetterController extends Controller
{
    /**
     * Menampilkan seluruh Surat Dinas milik Kabid yang sedang login.
     */
    public function index(Request $request): View
    {
        $this->ensureKabid();

        /** @var User $user */
        $user = $request->user();

        $search = trim((string) $request->input('search', ''));
        $filter = $request->input('filter');

        $allowedFilters = [
            'upcoming',
            'past',
            'cancelled',
            'report_pending',
            'report_revision',
            'report_verified',
        ];

        if (! in_array($filter, $allowedFilters, true)) {
            $filter = null;
        }

        $assignments = DutyAssignment::query()
            ->where('user_id', $user->id)
            ->with([
                'dutyLetter.creator',
                'report',
            ])
            ->whereHas('dutyLetter')
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->whereHas(
                        'dutyLetter',
                        function ($letterQuery) use ($search) {
                            $letterQuery->where(
                                function ($searchQuery) use ($search) {
                                    $searchQuery
                                        ->where(
                                            'letter_number',
                                            'like',
                                            '%' . $search . '%'
                                        )
                                        ->orWhere(
                                            'title',
                                            'like',
                                            '%' . $search . '%'
                                        )
                                        ->orWhere(
                                            'organizer',
                                            'like',
                                            '%' . $search . '%'
                                        )
                                        ->orWhere(
                                            'location_name',
                                            'like',
                                            '%' . $search . '%'
                                        );
                                }
                            );
                        }
                    );
                }
            )
            ->when(
                $filter === 'upcoming',
                fn($query) => $query->whereHas(
                    'dutyLetter',
                    fn($letterQuery) => $letterQuery
                        ->where('status', DutyLetter::STATUS_PUBLISHED)
                        ->whereDate('event_date', '>=', today())
                )
            )
            ->when(
                $filter === 'past',
                fn($query) => $query->whereHas(
                    'dutyLetter',
                    fn($letterQuery) => $letterQuery
                        ->where('status', DutyLetter::STATUS_PUBLISHED)
                        ->whereDate('event_date', '<', today())
                )
            )
            ->when(
                $filter === 'cancelled',
                fn($query) => $query->whereHas(
                    'dutyLetter',
                    fn($letterQuery) => $letterQuery->where(
                        'status',
                        DutyLetter::STATUS_CANCELLED
                    )
                )
            )
            ->when(
                $filter === 'report_pending',
                fn($query) => $query->where(
                    'report_status',
                    DutyAssignment::REPORT_PENDING
                )
            )
            ->when(
                $filter === 'report_revision',
                fn($query) => $query->where(
                    'report_status',
                    DutyAssignment::REPORT_REVISION
                )
            )
            ->when(
                $filter === 'report_verified',
                fn($query) => $query->where(
                    'report_status',
                    DutyAssignment::REPORT_VERIFIED
                )
            )
            ->orderByDesc(
                DutyLetter::select('event_date')
                    ->whereColumn(
                        'duty_letters.id',
                        'duty_assignments.duty_letter_id'
                    )
                    ->limit(1)
            )
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $totalCount = DutyAssignment::query()
            ->where('user_id', $user->id)
            ->count();

        $upcomingCount = DutyAssignment::query()
            ->where('user_id', $user->id)
            ->whereHas(
                'dutyLetter',
                fn($query) => $query
                    ->where('status', DutyLetter::STATUS_PUBLISHED)
                    ->whereDate('event_date', '>=', today())
            )
            ->count();

        $pendingReportCount = DutyAssignment::query()
            ->where('user_id', $user->id)
            ->where('report_status', DutyAssignment::REPORT_PENDING)
            ->whereHas(
                'dutyLetter',
                fn($query) => $query->where(
                    'status',
                    DutyLetter::STATUS_PUBLISHED
                )
            )
            ->count();

        $verifiedReportCount = DutyAssignment::query()
            ->where('user_id', $user->id)
            ->where('report_status', DutyAssignment::REPORT_VERIFIED)
            ->count();

        return view(
            'kabid.duty-letters.index',
            compact(
                'assignments',
                'totalCount',
                'upcomingCount',
                'pendingReportCount',
                'verifiedReportCount'
            )
        );
    }


    /**
     * Menampilkan detail Surat Dinas yang memang ditujukan kepada Kabid login.
     */
    public function show(DutyAssignment $dutyAssignment): View
    {
        $this->ensureOwner($dutyAssignment);

        $dutyAssignment->load([
            'dutyLetter.creator',
            'report.files',
            'reportVerifier',
            'feeConfirmer',
        ]);

        abort_if(
            $dutyAssignment->dutyLetter === null,
            404
        );

        return view(
            'kabid.duty-letters.show',
            compact('dutyAssignment')
        );
    }


    /**
     * Menampilkan PDF Surat Dinas secara private.
     * Hanya penerima surat yang dapat mengakses file.
     */
    public function pdf(
        DutyAssignment $dutyAssignment
    ): StreamedResponse {
        $this->ensureOwner($dutyAssignment);

        $dutyAssignment->loadMissing('dutyLetter');

        $dutyLetter = $dutyAssignment->dutyLetter;

        abort_if($dutyLetter === null, 404);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_unless(
            ! empty($dutyLetter->letter_path)
                && $disk->exists($dutyLetter->letter_path),
            404
        );

        return $disk->response(
            $dutyLetter->letter_path,
            $dutyLetter->letter_original_name,
            [
                'Content-Type' =>
                $dutyLetter->letter_mime ?: 'application/pdf',

                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline'
        );
    }


    /**
     * Memastikan assignment benar-benar milik Kabid login.
     */
    private function ensureOwner(
        DutyAssignment $dutyAssignment
    ): void {
        $this->ensureKabid();

        abort_unless(
            $dutyAssignment->user_id === Auth::id(),
            403
        );
    }


    /**
     * Pertahanan tambahan meskipun route sudah memakai role:karyawan.
     */
    private function ensureKabid(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless(
            $user !== null
                && $user->role === 'kabid',
            403
        );
    }
}
