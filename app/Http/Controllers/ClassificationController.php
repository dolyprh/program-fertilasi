<?php

namespace App\Http\Controllers;

use App\Enums\ClassificationStatus;
use App\Http\Requests\StoreClassificationRequest;
use App\Models\Classification;
use App\Models\CurrentAction;
use App\Models\CurrentPromil;
use App\Models\Doctor;
use App\Models\Poa1Option;
use App\Services\DecisionTreeService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ClassificationController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only([
            'case_code',
            'current_promil_id',
            'prediction_result',
            'status',
        ]);

        $classifications = Classification::query()
            ->whereBelongsTo($request->user())
            ->with(['doctor', 'currentPromil', 'currentAction', 'poa1Option'])
            ->when(
                filled($filters['case_code'] ?? null),
                fn ($query) => $query->where('case_code', 'like', '%'.$filters['case_code'].'%'),
            )
            ->when(
                filled($filters['current_promil_id'] ?? null),
                fn ($query) => $query->where('current_promil_id', $filters['current_promil_id']),
            )
            ->when(
                filled($filters['prediction_result'] ?? null),
                fn ($query) => $query->where('prediction_result', $filters['prediction_result']),
            )
            ->when(
                filled($filters['status'] ?? null),
                fn ($query) => $query->where('status', $filters['status']),
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $predictionResults = Classification::query()
            ->whereBelongsTo($request->user())
            ->whereNotNull('prediction_result')
            ->distinct()
            ->orderBy('prediction_result')
            ->pluck('prediction_result');

        return Inertia::render('classifications/Index', [
            'classifications' => $classifications,
            'filters' => [
                'case_code' => $filters['case_code'] ?? '',
                'current_promil_id' => $filters['current_promil_id'] ?? '',
                'prediction_result' => $filters['prediction_result'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'currentPromils' => CurrentPromil::query()->active()->ordered()->get(['id', 'code', 'name']),
            'predictionResults' => $predictionResults,
            'statuses' => [
                ['value' => ClassificationStatus::Classified->value, 'label' => 'Classified'],
                ['value' => ClassificationStatus::NoRule->value, 'label' => 'No Rule'],
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('classifications/Create', [
            'doctors' => Doctor::query()->active()->ordered()->get(['id', 'code', 'name']),
            'currentPromils' => CurrentPromil::query()->active()->ordered()->get(['id', 'code', 'name']),
            'currentActions' => CurrentAction::query()->active()->ordered()->get(['id', 'code', 'name']),
            'poa1Options' => Poa1Option::query()->active()->ordered()->get(['id', 'code', 'name']),
            'trainingAgeRange' => config('fertility.training_age_range'),
        ]);
    }

    public function store(
        StoreClassificationRequest $request,
        DecisionTreeService $decisionTreeService,
    ): RedirectResponse {
        $validated = $request->validated();

        $doctor = Doctor::query()->active()->findOrFail($validated['doctor_id']);
        $currentPromil = CurrentPromil::query()->active()->findOrFail($validated['current_promil_id']);
        $currentAction = CurrentAction::query()->active()->findOrFail($validated['current_action_id']);
        $poa1Option = Poa1Option::query()->active()->findOrFail($validated['poa1_option_id']);

        $dateOfBirth = CarbonImmutable::parse($validated['date_of_birth']);
        $visitDate = CarbonImmutable::parse($validated['visit_date']);
        $age = $dateOfBirth->diff($visitDate)->y;

        abort_unless($age >= 1 && $age <= 120, 422, 'Umur pasien harus antara 1 sampai 120 tahun.');

        $result = $decisionTreeService->classify([
            'age' => $age,
            'current_promil_code' => $currentPromil->code,
            'current_action_code' => $currentAction->code,
        ]);

        $classification = DB::transaction(function () use (
            $request,
            $validated,
            $doctor,
            $currentPromil,
            $currentAction,
            $poa1Option,
            $visitDate,
            $dateOfBirth,
            $age,
            $result,
        ): Classification {
            return Classification::query()->create([
                'user_id' => $request->user()->id,
                'doctor_id' => $doctor->id,
                'current_promil_id' => $currentPromil->id,
                'current_action_id' => $currentAction->id,
                'poa1_option_id' => $poa1Option->id,
                'case_code' => 'CLS-'.$visitDate->format('Ymd').'-'.Str::ulid(),
                'patient_name' => $validated['patient_name'],
                'visit_date' => $visitDate->toDateString(),
                'date_of_birth' => $dateOfBirth->toDateString(),
                'age' => $age,
                'prediction_result' => $result['prediction'],
                'rule_code' => $result['rule_code'],
                'decision_path' => $result['decision_path'],
                'confidence' => $result['confidence'],
                'support_count' => $result['support_count'],
                'support_percentage' => $result['support_percentage'],
                'status' => $result['matched']
                    ? ClassificationStatus::Classified
                    : ClassificationStatus::NoRule,
            ]);
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $result['matched']
                ? 'Klasifikasi berhasil diproses.'
                : 'Klasifikasi disimpan. Tidak ditemukan decision rule yang sesuai.',
        ]);

        return to_route('classifications.show', $classification);
    }

    public function show(Request $request, Classification $classification): Response
    {
        abort_unless($classification->user_id === $request->user()->id, 403);

        $classification->load(['doctor', 'currentPromil', 'currentAction', 'poa1Option']);

        return Inertia::render('classifications/Show', [
            'classification' => $classification,
            'trainingAgeRange' => config('fertility.training_age_range'),
            'trainingCount' => config('fertility.model.training_count'),
        ]);
    }
}
