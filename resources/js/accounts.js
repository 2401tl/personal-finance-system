// 账户管理脚本
let accounts = window.initialAccounts || [];

const icons = [
    { name: 'fa-credit-card', label: '银行卡' },
    { name: 'fa-alipay', label: '支付宝' },
    { name: 'fa-weixin', label: '微信' },
    { name: 'fa-money-bill-wave', label: '现金' },
    { name: 'fa-wallet', label: '钱包' }
];

const typeNames = { bank: '银行卡', alipay: '支付宝', wechat: '微信支付', cash: '现金' };

let currentDeleteId = null, editingAccountId = null, currentTransferFromId = null, selectedIcon = 'fa-credit-card';

function mapAccountFromApi(item) {
    return {
        id: item.id,
        name: item.name,
        type: item.type,
        typeName: item.type_name,
        balance: item.balance,
        icon: item.icon,
        note: item.note || '',
        transactionCount: item.transaction_count ?? 0,
        monthExpense: item.month_expense ?? 0,
    };
}

function renderIconPicker() {
    const container = document.getElementById('iconPicker');
    container.innerHTML = icons.map(icon => `
        <div class="icon-option" data-icon="${icon.name}" onclick="selectAccountIcon(this, '${icon.name}')">
            <i class="fas ${icon.name}" style="font-size:20px;"></i>
        </div>
    `).join('');
    document.querySelectorAll('.icon-option').forEach(opt => {
        if (opt.dataset.icon === selectedIcon) opt.classList.add('selected');
    });
}

function selectAccountIcon(element, icon) {
    document.querySelectorAll('.icon-option').forEach(opt => opt.classList.remove('selected'));
    element.classList.add('selected');
    selectedIcon = icon;
}

function renderAccounts() {
    const container = document.getElementById('accountsList');
    if (accounts.length === 0) {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-credit-card"></i><p>暂无账户</p><small>点击"添加账户"创建您的第一个账户</small></div>';
        return;
    }
    container.innerHTML = accounts.map(acc => `
        <div class="account-card">
            <div class="account-header">
                <div class="account-info">
                    <div class="account-icon" style="background:#f0f2ff;"><i class="fas ${acc.icon}" style="color:#667eea; font-size:24px;"></i></div>
                    <div><div class="account-name">${acc.name}</div><span class="account-type">${acc.typeName}</span></div>
                </div>
                <div><div class="balance-amount ${acc.balance >= 0 ? 'balance-positive' : 'balance-negative'}">¥${acc.balance.toFixed(2)}</div></div>
            </div>
            <div class="account-body">
                <div class="account-stats">
                    <div class="stat-item"><div class="stat-number">${acc.transactionCount}</div><div class="stat-label">交易笔数</div></div>
                    <div class="stat-item"><div class="stat-number">¥${acc.monthExpense.toFixed(2)}</div><div class="stat-label">本月支出</div></div>
                </div>
                ${acc.note ? `<div style="font-size:12px; color:#999; margin-bottom:15px;"><i class="fas fa-sticky-note"></i> ${acc.note}</div>` : ''}
                <div class="account-footer">
                    <button class="btn-transfer" onclick="openTransferModal(${acc.id})"><i class="fas fa-exchange-alt"></i> 转账</button>
                    <button class="btn-edit-account" onclick="editAccount(${acc.id})"><i class="fas fa-edit"></i> 编辑</button>
                    <button class="btn-delete-account" onclick="showDeleteModal(${acc.id})"><i class="fas fa-trash"></i> 删除</button>
                </div>
            </div>
        </div>
    `).join('');
}

function updateStats() {
    const totalAssets = accounts.reduce((s, a) => s + a.balance, 0);
    const totalMonthExpense = accounts.reduce((s, a) => s + a.monthExpense, 0);
    document.getElementById('totalAssets').innerHTML = `¥${totalAssets.toFixed(2)}`;
    document.getElementById('accountCount').innerHTML = accounts.length;
    const countSub = document.getElementById('accountCountSub');
    if (countSub) countSub.innerHTML = `共 ${accounts.length} 个账户`;
    document.getElementById('monthExpense').innerHTML = `¥${totalMonthExpense.toFixed(2)}`;
}

function openAddAccountModal() {
    editingAccountId = null;
    document.getElementById('modalTitle').innerHTML = '添加账户';
    document.getElementById('accountName').value = '';
    document.getElementById('accountType').value = 'bank';
    document.getElementById('accountBalance').value = '';
    document.getElementById('accountNote').value = '';
    selectedIcon = 'fa-credit-card';
    document.querySelectorAll('.icon-option').forEach(opt => {
        opt.classList.remove('selected');
        if (opt.dataset.icon === selectedIcon) opt.classList.add('selected');
    });
    new bootstrap.Modal(document.getElementById('accountModal')).show();
}

function editAccount(id) {
    let acc = accounts.find(a => a.id === id);
    if (acc) {
        editingAccountId = id;
        document.getElementById('modalTitle').innerHTML = '编辑账户';
        document.getElementById('accountName').value = acc.name;
        document.getElementById('accountType').value = acc.type;
        document.getElementById('accountBalance').value = acc.balance;
        document.getElementById('accountNote').value = acc.note || '';
        selectedIcon = acc.icon;
        document.querySelectorAll('.icon-option').forEach(opt => {
            opt.classList.remove('selected');
            if (opt.dataset.icon === selectedIcon) opt.classList.add('selected');
        });
        new bootstrap.Modal(document.getElementById('accountModal')).show();
    }
}

async function saveAccount() {
    const name = document.getElementById('accountName').value.trim();
    const type = document.getElementById('accountType').value;
    const balance = parseFloat(document.getElementById('accountBalance').value);
    const note = document.getElementById('accountNote').value;

    if (!name) { showToast('请输入账户名称', 'error'); return; }
    if (isNaN(balance)) { showToast('请输入有效金额', 'error'); return; }

    const payload = { name, type, balance, icon: selectedIcon, note };

    try {
        let res;
        if (editingAccountId) {
            res = await apiRequest(`/api/accounts/${editingAccountId}`, {
                method: 'PUT',
                body: JSON.stringify(payload),
            });
        } else {
            res = await apiRequest('/api/accounts', {
                method: 'POST',
                body: JSON.stringify(payload),
            });
        }

        const account = mapAccountFromApi(res.data);
        if (editingAccountId) {
            const idx = accounts.findIndex(a => a.id === editingAccountId);
            if (idx !== -1) {
                accounts[idx] = { ...accounts[idx], ...account, typeName: typeNames[account.type] };
            }
        } else {
            accounts.push({ ...account, typeName: typeNames[account.type], transactionCount: 0, monthExpense: 0 });
        }

        showToast(res.message || (editingAccountId ? '账户修改成功！' : '账户添加成功！'), 'success');
        bootstrap.Modal.getInstance(document.getElementById('accountModal')).hide();
        renderAccounts();
        updateStats();
    } catch (e) {
        showToast(e.message, 'error');
    }
}

function openTransferModal(fromId) {
    currentTransferFromId = fromId;
    document.getElementById('fromAccount').innerHTML = accounts.map(acc =>
        `<option value="${acc.id}" ${acc.id === fromId ? 'selected' : ''}>${acc.name} (¥${acc.balance.toFixed(2)})</option>`
    ).join('');
    document.getElementById('toAccount').innerHTML = accounts.filter(acc => acc.id !== fromId).map(acc =>
        `<option value="${acc.id}">${acc.name} (¥${acc.balance.toFixed(2)})</option>`
    ).join('');
    document.getElementById('transferAmount').value = '';
    document.getElementById('transferNote').value = '';
    new bootstrap.Modal(document.getElementById('transferModal')).show();
}

async function doTransfer() {
    const fromId = parseInt(document.getElementById('fromAccount').value);
    const toId = parseInt(document.getElementById('toAccount').value);
    const amount = parseFloat(document.getElementById('transferAmount').value);
    const note = document.getElementById('transferNote').value;

    if (fromId === toId) { showToast('不能转账到同一账户', 'error'); return; }
    if (!amount || amount <= 0) { showToast('请输入有效金额', 'error'); return; }

    try {
        const res = await apiRequest('/api/accounts/transfer', {
            method: 'POST',
            body: JSON.stringify({
                from_account_id: fromId,
                to_account_id: toId,
                amount,
                note,
            }),
        });

        const fromAcc = accounts.find(a => a.id === fromId);
        const toAcc = accounts.find(a => a.id === toId);
        if (fromAcc && toAcc) {
            fromAcc.balance -= amount;
            toAcc.balance += amount;
            fromAcc.monthExpense += amount;
        }

        showToast(res.message || `转账成功！从 ${fromAcc?.name} 转出 ¥${amount.toFixed(2)} 到 ${toAcc?.name}`, 'success');
        bootstrap.Modal.getInstance(document.getElementById('transferModal')).hide();
        renderAccounts();
        updateStats();
    } catch (e) {
        showToast(e.message, 'error');
    }
}

function showDeleteModal(id) {
    currentDeleteId = id;
    const acc = accounts.find(a => a.id === id);
    document.getElementById('deleteAccountName').innerHTML = acc ? acc.name : '';
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function () {
    if (!currentDeleteId) return;

    const id = currentDeleteId;
    const btn = this;
    btn.disabled = true;

    try {
        const res = await apiRequest(`/api/accounts/${id}`, { method: 'DELETE' });
        accounts = accounts.filter(a => a.id !== id);
        showToast(res.message || '账户删除成功！', 'success');
        renderAccounts();
        updateStats();
        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
        currentDeleteId = null;
    } catch (e) {
        showToast(e.message, 'error');
    } finally {
        btn.disabled = false;
    }
});

renderIconPicker();
updateStats();
