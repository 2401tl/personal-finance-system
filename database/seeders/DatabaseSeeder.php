<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'test',
                'phone' => '13800138000',
                'password' => Hash::make('123456'),
                'bio' => '记录生活，掌控财务',
            ],
        );

        $user->getOrCreateSetting();

        $this->seedCategories($user);
        $this->seedAccounts($user);
        $this->seedTransactions($user);
    }

    private function seedCategories(User $user): void
    {
        if ($user->categories()->exists()) {
            return;
        }

        $expenseData = [
            ['name' => '餐饮', 'icon' => 'fa-utensils', 'color' => '#ff6b6b', 'budget' => 2000, 'subs' => [
                ['name' => '早餐', 'icon' => 'fa-coffee'],
                ['name' => '午餐', 'icon' => 'fa-utensil-spoon'],
                ['name' => '晚餐', 'icon' => 'fa-utensils'],
            ]],
            ['name' => '交通', 'icon' => 'fa-bus', 'color' => '#4ecdc4', 'budget' => 500, 'subs' => [
                ['name' => '地铁', 'icon' => 'fa-subway'],
                ['name' => '公交', 'icon' => 'fa-bus'],
                ['name' => '打车', 'icon' => 'fa-taxi'],
            ]],
            ['name' => '购物', 'icon' => 'fa-shopping-bag', 'color' => '#ffa500', 'budget' => 3000, 'subs' => [
                ['name' => '服装', 'icon' => 'fa-tshirt'],
                ['name' => '数码', 'icon' => 'fa-laptop'],
            ]],
            ['name' => '娱乐', 'icon' => 'fa-film', 'color' => '#9b59b6', 'budget' => 1000, 'subs' => [
                ['name' => '电影', 'icon' => 'fa-film'],
                ['name' => '游戏', 'icon' => 'fa-gamepad'],
            ]],
        ];

        foreach ($expenseData as $index => $item) {
            $parent = $user->categories()->create([
                'type' => Category::TYPE_EXPENSE,
                'name' => $item['name'],
                'icon' => $item['icon'],
                'color' => $item['color'],
                'budget' => $item['budget'],
                'sort_order' => $index + 1,
            ]);

            foreach ($item['subs'] as $subIndex => $sub) {
                $user->categories()->create([
                    'type' => Category::TYPE_EXPENSE,
                    'parent_id' => $parent->id,
                    'name' => $sub['name'],
                    'icon' => $sub['icon'],
                    'color' => $item['color'],
                    'sort_order' => $subIndex + 1,
                ]);
            }
        }

        $incomeData = [
            ['name' => '工资', 'icon' => 'fa-wallet', 'color' => '#28a745', 'subs' => [
                ['name' => '基本工资', 'icon' => 'fa-wallet'],
                ['name' => '加班费', 'icon' => 'fa-clock'],
            ]],
            ['name' => '奖金', 'icon' => 'fa-gift', 'color' => '#28a745', 'subs' => []],
            ['name' => '投资', 'icon' => 'fa-chart-line', 'color' => '#28a745', 'subs' => [
                ['name' => '股票', 'icon' => 'fa-chart-line'],
                ['name' => '基金', 'icon' => 'fa-chart-pie'],
            ]],
        ];

        foreach ($incomeData as $index => $item) {
            $parent = $user->categories()->create([
                'type' => Category::TYPE_INCOME,
                'name' => $item['name'],
                'icon' => $item['icon'],
                'color' => $item['color'],
                'sort_order' => $index + 1,
            ]);

            foreach ($item['subs'] as $subIndex => $sub) {
                $user->categories()->create([
                    'type' => Category::TYPE_INCOME,
                    'parent_id' => $parent->id,
                    'name' => $sub['name'],
                    'icon' => $sub['icon'],
                    'color' => $item['color'],
                    'sort_order' => $subIndex + 1,
                ]);
            }
        }
    }

    private function seedAccounts(User $user): void
    {
        if ($user->accounts()->exists()) {
            return;
        }

        $accounts = [
            ['name' => '招商银行卡', 'type' => Account::TYPE_BANK, 'balance' => 12580, 'icon' => 'fa-credit-card', 'note' => '工资卡'],
            ['name' => '支付宝', 'type' => Account::TYPE_ALIPAY, 'balance' => 3560.5, 'icon' => 'fa-alipay', 'note' => '日常消费'],
            ['name' => '微信支付', 'type' => Account::TYPE_WECHAT, 'balance' => 1240, 'icon' => 'fa-weixin', 'note' => '小额支付'],
            ['name' => '现金', 'type' => Account::TYPE_CASH, 'balance' => 850, 'icon' => 'fa-money-bill-wave', 'note' => '备用现金'],
        ];

        foreach ($accounts as $account) {
            $user->accounts()->create($account);
        }
    }

    private function seedTransactions(User $user): void
    {
        if ($user->transactions()->exists()) {
            return;
        }

        $lunch = $user->categories()->where('name', '午餐')->first();
        $subway = $user->categories()->where('name', '地铁')->first();
        $clothes = $user->categories()->where('name', '服装')->first();
        $salary = $user->categories()->where('name', '基本工资')->first();

        $wechat = $user->accounts()->where('name', '微信支付')->first();
        $alipay = $user->accounts()->where('name', '支付宝')->first();
        $bank = $user->accounts()->where('name', '招商银行卡')->first();

        $records = [
            ['category_id' => $lunch?->id, 'account_id' => $wechat?->id, 'type' => Transaction::TYPE_EXPENSE, 'amount' => 68, 'date' => '2024-01-25', 'note' => '麦当劳'],
            ['category_id' => $subway?->id, 'account_id' => $alipay?->id, 'type' => Transaction::TYPE_EXPENSE, 'amount' => 12, 'date' => '2024-01-25', 'note' => '通勤'],
            ['category_id' => $clothes?->id, 'account_id' => $bank?->id, 'type' => Transaction::TYPE_EXPENSE, 'amount' => 299, 'date' => '2024-01-24', 'note' => '优衣库'],
            ['category_id' => $salary?->id, 'account_id' => $bank?->id, 'type' => Transaction::TYPE_INCOME, 'amount' => 8000, 'date' => '2024-01-20', 'note' => '1月工资'],
        ];

        foreach ($records as $record) {
            if (! $record['category_id'] || ! $record['account_id']) {
                continue;
            }

            $user->transactions()->create($record);
        }
    }
}
