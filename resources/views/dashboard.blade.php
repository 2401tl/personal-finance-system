@include('coms.header', [
    'title' => '仪表盘',
    'headScripts' => [
        'plugs/chart.js/chart.min.js',
        'plugs/flatpickr/flatpickr.min.js',
        'plugs/flatpickr/l10n/zh.js',
    ],
    'imports' => ['css/dashboard.css'],
])

<div class="container-fluid">
    <div class="row">
        @include('coms.left-nav', ['active' => 'dashboard'])

        <div class="col-md-9 col-lg-10 main-content">
            @include('coms.top-nav', ['pageTitle' => '财务仪表盘'])

            <div class="time-filter">
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="month">本月</button>
                    <button class="filter-btn" data-filter="year">本年</button>
                    <button class="filter-btn" data-filter="all">全部</button>
                    <button class="filter-btn" data-filter="range">自定义区间</button>
                </div>
                <div class="date-range-picker" id="dateRangePicker">
                    <div class="date-input"><label>开始日期</label><input type="text" id="startDate" placeholder="选择开始日期" readonly></div>
                    <div class="date-input"><label>结束日期</label><input type="text" id="endDate" placeholder="选择结束日期" readonly></div>
                    <button class="apply-range" id="applyRange">应用区间</button>
                </div>
            </div>

            <div class="stats-cards">
                <div class="stat-card"><div class="stat-title">总收入</div><div class="stat-value income-color" id="totalIncome">¥0</div><div class="stat-sub" id="incomeSub">统计中...</div></div>
                <div class="stat-card"><div class="stat-title">总支出</div><div class="stat-value expense-color" id="totalExpense">¥0</div><div class="stat-sub" id="expenseSub">统计中...</div></div>
                <div class="stat-card"><div class="stat-title">结余</div><div class="stat-value balance-color" id="totalBalance">¥0</div><div class="stat-sub" id="balanceSub">统计中...</div></div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="chart-container"><div class="chart-title">收支趋势</div><canvas id="trendChart" style="height: 300px;"></canvas></div>
                </div>
                <div class="col-lg-4">
                    <div class="chart-container"><div class="chart-title">支出分类占比</div><canvas id="categoryChart" style="height: 300px;"></canvas></div>
                </div>
            </div>

            <div class="recent-bills">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="chart-title" style="margin-bottom: 0;">近期账单</div>
                    <a href="{{ url('/transactions') }}" class="text-decoration-none" style="color: #667eea;">查看全部 &gt;</a>
                </div>
                <div id="recentBillsList"></div>
            </div>
        </div>
    </div>
</div>

@include('coms.footer', ['imports' => ['js/dashboard.js']])
