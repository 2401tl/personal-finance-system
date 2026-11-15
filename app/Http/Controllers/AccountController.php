<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\StoreAccountRequest;
use App\Http\Requests\Account\TransferRequest;
use App\Http\Requests\Account\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use InvalidArgumentException;

class AccountController extends Controller
{
    public function __construct(private AccountService $accountService)
    {
    }

    public function page(Request $request): View
    {
        $accounts = $this->loadUserAccounts($request);

        return view('account', [
            'accounts' => $accounts,
            'stats' => $this->buildStats($accounts),
            'accountsForJs' => $this->buildAccountsForJs($accounts),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $accounts = $this->loadUserAccounts($request);

        return $this->success([
            'items' => AccountResource::collection($accounts),
            'stats' => $this->buildStats($accounts),
        ]);
    }

    public function store(StoreAccountRequest $request): JsonResponse
    {
        $account = $request->user()->accounts()->create([
            ...$request->validated(),
            'icon' => $request->input('icon', 'fa-credit-card'),
        ]);

        return $this->success(new AccountResource($account), '账户添加成功');
    }

    public function update(UpdateAccountRequest $request, Account $account): JsonResponse
    {
        $this->authorizeAccount($request, $account);

        $account->update($request->validated());

        return $this->success(new AccountResource($account), '账户修改成功');
    }

    public function destroy(Request $request, Account $account): JsonResponse
    {
        $this->authorizeAccount($request, $account);

        $this->accountService->delete($account);

        return $this->success(null, '账户删除成功');
    }

    public function transfer(TransferRequest $request): JsonResponse
    {
        try {
            $transaction = $this->accountService->transfer(
                $request->user(),
                $request->integer('from_account_id'),
                $request->integer('to_account_id'),
                (float) $request->input('amount'),
                $request->input('note'),
            );

            $transaction->load(['account', 'transferToAccount']);

            return $this->success(new TransactionResource($transaction), '转账成功');
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage());
        }
    }

    private function loadUserAccounts(Request $request): Collection
    {
        $monthStart = now()->startOfMonth()->toDateString();

        return $request->user()->accounts()
            ->withCount('transactions')
            ->get()
            ->map(function (Account $account) use ($monthStart) {
                $account->month_expense = $account->transactions()
                    ->ofType(Transaction::TYPE_EXPENSE)
                    ->where('date', '>=', $monthStart)
                    ->sum('amount');

                return $account;
            });
    }

    private function buildStats(Collection $accounts): array
    {
        return [
            'total_assets' => (float) $accounts->sum('balance'),
            'account_count' => $accounts->count(),
            'month_expense' => (float) $accounts->sum('month_expense'),
        ];
    }

    private function buildAccountsForJs(Collection $accounts): array
    {
        return $accounts->map(fn (Account $account) => [
            'id' => $account->id,
            'name' => $account->name,
            'type' => $account->type,
            'typeName' => $account->type_name,
            'balance' => (float) $account->balance,
            'icon' => $account->icon,
            'note' => $account->note ?? '',
            'transactionCount' => $account->transactions_count,
            'monthExpense' => (float) $account->month_expense,
        ])->values()->all();
    }

    private function authorizeAccount(Request $request, Account $account): void
    {
        abort_if($account->user_id !== $request->user()->id, 403, '无权操作此账户');
    }
}
