// 账单管理脚本
const categoriesData = {
    expense: { parentCategories: [
        { id: 1, name: '餐饮', icon: 'fa-utensils', color: '#ff6b6b', subcategories: [{ id: 101, name: '早餐' }, { id: 102, name: '午餐' }, { id: 103, name: '晚餐' }] },
        { id: 2, name: '交通', icon: 'fa-bus', color: '#4ecdc4', subcategories: [{ id: 201, name: '地铁' }, { id: 202, name: '公交' }, { id: 203, name: '打车' }] },
        { id: 3, name: '购物', icon: 'fa-shopping-bag', color: '#ffa500', subcategories: [{ id: 301, name: '服装' }, { id: 302, name: '数码' }] },
        { id: 4, name: '娱乐', icon: 'fa-film', color: '#9b59b6', subcategories: [{ id: 401, name: '电影' }, { id: 402, name: '游戏' }] }
    ]},
    income: { parentCategories: [
        { id: 1, name: '工资', icon: 'fa-wallet', color: '#28a745', subcategories: [{ id: 101, name: '基本工资' }, { id: 102, name: '加班费' }] },
        { id: 2, name: '奖金', icon: 'fa-gift', color: '#28a745', subcategories: [{ id: 201, name: '年终奖' }] },
        { id: 3, name: '投资', icon: 'fa-chart-line', color: '#28a745', subcategories: [{ id: 301, name: '股票' }, { id: 302, name: '基金' }] }
    ]}
};

let transactions = [
    { id: 1, date: '2024-01-25', type: 'expense', parentId: 1, parentName: '餐饮', subId: 102, subName: '午餐', account: '微信支付', note: '麦当劳', amount: 68, icon: 'fa-utensil-spoon', color: '#ff6b6b' },
    { id: 2, date: '2024-01-25', type: 'expense', parentId: 2, parentName: '交通', subId: 201, subName: '地铁', account: '支付宝', note: '通勤', amount: 12, icon: 'fa-subway', color: '#4ecdc4' },
    { id: 3, date: '2024-01-24', type: 'expense', parentId: 3, parentName: '购物', subId: 301, subName: '服装', account: '银行卡', note: '优衣库', amount: 299, icon: 'fa-tshirt', color: '#ffa500' },
    { id: 4, date: '2024-01-20', type: 'income', parentId: 1, parentName: '工资', subId: 101, subName: '基本工资', account: '银行卡', note: '1月工资', amount: 8000, icon: 'fa-wallet', color: '#28a745' }
];

let currentData = [...transactions], currentPage = 1, pageSize = 10, currentDeleteId = null, currentModalType = 'expense';

flatpickr("#modalDate", { locale: "zh", dateFormat: "Y-m-d", defaultDate: new Date() });
flatpickr("#dateRangeFilter", { locale: "zh", mode: "range", dateFormat: "Y-m-d" });

function updateSubCategorySelect() {
    const parentId = parseInt(document.getElementById('parentCategorySelect').value);
    const categories = categoriesData[currentModalType].parentCategories;
    const parent = categories.find(p => p.id === parentId);
    const subSelect = document.getElementById('subCategorySelect');
    subSelect.innerHTML = parent ? parent.subcategories.map(sub => `<option value="${sub.id}">${sub.name}</option>`).join('') : '<option value="">请先选择一级分类</option>';
}

function switchModalType(type) {
    currentModalType = type;
    document.querySelectorAll('.type-option-modal').forEach(opt => opt.classList.remove('active'));
    document.querySelector(`.type-option-modal[data-type="${type}"]`).classList.add('active');
    const categories = categoriesData[type].parentCategories;
    document.getElementById('parentCategorySelect').innerHTML = '<option value="">请选择</option>' + categories.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');
    document.getElementById('subCategorySelect').innerHTML = '<option value="">请先选择一级分类</option>';
}

function openAddModal() {
    document.getElementById('modalAmount').value = '';
    document.getElementById('modalNote').value = '';
    switchModalType('expense');
    new bootstrap.Modal(document.getElementById('addBillModal')).show();
}

function saveBill() {
    const amount = parseFloat(document.getElementById('modalAmount').value);
    const date = document.getElementById('modalDate').value;
    const account = document.getElementById('modalAccount').value;
    const note = document.getElementById('modalNote').value;
    const parentId = parseInt(document.getElementById('parentCategorySelect').value);
    const subId = parseInt(document.getElementById('subCategorySelect').value);
    
    if (!amount || amount <= 0) { showToast('请输入有效金额', 'error'); return; }
    if (!date) { showToast('请选择日期', 'error'); return; }
    if (!parentId) { showToast('请选择一级分类', 'error'); return; }
    if (!subId) { showToast('请选择二级分类', 'error'); return; }
    
    const parent = categoriesData[currentModalType].parentCategories.find(p => p.id === parentId);
    const sub = parent.subcategories.find(s => s.id === subId);
    const newId = Math.max(...transactions.map(t => t.id), 0) + 1;
    transactions.unshift({ id: newId, date, type: currentModalType, parentId, parentName: parent.name, subId, subName: sub.name, account, note: note || '-', amount, icon: sub.icon || parent.icon, color: parent.color });
    resetFilters();
    bootstrap.Modal.getInstance(document.getElementById('addBillModal')).hide();
    showToast('添加成功！', 'success');
}

function renderList() {
    const start = (currentPage - 1) * pageSize, end = start + pageSize, pageData = currentData.slice(start, end);
    const container = document.getElementById('transactionList');
    if (pageData.length === 0) { container.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>暂无账单记录</p></div>'; return; }
    container.innerHTML = pageData.map(item => `
        <div class="transaction-item">
            <div class="transaction-category">
                <div class="category-icon" style="background: ${item.color}20;"><i class="fas ${item.icon}" style="color: ${item.color};"></i></div>
                <div class="category-info"><div class="category-name">${item.subName}</div><div class="category-path">${item.parentName}</div>${item.note !== '-' ? `<div class="transaction-note">${item.note}</div>` : ''}</div>
            </div>
            <div class="transaction-date">${item.date}</div>
            <div class="transaction-account">${item.account}</div>
            <div class="transaction-amount ${item.type === 'income' ? 'amount-income' : 'amount-expense'}">${item.type === 'income' ? '+' : '-'}¥${item.amount.toFixed(2)}</div>
            <div class="action-btns"><button class="btn-edit" onclick="editBill(${item.id})"><i class="fas fa-edit"></i></button><button class="btn-delete" onclick="showDeleteModal(${item.id})"><i class="fas fa-trash"></i></button></div>
        </div>
    `).join('');
}

function updateStats() {
    const totalIncome = currentData.filter(t => t.type === 'income').reduce((s, i) => s + i.amount, 0);
    const totalExpense = currentData.filter(t => t.type === 'expense').reduce((s, i) => s + i.amount, 0);
    document.getElementById('totalIncome').innerHTML = `¥${totalIncome.toFixed(2)}`;
    document.getElementById('totalExpense').innerHTML = `¥${totalExpense.toFixed(2)}`;
    document.getElementById('totalBalance').innerHTML = `¥${(totalIncome - totalExpense).toFixed(2)}`;
}

function updatePagination() {
    const totalPages = Math.ceil(currentData.length / pageSize);
    const start = (currentPage - 1) * pageSize + 1, end = Math.min(currentPage * pageSize, currentData.length);
    document.getElementById('recordsInfo').innerHTML = `显示 ${start}-${end} 条，共 ${currentData.length} 条`;
    if (totalPages <= 1) { document.getElementById('pagination').innerHTML = ''; return; }
    let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="changePage(${currentPage - 1})">上一页</a></li>`;
    for (let i = 1; i <= Math.min(totalPages, 5); i++) html += `<li class="page-item ${currentPage === i ? 'active' : ''}"><a class="page-link" href="#" onclick="changePage(${i})">${i}</a></li>`;
    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="changePage(${currentPage + 1})">下一页</a></li>`;
    document.getElementById('pagination').innerHTML = html;
}

function changePage(page) { const totalPages = Math.ceil(currentData.length / pageSize); if (page < 1 || page > totalPages) return; currentPage = page; renderList(); updatePagination(); }

function searchTransactions() {
    const dateRange = document.getElementById('dateRangeFilter').value, type = document.getElementById('typeFilter').value, parentId = document.getElementById('parentCategoryFilter').value;
    let filtered = [...transactions];
    if (dateRange) { const dates = dateRange.split(' 至 '); if (dates.length === 2) filtered = filtered.filter(i => i.date >= dates[0] && i.date <= dates[1]); }
    if (type !== 'all') filtered = filtered.filter(i => i.type === type);
    if (parentId !== 'all') filtered = filtered.filter(i => i.parentId === parseInt(parentId));
    currentData = filtered; currentPage = 1; renderList(); updateStats(); updatePagination();
}

function resetFilters() { document.getElementById('dateRangeFilter').value = ''; document.getElementById('typeFilter').value = 'all'; document.getElementById('parentCategoryFilter').value = 'all'; currentData = [...transactions]; currentPage = 1; renderList(); updateStats(); updatePagination(); }

function updateFilterSelects() {
    const allParents = [...categoriesData.expense.parentCategories, ...categoriesData.income.parentCategories];
    document.getElementById('parentCategoryFilter').innerHTML = '<option value="all">全部分类</option>' + allParents.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
}

function showDeleteModal(id) { currentDeleteId = id; new bootstrap.Modal(document.getElementById('deleteModal')).show(); }
document.getElementById('confirmDeleteBtn').addEventListener('click', function() { if (currentDeleteId) { transactions = transactions.filter(t => t.id !== currentDeleteId); resetFilters(); bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide(); showToast('删除成功！', 'success'); } });
function editBill(id) { showToast('编辑功能开发中...', 'info'); }

document.getElementById('searchBtn').addEventListener('click', searchTransactions);
document.getElementById('resetBtn').addEventListener('click', resetFilters);

updateFilterSelects();
resetFilters();