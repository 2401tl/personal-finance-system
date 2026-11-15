@include('coms.header', [
'title' => '个人中心',
'imports' => ['css/profile.css'],
])

<div class="container-fluid">
    <div class="row">
        @include('coms.left-nav', ['active' => 'profile'])

        <div class="col-md-9 col-lg-10 main-content">
            @include('coms.top-nav', ['pageTitle' => '个人中心'])

            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar" onclick="changeAvatar()">
                        <input type="file" name="avatar" id="selectAvatar" accept="image/*" style="display:none">
                        <div class="avatar-icon" id="avatarIcon">
                            @if (Auth::user()->avatar)
                            <img src="{{Auth::user()->avatar}}" alt="Avatar">
                            @else
                            <i class="fas fa-user-circle"></i>
                            @endif
                        </div>
                    </div>
                    <div class="profile-name" id="profileName">{{Auth::user()->username}}</div>
                    <div class="profile-email" id="profileEmail">{{Auth::user()->email}}</div>
                </div>
                <div class="profile-stats">
                    <div class="stat-item">
                        <div class="stat-number" id="statDays">128</div>
                        <div class="stat-label">记账天数</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="statRecords">156</div>
                        <div class="stat-label">总账单数</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="statCategories">24</div>
                        <div class="stat-label">分类数量</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="statMonthlyAvg">¥3,280</div>
                        <div class="stat-label">月均支出</div>
                    </div>
                </div>
            </div>

            <div class="menu-section">
                <div class="section-title"><i class="fas fa-shield-alt"></i> 账号安全</div>
                <div class="menu-item" onclick="openPasswordModal()">
                    <div class="menu-left">
                        <div class="menu-icon"><i class="fas fa-key"></i></div>
                        <div class="menu-info">
                            <div class="menu-title">修改密码</div>
                            <div class="menu-desc">定期更换密码，保护账户安全</div>
                        </div>
                    </div>
                    <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
                </div>
                <div class="menu-item" onclick="openEmailModal()">
                    <div class="menu-left">
                        <div class="menu-icon"><i class="fas fa-envelope"></i></div>
                        <div class="menu-info">
                            <div class="menu-title">绑定邮箱</div>
                            <div class="menu-desc">用于找回密码和接收通知</div>
                        </div>
                    </div>
                    <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
                </div>
                <div class="menu-item" onclick="openPhoneModal()">
                    <div class="menu-left">
                        <div class="menu-icon"><i class="fas fa-mobile-alt"></i></div>
                        <div class="menu-info">
                            <div class="menu-title">绑定手机</div>
                            <div class="menu-desc">增强账户安全性</div>
                        </div>
                    </div>
                    <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
                </div>
            </div>

            <div class="menu-section">
                <div class="section-title"><i class="fas fa-sliders-h"></i> 个人设置</div>
                <div class="menu-item" onclick="openProfileModal()">
                    <div class="menu-left">
                        <div class="menu-icon"><i class="fas fa-user-edit"></i></div>
                        <div class="menu-info">
                            <div class="menu-title">编辑资料</div>
                            <div class="menu-desc">修改昵称、个性签名等</div>
                        </div>
                    </div>
                    <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
                </div>
                <div class="menu-item" onclick="openCurrencyModal()">
                    <div class="menu-left">
                        <div class="menu-icon"><i class="fas fa-dollar-sign"></i></div>
                        <div class="menu-info">
                            <div class="menu-title">货币单位</div>
                            <div class="menu-desc">设置默认货币：人民币 ¥</div>
                        </div>
                    </div>
                    <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
                </div>
                <div class="menu-item" onclick="openDateFormatModal()">
                    <div class="menu-left">
                        <div class="menu-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div class="menu-info">
                            <div class="menu-title">日期格式</div>
                            <div class="menu-desc">YYYY-MM-DD</div>
                        </div>
                    </div>
                    <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
                </div>
            </div>

            <div class="menu-section">
                <div class="section-title"><i class="fas fa-database"></i> 数据管理</div>
                <div class="menu-item" onclick="exportData()">
                    <div class="menu-left">
                        <div class="menu-icon"><i class="fas fa-download"></i></div>
                        <div class="menu-info">
                            <div class="menu-title">导出数据</div>
                            <div class="menu-desc">导出账单为Excel或CSV格式</div>
                        </div>
                    </div>
                    <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
                </div>
                <div class="menu-item" onclick="backupData()">
                    <div class="menu-left">
                        <div class="menu-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="menu-info">
                            <div class="menu-title">备份数据</div>
                            <div class="menu-desc">将数据备份到云端</div>
                        </div>
                    </div>
                    <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
                </div>
                <div class="menu-item" onclick="clearData()">
                    <div class="menu-left">
                        <div class="menu-icon"><i class="fas fa-trash-alt"></i></div>
                        <div class="menu-info">
                            <div class="menu-title">清除数据</div>
                            <div class="menu-desc text-danger">清空所有账单记录（不可恢复）</div>
                        </div>
                    </div>
                    <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
                </div>
            </div>

            <div class="menu-section">
                <div class="section-title"><i class="fas fa-info-circle"></i> 关于我们</div>
                <div class="menu-item" onclick="showAbout()">
                    <div class="menu-left">
                        <div class="menu-icon"><i class="fas fa-heart"></i></div>
                        <div class="menu-info">
                            <div class="menu-title">关于账本</div>
                            <div class="menu-desc">版本 1.0.0 | 用心记录每一笔</div>
                        </div>
                    </div>
                    <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
                </div>
                <div class="menu-item" onclick="showFeedback()">
                    <div class="menu-left">
                        <div class="menu-icon"><i class="fas fa-comment-dots"></i></div>
                        <div class="menu-info">
                            <div class="menu-title">意见反馈</div>
                            <div class="menu-desc">帮助我们做得更好</div>
                        </div>
                    </div>
                    <div class="menu-right"><i class="fas fa-chevron-right"></i></div>
                </div>
            </div>

            <div class="logout-btn"><button class="btn btn-danger w-100" onclick="logout()" style="border-radius:12px; padding:12px;"><i class="fas fa-sign-out-alt"></i> 退出登录</button></div>
        </div>
    </div>
</div>

<!-- 修改密码模态框 -->
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">修改密码</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">当前密码</label><input type="password" id="oldPassword" class="form-control" placeholder="请输入当前密码"></div>
                <div class="mb-3"><label class="form-label">新密码</label><input type="password" id="newPassword" class="form-control" placeholder="至少6位"></div>
                <div class="mb-3"><label class="form-label">确认新密码</label><input type="password" id="confirmPassword" class="form-control" placeholder="请再次输入"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="changePassword()">确认修改</button></div>
        </div>
    </div>
</div>

<!-- 编辑资料模态框 -->
<div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">编辑资料</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">昵称</label><input type="text" id="editName" class="form-control" placeholder="请输入昵称"></div>
                <div class="mb-3"><label class="form-label">个性签名</label><textarea id="editBio" class="form-control" rows="3" placeholder="写点什么..."></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="saveProfile()">保存修改</button></div>
        </div>
    </div>
</div>

@include('coms.footer', ['imports' => ['js/profile.js']])