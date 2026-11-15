// 个人中心脚本
let userData = { name: '小明', email: 'xiaoming@example.com', bio: '记录生活，掌控财务' };
let statsData = { days: 128, records: 156, categories: 24, monthlyAvg: 3280 };

function changePassword() {
    let oldPwd = document.getElementById('oldPassword').value;
    let newPwd = document.getElementById('newPassword').value;
    let confirmPwd = document.getElementById('confirmPassword').value;

    if (!oldPwd) { showToast('请输入当前密码', 'error'); return; }
    if (newPwd.length < 6) { showToast('新密码至少6位', 'error'); return; }
    if (newPwd !== confirmPwd) { showToast('两次密码不一致', 'error'); return; }

    showToast('密码修改成功！', 'success');
    bootstrap.Modal.getInstance(document.getElementById('passwordModal')).hide();
    document.getElementById('oldPassword').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
}

function saveProfile() {
    let newName = document.getElementById('editName').value.trim();
    let newBio = document.getElementById('editBio').value;

    if (newName) {
        userData.name = newName;
        document.getElementById('profileName').textContent = newName;
    }
    userData.bio = newBio;

    showToast('资料更新成功！', 'success');
    bootstrap.Modal.getInstance(document.getElementById('profileModal')).hide();
}

function openPasswordModal() { new bootstrap.Modal(document.getElementById('passwordModal')).show(); }
function openProfileModal() {
    document.getElementById('editName').value = userData.name;
    document.getElementById('editBio').value = userData.bio || '';
    new bootstrap.Modal(document.getElementById('profileModal')).show();
}
function openEmailModal() { showToast('邮箱绑定功能开发中...', 'info'); }
function openPhoneModal() { showToast('手机绑定功能开发中...', 'info'); }
function openCurrencyModal() { showToast('货币单位设置开发中...', 'info'); }
function openDateFormatModal() { showToast('日期格式设置开发中...', 'info'); }
function changeAvatar() {
    document.querySelector('#selectAvatar').click(); //打开文件选择
}
window.changeAvatar = changeAvatar;
document.querySelector('#selectAvatar').addEventListener('change', (e) => {
    //选择头像后, 1、预览, 2、提交
    let avatar = e.target.files[0];
    if (avatar.size > 10 * 1024 * 1024) {
        showToast('头像必须小于10M', 'error');
        return;
    }
    //预览
    let reader = new FileReader();
    reader.onload = (ex) => {
        document.querySelector('#avatarIcon').innerHTML = `<img src="${ex.target.result}" alt="">`;
    }
    reader.readAsDataURL(avatar);

    //提交到服务
    let fd = new FormData();
    fd.append('avatar', avatar);
    fetch('/user/avatar', {
        method: 'post',
        credentials: 'same-origin',
        body: fd
    }).then(res => res.json())
        .then(res => {
            if (res.code === 200) {
                showToast('头像修改成功', 'success');
            } else {
                showToast(res.message, 'error');
            }
        })
})
function exportData() { showToast('导出功能开发中...', 'success'); }
function backupData() { showToast('正在备份数据到云端...', 'success'); }
function clearData() {
    if (confirm('⚠️ 警告：此操作将清空所有账单记录，且不可恢复！确定要继续吗？')) {
        if (confirm('再次确认：真的要清空所有数据吗？')) {
            showToast('数据已清空', 'error');
        }
    }
}
function showAbout() { showToast('我的账本 v1.0.0\n用心记录每一笔，掌控财务自由', 'info'); }
function showFeedback() { showToast('意见反馈功能开发中...', 'info'); }

function updateStats() {
    document.getElementById('statDays').textContent = statsData.days;
    document.getElementById('statRecords').textContent = statsData.records;
    document.getElementById('statCategories').textContent = statsData.categories;
    document.getElementById('statMonthlyAvg').textContent = `¥${statsData.monthlyAvg.toLocaleString()}`;
}

updateStats();