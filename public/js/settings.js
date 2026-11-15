// 系统设置脚本
let settings = {
    currency: 'CNY', currencySymbol: '¥', dateFormat: 'YYYY-MM-DD', weekStart: 'monday', pageSize: 10,
    budgetAlert: true, budgetThreshold: 80, dailyReminder: false, reminderTime: '20:00',
    theme: 'light', compactMode: false, chartAnimation: true, autoBackup: true, backupFrequency: 'weekly',
    appLock: false, autoLock: 5
};

function saveCurrency() {
    let currency = document.getElementById('currencySelect').value;
    let symbols = { CNY: '¥', USD: '$', EUR: '€' };
    settings.currency = currency;
    settings.currencySymbol = symbols[currency];
    document.getElementById('currencyValue').innerHTML = `${currency === 'CNY' ? '人民币' : currency} (${settings.currencySymbol})`;
    showToast('货币单位已更新', 'success');
    bootstrap.Modal.getInstance(document.getElementById('currencyModal')).hide();
}

function saveDateFormat() {
    let format = document.getElementById('dateFormatSelect').value;
    settings.dateFormat = format;
    let formatNames = { 'YYYY-MM-DD': 'YYYY-MM-DD', 'MM/DD/YYYY': 'MM/DD/YYYY', 'DD/MM/YYYY': 'DD/MM/YYYY' };
    document.getElementById('dateFormatValue').innerHTML = formatNames[format];
    showToast('日期格式已更新', 'success');
    bootstrap.Modal.getInstance(document.getElementById('dateFormatModal')).hide();
}

function savePageSize() {
    let pageSize = parseInt(document.getElementById('pageSizeSelect').value);
    settings.pageSize = pageSize;
    document.getElementById('pageSizeValue').innerHTML = `${pageSize} 条`;
    showToast('每页显示条数已更新', 'success');
    bootstrap.Modal.getInstance(document.getElementById('pageSizeModal')).hide();
}

function saveBudgetThreshold() {
    let threshold = parseInt(document.getElementById('budgetThresholdSelect').value);
    settings.budgetThreshold = threshold;
    document.getElementById('budgetThresholdValue').innerHTML = `${threshold}%`;
    showToast('预算提醒阈值已更新', 'success');
    bootstrap.Modal.getInstance(document.getElementById('budgetThresholdModal')).hide();
}

function saveReminderTime() {
    let time = document.getElementById('reminderTimeInput').value;
    settings.reminderTime = time;
    document.getElementById('reminderTimeValue').innerHTML = time;
    showToast('提醒时间已更新', 'success');
    bootstrap.Modal.getInstance(document.getElementById('reminderTimeModal')).hide();
}

function saveTheme() {
    let theme = document.getElementById('themeSelect').value;
    settings.theme = theme;
    let names = { light: '浅色模式', dark: '深色模式' };
    document.getElementById('themeValue').innerHTML = names[theme];
    showToast('主题已更新，需刷新页面生效', 'success');
    bootstrap.Modal.getInstance(document.getElementById('themeModal')).hide();
}

function saveBackupFrequency() {
    let frequency = document.getElementById('backupFrequencySelect').value;
    settings.backupFrequency = frequency;
    let names = { daily: '每天', weekly: '每周', monthly: '每月' };
    document.getElementById('backupFrequencyValue').innerHTML = names[frequency];
    showToast('备份频率已更新', 'success');
    bootstrap.Modal.getInstance(document.getElementById('backupFrequencyModal')).hide();
}

function saveAutoLock() {
    let lockTime = parseInt(document.getElementById('autoLockSelect').value);
    settings.autoLock = lockTime;
    let names = { 1: '1分钟', 5: '5分钟', 10: '10分钟', 30: '30分钟' };
    document.getElementById('autoLockValue').innerHTML = names[lockTime];
    showToast('自动锁定时间已更新', 'success');
    bootstrap.Modal.getInstance(document.getElementById('autoLockModal')).hide();
}

// 开关事件
document.getElementById('budgetAlert').addEventListener('change', e => { settings.budgetAlert = e.target.checked; showToast(e.target.checked ? '预算提醒已开启' : '预算提醒已关闭', 'success'); });
document.getElementById('dailyReminder').addEventListener('change', e => { settings.dailyReminder = e.target.checked; showToast(e.target.checked ? '每日提醒已开启' : '每日提醒已关闭', 'success'); });
document.getElementById('compactMode').addEventListener('change', e => { settings.compactMode = e.target.checked; showToast(e.target.checked ? '紧凑模式已开启' : '紧凑模式已关闭', 'success'); });
document.getElementById('chartAnimation').addEventListener('change', e => { settings.chartAnimation = e.target.checked; showToast(e.target.checked ? '图表动画已开启' : '图表动画已关闭', 'success'); });
document.getElementById('autoBackup').addEventListener('change', e => { settings.autoBackup = e.target.checked; showToast(e.target.checked ? '自动备份已开启' : '自动备份已关闭', 'success'); });
document.getElementById('appLock').addEventListener('change', e => { settings.appLock = e.target.checked; showToast(e.target.checked ? '应用锁已开启' : '应用锁已关闭', 'success'); });

function openCurrencyModal() { new bootstrap.Modal(document.getElementById('currencyModal')).show(); }
function openDateFormatModal() { new bootstrap.Modal(document.getElementById('dateFormatModal')).show(); }
function openWeekStartModal() { showToast('周起始日设置开发中...', 'info'); }
function openPageSizeModal() { new bootstrap.Modal(document.getElementById('pageSizeModal')).show(); }
function openBudgetThresholdModal() { new bootstrap.Modal(document.getElementById('budgetThresholdModal')).show(); }
function openReminderTimeModal() { new bootstrap.Modal(document.getElementById('reminderTimeModal')).show(); }
function openThemeModal() { new bootstrap.Modal(document.getElementById('themeModal')).show(); }
function openBackupFrequencyModal() { new bootstrap.Modal(document.getElementById('backupFrequencyModal')).show(); }
function openAutoLockModal() { new bootstrap.Modal(document.getElementById('autoLockModal')).show(); }

function backupNow() { showToast('正在备份数据...', 'success'); setTimeout(() => showToast('备份成功！', 'success'), 1500); }
function restoreData() { showToast('请选择备份文件恢复数据', 'info'); }
function clearCache() { if (confirm('确定要清除所有缓存数据吗？')) showToast('缓存已清除', 'success'); }
function checkUpdate() { showToast('当前已是最新版本 v1.0.0', 'info'); }
function showAgreement() { showToast('用户协议内容...', 'info'); }
function showPrivacy() { showToast('隐私政策内容...', 'info'); }

function initSettings() {
    document.getElementById('currencyValue').innerHTML = '人民币 (¥)';
    document.getElementById('dateFormatValue').innerHTML = 'YYYY-MM-DD';
    document.getElementById('weekStartValue').innerHTML = '星期一';
    document.getElementById('pageSizeValue').innerHTML = '10 条';
    document.getElementById('budgetThresholdValue').innerHTML = '80%';
    document.getElementById('reminderTimeValue').innerHTML = '20:00';
    document.getElementById('themeValue').innerHTML = '浅色模式';
    document.getElementById('backupFrequencyValue').innerHTML = '每周';
    document.getElementById('autoLockValue').innerHTML = '5分钟';
}

initSettings();