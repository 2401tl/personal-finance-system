@include('coms.header', [
    'title' => '账单管理',
    'headScripts' => [
        'plugs/flatpickr/flatpickr.min.js',
        'plugs/flatpickr/l10n/zh.js',
    ],
    'imports' => ['css/transactions.css'],
])

<div class="container-fluid">
    <div class="row">
        @include('coms.left-nav', ['active' => 'transactions'])

        <div class="col-md-9 col-lg-10 main-content">
            @include('coms.top-nav', ['pageTitle' => '账单管理'])

            <div class="stats-cards">
                <div class="stat-card"><div class="stat-title">总收入</div><div class="stat-value income-color" id="totalIncome">¥0</div></div>
                <div class="stat-card"><div class="stat-title">总支出</div><div class="stat-value expense-color" id="totalExpense">¥0</div></div>
                <div class="stat-card"><div class="stat-title">结余</div><div class="stat-value balance-color" id="totalBalance">¥0</div></div>
            </div>

            <button class="add-btn" onclick="openAddModal()"><i class="fas fa-plus"></i> 记一笔</button>

            <div class="filter-card">
                <div class="filter-row">
                    <div class="filter-group"><label>日期范围</label><input type="text" id="dateRangeFilter" placeholder="选择日期范围"></div>
                    <div class="filter-group"><label>收支类型</label><select id="typeFilter"><option value="all">全部</option><option value="income">收入</option><option value="expense">支出</option></select></div>
                    <div class="filter-group"><label>一级分类</label><select id="parentCategoryFilter"><option value="all">全部分类</option></select></div>
                    <div><button class="search-btn" id="searchBtn">搜索</button><button class="reset-btn" id="resetBtn">重置</button></div>
                </div>
            </div>

            <div class="transactions-container">
                <div class="transactions-header"><div>分类</div><div>日期</div><div>账户</div><div>金额</div><div>操作</div></div>
                <div id="transactionList"></div>
                <div class="pagination-container"><div class="records-info" id="recordsInfo"></div><nav><ul class="pagination" id="pagination"></ul></nav></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addBillModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">记一笔</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="type-toggle-modal">
                    <div class="type-option-modal active" data-type="expense" onclick="switchModalType('expense')"><i class="fas fa-arrow-down"></i> 支出</div>
                    <div class="type-option-modal" data-type="income" onclick="switchModalType('income')"><i class="fas fa-arrow-up"></i> 收入</div>
                </div>
                <div class="mb-3"><label class="form-label">金额 *</label><input type="number" id="modalAmount" class="form-control" placeholder="0.00" step="0.01"></div>
                <div class="mb-3"><label class="form-label">日期 *</label><input type="text" id="modalDate" class="form-control" placeholder="选择日期"></div>
                <div class="category-select-group"><label>一级分类 *</label><select id="parentCategorySelect" class="form-select" onchange="updateSubCategorySelect()"></select></div>
                <div class="category-select-group"><label>二级分类 *</label><select id="subCategorySelect" class="form-select"></select></div>
                <div class="mb-3"><label class="form-label">账户</label><select id="modalAccount" class="form-select"><option>现金</option><option>银行卡</option><option>支付宝</option><option>微信支付</option></select></div>
                <div class="mb-3"><label class="form-label">备注</label><input type="text" id="modalNote" class="form-control" placeholder="可选"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="saveBill()">保存</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">确认删除</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>确定要删除这条账单吗？</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-danger" id="confirmDeleteBtn">确认删除</button></div></div></div>
</div>

@include('coms.footer', ['imports' => ['js/transactions.js']])
