function switchTab(tab) {
    const loginPane = document.getElementById('loginPane');
    const registerPane = document.getElementById('registerPane');
    const loginBtn = document.querySelectorAll('.tab-btn')[0];
    const registerBtn = document.querySelectorAll('.tab-btn')[1];

    if (tab === 'login') {
        loginPane.classList.add('active');
        registerPane.classList.remove('active');
        loginBtn.classList.add('active');
        registerBtn.classList.remove('active');
    } else {
        loginPane.classList.remove('active');
        registerPane.classList.add('active');
        loginBtn.classList.remove('active');
        registerBtn.classList.add('active');
    }
}

document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const username = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value;
    const remember = document.getElementById('rememberMe').checked;

    if (!username) { showToast('请输入手机号', 'error'); return; }
    if (!/^1[3-9]\d{9}$/.test(username)) { showToast('请输入有效的11位手机号', 'error'); return; }
    if (!password) { showToast('请输入密码', 'error'); return; }
    if (password.length < 6) { showToast('密码至少6位', 'error'); return; }

    try {
        await apiRequest('/api/login', {
            method: 'POST',
            body: JSON.stringify({ username, password, remember }),
        });
        showToast('登录成功！', 'success');
        setTimeout(() => window.location.href = '/dashboard', 1000);
    } catch (err) {
        showToast(err.message, 'error');
    }
});

document.getElementById('registerForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const username = document.getElementById('regUsername').value.trim();
    const email = document.getElementById('regEmail').value.trim();
    const phone = document.getElementById('regPhone').value.trim();
    const password = document.getElementById('regPassword').value;
    const confirm = document.getElementById('regConfirmPassword').value;
    const agree = document.getElementById('agreeTerms').checked;

    if (!username) { showToast('请输入用户名', 'error'); return; }
    if (username.length < 4 || username.length > 20) { showToast('用户名需在4-20个字符之间', 'error'); return; }
    if (!email) { showToast('请输入邮箱地址', 'error'); return; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showToast('请输入有效的邮箱地址', 'error'); return; }
    if (!phone) { showToast('请输入手机号', 'error'); return; }
    if (!/^1[3-9]\d{9}$/.test(phone)) { showToast('请输入有效的11位手机号', 'error'); return; }
    if (!password) { showToast('请输入密码', 'error'); return; }
    if (password.length < 6) { showToast('密码至少6位', 'error'); return; }
    if (password !== confirm) { showToast('两次密码不一致', 'error'); return; }
    if (!agree) { showToast('请同意用户协议', 'error'); return; }

    try {
        await apiRequest('/api/register', {
            method: 'POST',
            body: JSON.stringify({ username, email, phone, password }),
        });
        showToast('注册成功！请登录', 'success');
        document.getElementById('registerForm').reset();
        setTimeout(() => switchTab('login'), 1500);
    } catch (err) {
        showToast(err.message, 'error');
    }
});
