// 统计分析脚本
const mockData = {
    week: { income: 3240, expense: 1280, balance: 1960, count: 23 },
    month: { income: 8520, expense: 3280, balance: 5240, count: 48 },
    year: { income: 68520, expense: 28340, balance: 40180, count: 156 },
    all: { income: 125680, expense: 45680, balance: 80000, count: 268 }
};

const trendData = {
    week: { income: [450, 520, 380, 620, 480, 560, 3240], expense: [180, 220, 150, 190, 210, 180, 1280], labels: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'] },
    month: { income: [1200, 800, 1500, 600, 2000, 1000, 800], expense: [500, 600, 450, 700, 550, 400, 480], labels: ['1/20', '1/21', '1/22', '1/23', '1/24', '1/25', '今天'] },
    year: { income: [8500, 7800, 9200, 8800, 9500, 8520], expense: [3200, 2900, 3500, 3100, 3800, 3280], labels: ['1月', '2月', '3月', '4月', '5月', '6月'] }
};

const categoryData = {
    expense: { labels: ['餐饮', '购物', '交通', '娱乐', '医疗', '教育', '住房'], data: [1280, 890, 450, 320, 180, 240, 500] },
    income: { labels: ['工资', '奖金', '投资', '兼职', '红包'], data: [8000, 3000, 1500, 800, 500] }
};

const accountData = { labels: ['招商银行卡', '支付宝', '微信支付', '现金'], data: [2350, 1890, 980, 450] };
const monthlyCompareData = { labels: ['7月', '8月', '9月', '10月', '11月', '12月'], income: [7800, 8200, 9100, 8800, 9500, 8520], expense: [3100, 2900, 3500, 3300, 3800, 3280] };
const rankData = [{ name: '餐饮', amount: 1280, percent: 39.0 }, { name: '购物', amount: 890, percent: 27.1 }, { name: '交通', amount: 450, percent: 13.7 }, { name: '住房', amount: 500, percent: 15.2 }, { name: '娱乐', amount: 320, percent: 9.8 }];

let currentFilter = 'month', trendChart, categoryChart, incomeCategoryChart, accountChart, monthlyChart;

flatpickr("#startDate", { locale: "zh", dateFormat: "Y-m-d" });
flatpickr("#endDate", { locale: "zh", dateFormat: "Y-m-d" });

function updateStatistics(filter, startDate = '', endDate = '') {
    let data = mockData[filter] || mockData.month;
    document.getElementById('totalIncome').innerHTML = `¥${data.income.toFixed(2)}`;
    document.getElementById('totalExpense').innerHTML = `¥${data.expense.toFixed(2)}`;
    document.getElementById('totalBalance').innerHTML = `¥${data.balance.toFixed(2)}`;
    document.getElementById('transactionCount').innerHTML = data.count;
    const subText = filter === 'range' ? `${startDate}至${endDate}` : { week: '本周', month: '本月', year: '本年', all: '累计' }[filter];
    document.getElementById('incomeSub').innerHTML = `${subText}收入`;
    document.getElementById('expenseSub').innerHTML = `${subText}支出`;
    document.getElementById('balanceSub').innerHTML = `${subText}结余`;
    document.getElementById('countSub').innerHTML = `${subText}交易`;
}

function updateTrendChart(filter) { let trend = trendData[filter] || trendData.month; trendChart.data.datasets[0].data = trend.income; trendChart.data.datasets[1].data = trend.expense; trendChart.data.labels = trend.labels; trendChart.update(); }
function updateCategoryChart() { categoryChart.data.datasets[0].data = categoryData.expense.data; categoryChart.update(); }
function updateIncomeCategoryChart() { incomeCategoryChart.data.datasets[0].data = categoryData.income.data; incomeCategoryChart.update(); }
function updateAccountChart() { accountChart.data.datasets[0].data = accountData.data; accountChart.update(); }
function updateMonthlyChart() { monthlyChart.data.datasets[0].data = monthlyCompareData.income; monthlyChart.data.datasets[1].data = monthlyCompareData.expense; monthlyChart.update(); }

function updateRankList() {
    document.getElementById('expenseRankList').innerHTML = rankData.map((item, index) => `<div class="rank-item"><div class="rank-left"><div class="rank-number ${index === 0 ? 'top1' : index === 1 ? 'top2' : index === 2 ? 'top3' : ''}">${index + 1}</div><div class="rank-category">${item.name}</div></div><div class="rank-amount">¥${item.amount.toFixed(2)}</div><div class="rank-percent">${item.percent}%</div></div>`).join('');
}

function initCharts() {
    trendChart = new Chart(document.getElementById('trendChart'), { type: 'line', data: { labels: trendData.month.labels, datasets: [{ label: '收入', data: trendData.month.income, borderColor: '#28a745', backgroundColor: 'rgba(40,167,69,0.1)', tension: 0.4, fill: true }, { label: '支出', data: trendData.month.expense, borderColor: '#dc3545', backgroundColor: 'rgba(220,53,69,0.1)', tension: 0.4, fill: true }] }, options: { responsive: true } });
    categoryChart = new Chart(document.getElementById('categoryChart'), { type: 'doughnut', data: { labels: categoryData.expense.labels, datasets: [{ data: categoryData.expense.data, backgroundColor: ['#ff6b6b', '#ffa500', '#4ecdc4', '#9b59b6', '#e74c3c', '#3498db', '#f39c12'] }] }, options: { responsive: true } });
    incomeCategoryChart = new Chart(document.getElementById('incomeCategoryChart'), { type: 'doughnut', data: { labels: categoryData.income.labels, datasets: [{ data: categoryData.income.data, backgroundColor: ['#28a745', '#20c997', '#17a2b8', '#6f42c1', '#fd7e14'] }] }, options: { responsive: true } });
    accountChart = new Chart(document.getElementById('accountChart'), { type: 'bar', data: { labels: accountData.labels, datasets: [{ label: '支出金额', data: accountData.data, backgroundColor: '#667eea', borderRadius: 8 }] }, options: { responsive: true, scales: { y: { beginAtZero: true } } } });
    monthlyChart = new Chart(document.getElementById('monthlyChart'), { type: 'bar', data: { labels: monthlyCompareData.labels, datasets: [{ label: '收入', data: monthlyCompareData.income, backgroundColor: 'rgba(40,167,69,0.7)', borderRadius: 8 }, { label: '支出', data: monthlyCompareData.expense, backgroundColor: 'rgba(220,53,69,0.7)', borderRadius: 8 }] }, options: { responsive: true } });
}

function applyFilter(filter, startDate = '', endDate = '') {
    currentFilter = filter;
    updateStatistics(filter, startDate, endDate);
    if (filter !== 'range') updateTrendChart(filter);
}

document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const filter = this.dataset.filter;
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const rangePicker = document.getElementById('dateRangePicker');
        filter === 'range' ? rangePicker.classList.add('show') : rangePicker.classList.remove('show');
        if (filter !== 'range') applyFilter(filter);
    });
});

document.getElementById('applyRange').addEventListener('click', function() {
    const startDate = document.getElementById('startDate').value, endDate = document.getElementById('endDate').value;
    if (!startDate || !endDate) { showToast('请选择日期范围', 'error'); return; }
    applyFilter('range', startDate, endDate);
});

function exportReport() { showToast('正在生成统计报表...', 'success'); setTimeout(() => showToast('报表导出成功！', 'success'), 1500); }

initCharts();
updateRankList();
applyFilter('month');