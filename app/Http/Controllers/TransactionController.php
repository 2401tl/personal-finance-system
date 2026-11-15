<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct(private AccountService $accountService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->transactions()
            ->with(['category.parent', 'account'])
            ->whereIn('type', [Transaction::TYPE_INCOME, Transaction::TYPE_EXPENSE]);

        if ($request->filled('type') && $request->input('type') !== 'all') {
            $query->ofType($request->input('type'));
        }

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->betweenDates($request->input('start_date'), $request->input('end_date'));
        }

        if ($request->filled('parent_id') && $request->input('parent_id') !== 'all') {
            $parentId = (int) $request->input('parent_id');
            $childIds = Category::where('parent_id', $parentId)->pluck('id')->push($parentId);
            $query->whereIn('category_id', $childIds);
        }

        $pageSize = (int) $request->input('page_size', 10);
        $summaryQuery = clone $query;
        $income = (clone $summaryQuery)->ofType(Transaction::TYPE_INCOME)->sum('amount');
        $expense = (clone $summaryQuery)->ofType(Transaction::TYPE_EXPENSE)->sum('amount');
        $transactions = $query->orderByDesc('date')->orderByDesc('id')->paginate($pageSize);

        return $this->success([
            'items' => TransactionResource::collection($transactions),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'total' => $transactions->total(),
                'per_page' => $transactions->perPage(),
            ],
            'summary' => [
                'income' => (float) $income,
                'expense' => (float) $expense,
                'balance' => (float) ($income - $expense),
            ],
        ]);
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $category = Category::where('user_id', $user->id)->findOrFail($data['category_id']);
        $account = $user->accounts()->findOrFail($data['account_id']);

        $transaction = DB::transaction(function () use ($user, $data, $category, $account) {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'account_id' => $account->id,
                'type' => $data['type'],
                'amount' => $data['amount'],
                'date' => $data['date'],
                'note' => $data['note'] ?? null,
            ]);

            $this->accountService->applyTransactionBalance($transaction);

            return $transaction;
        });

        $transaction->load(['category.parent', 'account']);

        return $this->success(new TransactionResource($transaction), '添加成功');
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $this->authorizeTransaction($request, $transaction);

        DB::transaction(function () use ($request, $transaction) {
            $this->accountService->applyTransactionBalance($transaction, reverse: true);

            $transaction->update($request->validated());

            $this->accountService->applyTransactionBalance($transaction->fresh());
        });

        $transaction->load(['category.parent', 'account']);

        return $this->success(new TransactionResource($transaction->fresh()), '修改成功');
    }

    public function destroy(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorizeTransaction($request, $transaction);

        DB::transaction(function () use ($transaction) {
            $this->accountService->applyTransactionBalance($transaction, reverse: true);
            $transaction->delete();
        });

        return $this->success(null, '删除成功');
    }

    private function authorizeTransaction(Request $request, Transaction $transaction): void
    {
        abort_if($transaction->user_id !== $request->user()->id, 403, '无权操作此账单');
    }
}
