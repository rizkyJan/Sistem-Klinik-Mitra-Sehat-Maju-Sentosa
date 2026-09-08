<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\HearYouFeedback;
use App\Services\HearYouRequirementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HearYouController extends Controller
{
    public function __construct(
        private readonly HearYouRequirementService $requirements
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $this->ensureEmployeeRole($user?->role);

        $status = $this->requirements->statusFor($user);
        $routePrefix = $user->role === 'kabid' ? 'kabid' : 'karyawan';
        $layout = $user->role === 'kabid' ? 'layouts.kabid' : 'layouts.karyawan';

        $history = HearYouFeedback::query()
            ->with('respondedBy:id,name')
            ->where('user_id', $user->id)
            ->orderByDesc('period')
            ->paginate(12)
            ->withQueryString();

        return view('hear-you.index', [
            'user' => $user,
            'status' => $status,
            'currentFeedback' => $status['current_feedback'],
            'dueEvaluations' => $status['due_evaluations'],
            'history' => $history,
            'layout' => $layout,
            'routePrefix' => $routePrefix,
            'categoryOptions' => HearYouFeedback::categoryOptions(),
            'evaluationOptions' => HearYouFeedback::evaluationOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->ensureEmployeeRole($user?->role);

        $period = $this->requirements->currentPeriod();

        if (
            HearYouFeedback::query()
            ->where('user_id', $user->id)
            ->whereDate('period', $period->toDateString())
            ->exists()
        ) {
            return $this->redirectToIndex($user->role)
                ->with('info', 'Hear You bulan ini sudah pernah dikirim.');
        }

        $validated = $request->validate([
            'category' => [
                'required',
                Rule::in(array_keys(HearYouFeedback::categoryOptions())),
            ],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'category.required' => 'Pilih jenis aspirasi.',
            'category.in' => 'Jenis aspirasi tidak valid.',
            'message.required' => 'Isi Hear You wajib ditulis.',
            'message.min' => 'Isi Hear You minimal 10 karakter.',
            'message.max' => 'Isi Hear You maksimal 5.000 karakter.',
        ]);

        DB::transaction(function () use ($user, $period, $validated): void {
            HearYouFeedback::create([
                'user_id' => $user->id,
                'period' => $period->toDateString(),
                'category' => $validated['category'],
                'message' => trim($validated['message']),
                'follow_up_status' => HearYouFeedback::STATUS_WAITING_RESPONSE,
            ]);
        });

        return $this->redirectToIndex($user->role)
            ->with(
                'success',
                'Hear You bulan ini berhasil dikirim. Kewajiban bulanan Anda sudah tercatat.'
            );
    }

    public function evaluate(
        Request $request,
        HearYouFeedback $hearYouFeedback
    ): RedirectResponse {
        $user = $request->user();
        $this->ensureEmployeeRole($user?->role);

        abort_unless($hearYouFeedback->user_id === $user->id, 403);

        $currentPeriod = $this->requirements->currentPeriod();

        if ($hearYouFeedback->period->gte($currentPeriod)) {
            return $this->redirectToIndex($user->role)
                ->with(
                    'error',
                    'Aspirasi bulan berjalan belum dapat dievaluasi. Evaluasi tersedia mulai bulan berikutnya.'
                );
        }

        if (! $hearYouFeedback->admin_response || ! $hearYouFeedback->responded_at) {
            return $this->redirectToIndex($user->role)
                ->with(
                    'warning',
                    'Evaluasi belum dapat dilakukan karena Admin belum memberikan tanggapan.'
                );
        }

        if ($hearYouFeedback->evaluated_at) {
            return $this->redirectToIndex($user->role)
                ->with('info', 'Aspirasi tersebut sudah pernah Anda evaluasi.');
        }

        $validated = $request->validate([
            'employee_evaluation' => [
                'required',
                Rule::in(array_keys(HearYouFeedback::evaluationOptions())),
            ],
            'evaluation_note' => ['nullable', 'string', 'max:3000'],
        ], [
            'employee_evaluation.required' => 'Pilih hasil evaluasi tindak lanjut.',
            'employee_evaluation.in' => 'Pilihan evaluasi tidak valid.',
            'evaluation_note.max' => 'Catatan evaluasi maksimal 3.000 karakter.',
        ]);

        $hearYouFeedback->update([
            'employee_evaluation' => $validated['employee_evaluation'],
            'evaluation_note' => filled($validated['evaluation_note'] ?? null)
                ? trim($validated['evaluation_note'])
                : null,
            'evaluated_at' => now(config('hear-you.timezone', 'Asia/Jakarta')),
        ]);

        return $this->redirectToIndex($user->role)
            ->with('success', 'Evaluasi tindak lanjut berhasil disimpan.');
    }

    private function ensureEmployeeRole(?string $role): void
    {
        abort_unless(in_array($role, ['karyawan', 'kabid'], true), 403);
    }

    private function redirectToIndex(string $role): RedirectResponse
    {
        $prefix = $role === 'kabid' ? 'kabid' : 'karyawan';

        return redirect()->route($prefix . '.hear-you.index');
    }
}
