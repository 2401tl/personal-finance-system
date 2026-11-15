<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function __construct(private StatisticsService $statisticsService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $period = $request->input('period', 'month');
        [$start, $end] = $this->resolvePeriod($request, $period);

        $user = $request->user();

        return $this->success([
            'summary' => $this->statisticsService->summary($user, $start, $end),
            'trend' => $this->statisticsService->trend($user, $start, $end),
            'expense_categories' => $this->statisticsService->categoryBreakdown(
                $user,
                Transaction::TYPE_EXPENSE,
                $start,
                $end,
            ),
            'income_categories' => $this->statisticsService->categoryBreakdown(
                $user,
                Transaction::TYPE_INCOME,
                $start,
                $end,
            ),
            'accounts' => $this->statisticsService->accountBreakdown($user, $start, $end),
            'expense_rank' => $this->statisticsService->expenseRank($user, $start, $end),
        ]);
    }

    private function resolvePeriod(Request $request, string $period): array
    {
        if ($period === 'range') {
            return [$request->input('start_date'), $request->input('end_date')];
        }

        return match ($period) {
            'week' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
            'year' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
            'all' => [null, null],
            default => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
        };
    }
}
