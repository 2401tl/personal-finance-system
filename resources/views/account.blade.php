@include('coms.header', [
'title' => '账户管理',
'imports' => ['css/accounts.css'],
])

<div class="container-fluid">
    <div class="row">
        @include('coms.left-nav', ['active' => 'accounts'])

        <div class="col-md-9 col-lg-10 main-content">
            @include('coms.top-nav', ['pageTitle' => '账户管理'])

            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-title">总资产</div>
                    <div class="stat-value" id="totalAssets">¥{{ number_format($stats['total_assets'], 2) }}</div>
                    <div class="stat-sub">所有账户余额总和</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">账户数量</div>
                    <div class="stat-value" id="accountCount">{{ $stats['account_count'] }}</div>
                    <div class="stat-sub" id="accountCountSub">共 {{ $stats['account_count'] }} 个账户</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">本月支出</div>
                    <div class="stat-value expense-color" id="monthExpense">¥{{ number_format($stats['month_expense'], 2) }}</div>
                    <div class="stat-sub">本月所有账户支出</div>
                </div>
            </div>

            <button class="add-btn" onclick="openAddAccountModal()"><i class="fas fa-plus"></i> 添加账户</button>
            <div class="accounts-grid" id="accountsList">
                @forelse ($accounts as $acc)
                <div class="account-card">
                    <div class="account-header">
                        <div class="account-info">
                            <div class="account-icon" style="background:#f0f2ff;">
                                <i class="fas {{ $acc->icon }}" style="color:#667eea; font-size:24px;"></i>
                            </div>
                            <div>
                                <div class="account-name">{{ $acc->name }}</div>
                                <span class="account-type">{{ $acc->type_name }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="balance-amount {{ $acc->balance >= 0 ? 'balance-positive' : 'balance-negative' }}">
                                ¥{{ number_format($acc->balance, 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="account-body">
                        <div class="account-stats">
                            <div class="stat-item">
                                <div class="stat-number">{{ $acc->transactions_count }}</div>
                                <div class="stat-label">交易笔数</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">¥{{ number_format($acc->month_expense, 2) }}</div>
                                <div class="stat-label">本月支出</div>
                            </div>
                        </div>
                        @if ($acc->note)
                        <div style="font-size:12px; color:#999; margin-bottom:15px;">
                            <i class="fas fa-sticky-note"></i> {{ $acc->note }}
                        </div>
                        @endif
                        <div class="account-footer">
                            <button class="btn-transfer" onclick="openTransferModal('{{ $acc->id }}')"><i class="fas fa-exchange-alt"></i> 转账</button>
                            <button class="btn-edit-account" onclick="editAccount('{{ $acc->id }}')"><i class="fas fa-edit"></i> 编辑</button>
                            <button class="btn-delete-account" onclick="showDeleteModal('{{ $acc->id }}')"><i class="fas fa-trash"></i> 删除</button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <i class="fas fa-credit-card"></i>
                    <p>暂无账户</p>
                    <small>点击"添加账户"创建您的第一个账户</small>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="accountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">添加账户</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">账户名称 *</label><input type="text" id="accountName" class="form-control" placeholder="例如：招商银行卡"></div>
                <div class="mb-3"><label class="form-label">账户类型 *</label><select id="accountType" class="form-select">
                        <option value="bank">银行卡</option>
                        <option value="alipay">支付宝</option>
                        <option value="wechat">微信支付</option>
                        <option value="cash">现金</option>
                    </select></div>
                <div class="mb-3"><label class="form-label">初始余额 *</label><input type="number" id="accountBalance" class="form-control" placeholder="0.00" step="0.01"></div>
                <div class="mb-3"><label class="form-label">账户图标</label>
                    <div class="icon-picker" id="iconPicker"></div>
                </div>
                <div class="mb-3"><label class="form-label">备注</label><textarea id="accountNote" class="form-control" rows="2" placeholder="可选"></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="saveAccount()">保存</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">转账</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">转出账户</label><select id="fromAccount" class="form-select"></select></div>
                <div class="mb-3"><label class="form-label">转入账户</label><select id="toAccount" class="form-select"></select></div>
                <div class="mb-3"><label class="form-label">转账金额 *</label><input type="number" id="transferAmount" class="form-control" placeholder="0.00" step="0.01"></div>
                <div class="mb-3"><label class="form-label">备注</label><input type="text" id="transferNote" class="form-control" placeholder="可选"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="doTransfer()">确认转账</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">确认删除</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>确定要删除账户 <strong id="deleteAccountName"></strong> 吗？</p>
                <p class="text-danger">删除后该账户下的账单将变为"未指定账户"！</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">确认删除</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.initialAccounts = @json($accountsForJs);
</script>
@include('coms.footer', ['imports' => ['js/accounts.js']])