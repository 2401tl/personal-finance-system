// ============================================
// 个人记账系统 - 公共函数
// ============================================

// 显示提示消息
function showToast(message, type = 'success') {
    const bgColor = type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8';
    const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
    
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; top: 20px; right: 20px; padding: 12px 20px;
        border-radius: 10px; background: ${bgColor}; color: white;
        z-index: 9999; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        max-width: 300px; animation: slideInRight 0.3s ease;
    `;
    toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100px)';
        setTimeout(() => toast.remove(), 3000);
    }, 3000);
}

// 添加动画样式
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(100px); }
        to { opacity: 1; transform: translateX(0); }
    }
`;
document.head.appendChild(style);

// 格式化金额
function formatMoney(amount, currency = '¥') {
    return `${currency}${amount.toFixed(2)}`;
}

// 格式化日期
function formatDate(date, format = 'YYYY-MM-DD') {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    
    const formats = {
        'YYYY-MM-DD': `${year}-${month}-${day}`,
        'MM/DD/YYYY': `${month}/${day}/${year}`,
        'DD/MM/YYYY': `${day}/${month}/${year}`,
        'YYYY年MM月DD日': `${year}年${month}月${day}日`
    };
    return formats[format] || `${year}-${month}-${day}`;
}

// 获取当前时间范围
function getDateRange(range) {
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    
    switch(range) {
        case 'today':
            return { start: today, end: today };
        case 'week':
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay() + (today.getDay() === 0 ? -6 : 1));
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekStart.getDate() + 6);
            return { start: weekStart, end: weekEnd };
        case 'month':
            return { start: new Date(now.getFullYear(), now.getMonth(), 1), end: new Date(now.getFullYear(), now.getMonth() + 1, 0) };
        case 'year':
            return { start: new Date(now.getFullYear(), 0, 1), end: new Date(now.getFullYear(), 11, 31) };
        default:
            return { start: null, end: null };
    }
}

// 导出CSV
function exportToCSV(data, filename, headers) {
    const csvRows = [headers.join(',')];
    for (const row of data) {
        const values = headers.map(header => `"${String(row[header] || '').replace(/"/g, '""')}"`);
        csvRows.push(values.join(','));
    }
    const blob = new Blob(["\uFEFF" + csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.href = url;
    link.setAttribute('download', `${filename}_${new Date().toISOString().slice(0,10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// 本地存储
const storage = {
    set(key, value) { localStorage.setItem(key, JSON.stringify(value)); },
    get(key) { const v = localStorage.getItem(key); return v ? JSON.parse(v) : null; },
    remove(key) { localStorage.removeItem(key); },
    clear() { localStorage.clear(); }
};

// API 请求
async function apiRequest(url, options = {}) {
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'X-Requested-With': 'XMLHttpRequest',
        ...options.headers,
    };

    const response = await fetch(url, {
        credentials: 'same-origin',
        ...options,
        headers,
    });

    let data = {};
    try {
        data = await response.json();
    } catch (e) {
        throw new Error('服务器响应异常');
    }

    if (!response.ok) {
        const message = data.message
            || (data.errors ? Object.values(data.errors).flat()[0] : null)
            || '请求失败';
        throw new Error(message);
    }

    return data;
}
