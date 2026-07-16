<?php

namespace App\Http\Controllers;

use App\Enums\ClassificationStatus;
use App\Models\Classification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $baseQuery = Classification::query()->whereBelongsTo($user);

        $totalClassifications = (clone $baseQuery)->count();
        $todayClassifications = (clone $baseQuery)
            ->whereDate('created_at', Carbon::today())
            ->count();
        $classifiedCount = (clone $baseQuery)
            ->where('status', ClassificationStatus::Classified)
            ->count();
        $noRuleCount = (clone $baseQuery)
            ->where('status', ClassificationStatus::NoRule)
            ->count();

        $recentClassifications = (clone $baseQuery)
            ->with(['doctor', 'currentPromil'])
            ->latest()
            ->limit(5)
            ->get();

        $resultDistribution = (clone $baseQuery)
            ->whereNotNull('prediction_result')
            ->selectRaw('prediction_result, COUNT(*) as total')
            ->groupBy('prediction_result')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->prediction_result,
                'total' => (int) $row->total,
            ])
            ->values();

        return Inertia::render('Dashboard', [
            'totalClassifications' => $totalClassifications,
            'todayClassifications' => $todayClassifications,
            'classifiedCount' => $classifiedCount,
            'noRuleCount' => $noRuleCount,
            'recentClassifications' => $recentClassifications,
            'resultDistribution' => $resultDistribution,
        ]);
    }
}
