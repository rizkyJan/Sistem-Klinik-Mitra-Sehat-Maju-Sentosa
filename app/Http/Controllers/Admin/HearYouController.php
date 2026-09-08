<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HearYouFeedback;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HearYouController extends Controller
{
    public function index(Request $request): View
    {
        $period = $this->resolvePeriod($request->query('period'));
        $periodDate = $period->toDateString();
        $search = trim((string) $request->query('search', ''));
        $statusFilter = (string) $request->query('status', 'all');

        $eligibleUsersQuery = User::query()
            ->whereIn('role', ['karyawan', 'kabid'])
            ->where('is_active', true)
            ->where('approval_status', 'approved');

        $eligibleEmployeeCount = (clone $eligibleUsersQuery)->count();

        $submittedCount = HearYouFeedback::query()
            ->whereDate('period', $periodDate)
            ->whereHas('user', function ($query): void {
                $query
                    ->whereIn('role', ['karyawan', 'kabid'])
                    ->where('is_active', true)
                    ->where('approval_status', 'approved');
            })
            ->count();

        $missingCount = max(0, $eligibleEmployeeCount - $submittedCount);

        $pendingResponseCount = HearYouFeedback::query()
            ->whereDate('period', $periodDate)
            ->whereNull('admin_response')
            ->count();

        $evaluatedCount = HearYouFeedback::query()
            ->whereDate('period', $periodDate)
            ->whereNotNull('evaluated_at')
            ->count();

        $feedbackQuery = HearYouFeedback::query()
            ->with([
                'user:id,name,nip,nik,role,department_id',
                'user.department:id,name',
                'respondedBy:id,name',
            ])
            ->whereDate('period', $periodDate)
            ->orderByRaw('CASE WHEN admin_response IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('created_at');

        if ($search !== '') {
            $feedbackQuery->whereHas('user', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nip', 'like', '%' . $search . '%')
                        ->orWhere('nik', 'like', '%' . $search . '%');
                });
            });
        }

        if ($statusFilter === 'waiting_response') {
            $feedbackQuery->whereNull('admin_response');
        } elseif (array_key_exists(
            $statusFilter,
            HearYouFeedback::followUpStatusOptions()
        )) {
            $feedbackQuery
                ->whereNotNull('admin_response')
                ->where('follow_up_status', $statusFilter);
        } elseif ($statusFilter === 'evaluated') {
            $feedbackQuery->whereNotNull('evaluated_at');
        } elseif ($statusFilter === 'not_evaluated') {
            $feedbackQuery
                ->whereNotNull('admin_response')
                ->whereDate('period', '<', $this->currentPeriod()->toDateString())
                ->whereNull('evaluated_at');
        }

        $feedbacks = $feedbackQuery
            ->paginate(20)
            ->withQueryString();

        $missingUsers = (clone $eligibleUsersQuery)
            ->with('department:id,name')
            ->whereDoesntHave('hearYouFeedbacks', function ($query) use ($periodDate): void {
                $query->whereDate('period', $periodDate);
            })
            ->orderBy('name')
            ->get();

        return view('admin.hear-you.index', [
            'period' => $period,
            'feedbacks' => $feedbacks,
            'missingUsers' => $missingUsers,
            'eligibleEmployeeCount' => $eligibleEmployeeCount,
            'submittedCount' => $submittedCount,
            'missingCount' => $missingCount,
            'pendingResponseCount' => $pendingResponseCount,
            'evaluatedCount' => $evaluatedCount,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'statusOptions' => HearYouFeedback::followUpStatusOptions(),
        ]);
    }

    public function show(HearYouFeedback $hearYouFeedback): View
    {
        $hearYouFeedback->load([
            'user.department',
            'respondedBy:id,name',
        ]);

        return view('admin.hear-you.show', [
            'feedback' => $hearYouFeedback,
            'statusOptions' => HearYouFeedback::followUpStatusOptions(),
        ]);
    }

    public function respond(
        Request $request,
        HearYouFeedback $hearYouFeedback
    ): RedirectResponse {
        $validated = $request->validate([
            'admin_response' => ['required', 'string', 'min:3', 'max:5000'],
            'follow_up_status' => [
                'required',
                Rule::in(array_keys(HearYouFeedback::followUpStatusOptions())),
            ],
        ], [
            'admin_response.required' => 'Tanggapan Admin wajib diisi.',
            'admin_response.min' => 'Tanggapan Admin minimal 3 karakter.',
            'admin_response.max' => 'Tanggapan Admin maksimal 5.000 karakter.',
            'follow_up_status.required' => 'Pilih status tindak lanjut.',
            'follow_up_status.in' => 'Status tindak lanjut tidak valid.',
        ]);

        $hearYouFeedback->update([
            'admin_response' => trim($validated['admin_response']),
            'follow_up_status' => $validated['follow_up_status'],
            'responded_by' => $request->user()->id,
            'responded_at' => now(config('hear-you.timezone', 'Asia/Jakarta')),
        ]);

        return redirect()
            ->route('admin.hear-you.show', $hearYouFeedback)
            ->with('success', 'Tanggapan Hear You berhasil disimpan.');
    }

    private function resolvePeriod(mixed $value): Carbon
    {
        if (is_string($value) && preg_match('/^\d{4}-\d{2}$/', $value)) {
            try {
                return Carbon::createFromFormat(
                    'Y-m-d',
                    $value . '-01',
                    config('hear-you.timezone', 'Asia/Jakarta')
                )->startOfMonth();
            } catch (\Throwable) {
                // Jatuh ke periode bulan berjalan.
            }
        }

        return $this->currentPeriod();
    }

    private function currentPeriod(): Carbon
    {
        return Carbon::now(config('hear-you.timezone', 'Asia/Jakarta'))
            ->startOfMonth();
    }
}
