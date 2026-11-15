<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AccountService
{
    public function transfer(User $user, int $fromAccountId, int $toAccountId, float $amount, ?string $note = null): Transaction
    {
        if ($fromAccountId === $toAccountId) {
            throw new InvalidArgumentException('不能转账到同一账户');
        }

        if ($amount <= 0) {
            throw new InvalidArgumentException('转账金额必须大于0');
        }

        return DB::transaction(function () use ($user, $fromAccountId, $toAccountId, $amount, $note) {
            $fromAccount = Account::where('user_id', $user->id)->lockForUpdate()->findOrFail($fromAccountId);
            $toAccount = Account::where('user_id', $user->id)->lockForUpdate()->findOrFail($toAccountId);

            if ($fromAccount->balance < $amount) {
                throw new InvalidArgumentException("{$fromAccount->name} 余额不足");
            }

            $fromAccount->decrement('balance', $amount);
            $toAccount->increment('balance', $amount);

            return Transaction::create([
                'user_id' => $user->id,
                'account_id' => $fromAccount->id,
                'transfer_to_account_id' => $toAccount->id,
                'type' => Transaction::TYPE_TRANSFER,
                'amount' => $amount,
                'date' => now()->toDateString(),
                'note' => $note,
            ]);
        });
    }

    public function delete(Account $account): void
    {
        DB::transaction(function () use ($account) {
            Transaction::where('account_id', $account->id)->update(['account_id' => null]);
            Transaction::where('transfer_to_account_id', $account->id)->update(['transfer_to_account_id' => null]);
            $account->delete();
        });
    }

    public function applyTransactionBalance(Transaction $transaction, bool $reverse = false): void
    {
        $account = Account::lockForUpdate()->findOrFail($transaction->account_id);
        $multiplier = $reverse ? -1 : 1;

        match ($transaction->type) {
            Transaction::TYPE_INCOME => $account->increment('balance', $transaction->amount * $multiplier),
            Transaction::TYPE_EXPENSE => $account->decrement('balance', $transaction->amount * $multiplier),
            default => null,
        };
    }
}
