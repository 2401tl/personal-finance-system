@include('coms.header', [
    'title' => '统计分析',
    'headScripts' => [
        'plugs/chart.js/chart.min.js',
        'plugs/flatpickr/flatpickr.min.js',
        'plugs/flatpickr/l10n/zh.js',
    ],
    'imports' => ['css/statistics.css'],
])

<div class="container-fluid">
    <div class="row">
        @include('coms.left-nav', ['active' => 'statistics'])

        <div class="col-md-9 col-lg-10 main-content">
            @include('coms.top-nav', ['pageTitle' => '统计分析'])

            <div class="time-filter-card">
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="week">本周</button>
                    <button class="filter-btn" data-filter="month">本月</button>
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
                <div class="stat-card"><div class="stat-title">交易次数</div><div class="stat-value" id="transactionCount">0</div><div class="stat-sub" id="countSub">笔交易</div></div>
            </div>

            <div class="chart-container">
                <div class="chart-title">收支趋势<span style="font-size:12px;font-weight:normal;">近7天趋势</span></div>
                <canvas id="trendChart" style="height: 300px;"></canvas>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="chart-container"><div class="chart-title">支出分类占比</div><canvas id="categoryChart" style="height: 280px;"></canvas></div>
                </div>
                <div class="col-lg-6">
                    <div class="chart-container"><div class="chart-title">收入分类占比</div><canvas id="incomeCategoryChart" style="height: 280px;"></canvas></div>
                </div>
            </div>

            <div class="rank-container">
                <div class="rank-title"><i class="fas fa-trophy"></i> 支出排行榜 TOP5</div>
                <div id="expenseRankList"></div>
            </div>

            <div class="chart-container">
                <div class="chart-title">账户支出分布</div>
                <canvas id="accountChart" style="height: 280px;"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-title">月度对比</div>
                <canvas id="monthlyChart" style="height: 300px;"></canvas>
            </div>

            <div class="export-btn" onclick="exportReport()"><i class="fas fa-download"></i> 导出统计报表（Excel）</div>
        </div>
    </div>
</div>

@include('coms.footer', ['imports' => ['js/statistics.js']])
