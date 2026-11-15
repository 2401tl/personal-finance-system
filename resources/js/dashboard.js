// 仪表盘脚本
const mockData = {
    month: { income: 8520, expense: 3280, balance: 5240, incomeSub: '本月收入', expenseSub: '本月支出', balanceSub: '本月结余' },
    year: { income: 68520, expense: 28340, balance: 40180, incomeSub: '本年收入', expenseSub: '本年支出', balanceSub: '本年结余' },
    all: { income: 125680, expense: 45680, balance: 80000, incomeSub: '累计收入', expenseSub: '累计支出', balanceSub: '累计结余' }
};

const trendData = {
    month: { income: [1200, 800, 1500, 600, 2000, 1000, 800], expense: [500, 600, 450, 700, 550, 400, 480], labels: ['1/20', '1/21', '1/22', '1/23', '1/24', '1/25', '今天'] },
    year: { income: [8500, 7800, 9200, 8800, 9500, 8520], expense: [3200, 2900, 3500, 3100, 3800, 3280], labels: ['1月', '2月', '3月', '4月', '5月', '6月'] }
};

const bills = [
    { icon: 'fa-utensils', color: '#ff6b6b', category: '餐饮', note: '午餐', amount: 68, date: '今天', type: 'expense' },
    { icon: 'fa-bus', color: '#4ecdc4', category: '交通', note: '地铁', amount: 12, date: '今天', type: 'expense' },
    { icon: 'fa-shopping-bag', color: '#ffa500', category: '购物', note: '超市', amount: 299, date: '昨天', type: 'expense' },
    { icon: 'fa-wallet', color: '#28a745', category: '工资', note: '1月工资', amount: 8000, date: '2024-01-15', type: 'income' }
];

let currentFilter = 'month', trendChart, categoryChart;

flatpickr("#startDate", { locale: "zh", dateFormat: "Y-m-d" });
flatpickr("#endDate", { locale: "zh", dateFormat: "Y-m-d" });

function updateStatistics(filter, startDate = '', endDate = '') {
    let data = mockData[filter] || mockData.month;
    document.getElementById('totalIncome').innerHTML = `¥${data.income.toFixed(2)}`;
    document.getElementById('totalExpense').innerHTML = `¥${data.expense.toFixed(2)}`;
    document.getElementById('totalBalance').innerHTML = `¥${data.balance.toFixed(2)}`;
    document.getElementById('incomeSub').innerHTML = data.incomeSub;
    document.getElementById('expenseSub').innerHTML = data.expenseSub;
    document.getElementById('balanceSub').innerHTML = data.balanceSub;
}

function updateTrendChart(filter) {
    let trend = trendData[filter] || trendData.month;
    trendChart.data.datasets[0].data = trend.income;
    trendChart.data.datasets[1].data = trend.expense;
    trendChart.data.labels = trend.labels;
    trendChart.update();
}

function updateBills() {
    const container = document.getElementById('recentBillsList');
    container.innerHTML = bills.map(bill => `
        <div class="bill-item">
            <div class="d-flex align-items-center">
                <div class="bill-icon" style="background: ${bill.type === 'income' ? '#e8f5e8' : '#ffe8e8'};">
                    <i class="fas ${bill.icon}" style="color: ${bill.color};"></i>
                </div>
                <div class="bill-info">
                    <div class="bill-category">${bill.category}</div>
                    <div class="bill-date">${bill.date}</div>
                </div>
            </div>
            <div class="bill-amount ${bill.type === 'income' ? 'income-color' : 'expense-color'}">
                ${bill.type === 'income' ? '+' : '-'}¥${bill.amount.toFixed(2)}
            </div>
        </div>
    `).join('');
}

function applyFilter(filter, startDate = '', endDate = '') {
    currentFilter = filter;
    updateStatistics(filter, startDate, endDate);
    if (filter !== 'range') updateTrendChart(filter);
}

function initCharts() {
    const ctx1 = document.getElementById('trendChart').getContext('2d');
    trendChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: trendData.month.labels,
            datasets: [
                { label: '收入', data: trendData.month.income, borderColor: '#28a745', backgroundColor: 'rgba(40,167,69,0.1)', tension: 0.4, fill: true },
                { label: '支出', data: trendData.month.expense, borderColor: '#dc3545', backgroundColor: 'rgba(220,53,69,0.1)', tension: 0.4, fill: true }
            ]
        },
        options: { responsive: true, maintainAspectRatio: true }
    });
    
    const ctx2 = document.getElementById('categoryChart').getContext('2d');
    categoryChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['餐饮', '购物', '交通', '娱乐', '其他'],
            datasets: [{ data: [1280, 890, 450, 320, 340], backgroundColor: ['#ff6b6b', '#ffa500', '#4ecdc4', '#667eea', '#95a5a6'] }]
        },
        options: { responsive: true, maintainAspectRatio: true }
    });
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
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    if (!startDate || !endDate) { showToast('请选择日期范围', 'error'); return; }
    applyFilter('range', startDate, endDate);
});

initCharts();
applyFilter('month');
updateBills();