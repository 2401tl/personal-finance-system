@include('coms.header', [
    'title' => '登录|注册',
    'minimal' => true,
    'imports' => ['css/index.css'],
])

<div class="bg-decoration">
    <div class="bg-circle circle-1"></div>
    <div class="bg-circle circle-2"></div>
</div>

<div class="container">
    <div class="auth-card">
        <div class="brand-section">
            <div class="brand-icon"><i class="fas fa-coins"></i></div>
            <h1 class="brand-title">智慧记账，<br>掌控生活</h1>
            <p class="brand-desc">简单易用的个人记账工具，帮您轻松管理每一笔收支，实现财务自由。</p>
            <ul class="feature-list">
                <li><i class="fas fa-chart-line"></i><span>实时统计，一目了然</span></li>
                <li><i class="fas fa-chart-pie"></i><span>智能分析，优化支出</span></li>
                <li><i class="fas fa-cloud-upload-alt"></i><span>云端同步，数据安全</span></li>
                <li><i class="fas fa-mobile-alt"></i><span>多端支持，随时随地</span></li>
            </ul>
        </div>

        <div class="form-section">
            <div class="tab-switch">
                <button class="tab-btn active" onclick="switchTab('login')">登录</button>
                <button class="tab-btn" onclick="switchTab('register')">注册</button>
            </div>

            <div id="loginPane" class="form-pane active">
                <form id="loginForm">
                    <div class="form-group">
                        <label>手机号</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="text" id="loginUsername" placeholder="请输入手机号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>密码</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="loginPassword" placeholder="请输入密码">
                        </div>
                    </div>
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" id="rememberMe"> 记住我
                        </label>
                        <a href="#" class="forgot-link">忘记密码？</a>
                    </div>
                    <button type="submit" class="submit-btn">登录</button>
                </form>

                <div class="social-login">
                    <div class="social-divider"><span>或使用其他方式登录</span></div>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-weixin"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-qq"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-google"></i></a>
                    </div>
                </div>
            </div>

            <div id="registerPane" class="form-pane">
                <form id="registerForm">
                    <div class="form-group">
                        <label>用户名</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="regUsername" placeholder="4-20个字符">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>邮箱地址</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="regEmail" placeholder="example@email.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>手机号</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="text" id="regPhone" name="phone" placeholder="11位数字">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>密码</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="regPassword" placeholder="至少6位">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>确认密码</label>
                        <div class="input-wrapper">
                            <i class="fas fa-check-circle"></i>
                            <input type="password" id="regConfirmPassword" placeholder="请再次输入密码">
                        </div>
                    </div>
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" id="agreeTerms"> 我已阅读并同意《用户协议》
                        </label>
                    </div>
                    <button type="submit" class="submit-btn">立即注册</button>
                </form>

                <div class="social-login">
                    <div class="social-divider"><span>注册即享</span></div>
                    <div style="text-align: center; margin-top: 16px;">
                        <span style="font-size: 13px; color: #6b7280;">
                            <i class="fas fa-database"></i> 无限账单记录 ·
                            <i class="fas fa-chart-simple"></i> 专业数据分析
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('coms.footer', ['imports' => ['js/index.js']])
