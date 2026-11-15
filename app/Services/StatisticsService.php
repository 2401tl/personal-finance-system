<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StatisticsService
{
    public function summary(User $user, ?string $start = null, ?string $end = null): array
    {
        $query = $user->transactions()->betweenDates($start, $end);

        $income = (clone $query)->ofType(Transaction::TYPE_INCOME)->sum('amount');
        $expense = (clone $query)->ofType(Transaction::TYPE_EXPENSE)->sum('amount');
        $count = (clone $query)->whereIn('type', [Transaction::TYPE_INCOME, Transaction::TYPE_EXPENSE])->count();

        return [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'balance' => (float) ($income - $expense),
            'count' => $count,
        ];
    }

    public function dashboard(User $user, string $period = 'month'): array
    {
        [$start, $end] = $this->resolvePeriod($period);

        $summary = $this->summary($user, $start, $end);
        $recentBills = $user->transactions()
            ->with(['category.parent', 'account'])
            ->whereIn('type', [Transaction::TYPE_INCOME, Transaction::TYPE_EXPENSE])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return [
            'summary' => $summary,
            'trend' => $this->trend($user, $start, $end),
            'recent_bills' => $recentBills,
        ];
    }

    public function trend(User $user, ?string $start = null, ?string $end = null): array
    {
        $transactions = $user->transactions()
            ->betweenDates($start, $end)
            ->whereIn('type', [Transaction::TYPE_INCOME, Transaction::TYPE_EXPENSE])
            ->get(['type', 'amount', 'date']);

        $grouped = $transactions->groupBy(fn ($item) => $item->date->format('Y-m-d'));

        $labels = [];
        $income = [];
        $expense = [];

        foreach ($grouped->sortKeys() as $date => $items) {
            $labels[] = $date;
            $income[] = (float) $items->where('type', Transaction::TYPE_INCOME)->sum('amount');
            $expense[] = (float) $items->where('type', Transaction::TYPE_EXPENSE)->sum('amount');
        }

        return compact('labels', 'income', 'expense');
    }

    public function categoryBreakdown(User $user, string $type, ?string $start = null, ?string $end = null): array
    {
        $transactions = $user->transactions()
            ->with('category.parent')
            ->ofType($type)
            ->betweenDates($start, $end)
            ->get();

        $grouped = $transactions->groupBy(function ($transaction) {
            $category = $transaction->category;

            return $category?->parent?->name ?? $category?->name ?? '未分类';
        });

        $labels = [];
        $data = [];

        foreach ($grouped->sortByDesc(fn ($items) => $items->sum('amount')) as $name => $items) {
            $labels[] = $name;
            $data[] = (float) $items->sum('amount');
        }

        return compact('labels', 'data');
    }

    public function accountBreakdown(User $user, ?string $start = null, ?string $end = null): array
    {
        $transactions = $user->transactions()
            ->with('account')
            ->ofType(Transaction::TYPE_EXPENSE)
            ->betweenDates($start, $end)
            ->get();

        $grouped = $transactions->groupBy(fn ($item) => $item->account?->name ?? '未知账户');

        $labels = [];
        $data = [];

        foreach ($grouped as $name => $items) {
            $labels[] = $name;
            $data[] = (float) $items->sum('amount');
        }

        return compact('labels', 'data');
    }

    public function expenseRank(User $user, ?string $start = null, ?string $end = null): Collection
    {
        $breakdown = $this->categoryBreakdown($user, Transaction::TYPE_EXPENSE, $start, $end);
        $total = array_sum($breakdown['data']);

        return collect($breakdown['labels'])->map(function ($name, $index) use ($breakdown, $total) {
            $amount = $breakdown['data'][$index];

            return [
                'name' => $name,
                'amount' => $amount,
                'percent' => $total > 0 ? round($amount / $total * 100, 1) : 0,
            ];
        })->sortByDesc('amount')->values();
    }

    public function profileStats(User $user): array
    {
        $firstTransaction = $user->transactions()->orderBy('date')->first();
        $days = $firstTransaction
            ? Carbon::parse($firstTransaction->date)->diffInDays(now()) + 1
            : 0;

        $records = $user->transactions()
            ->whereIn('type', [Transaction::TYPE_INCOME, Transaction::TYPE_EXPENSE])
            ->count();

        $categories = $user->categories()->count();

        $monthlyAvg = $user->transactions()
            ->ofType(Transaction::TYPE_EXPENSE)
            ->where('date', '>=', now()->subMonths(6)->toDateString())
            ->avg('amount') ?? 0;

        return [
            'days' => (int) $days,
            'records' => $records,
            'categories' => $categories,
            'monthly_avg' => round((float) $monthlyAvg, 2),
        ];
    }

    private function resolvePeriod(string $period): array
    {
        return match ($period) {
            'week' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
            'year' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
            'all' => [null, null],
            default => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
        };
    }
}
