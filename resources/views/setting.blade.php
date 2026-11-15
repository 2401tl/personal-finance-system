@include('coms.header', [
    'title' => '系统设置',
    'imports' => ['css/settings.css'],
])

<div class="container-fluid">
    <div class="row">
        @include('coms.left-nav', ['active' => 'settings'])

        <div class="col-md-9 col-lg-10 main-content">
            @include('coms.top-nav', ['pageTitle' => '系统设置'])

            <!-- 通用设置 -->
                <div class="settings-card">
                    <div class="card-header"><i class="fas fa-globe"></i> 通用设置</div>
                    <div class="settings-list">
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">货币单位</div><div class="settings-desc">设置账单显示的货币符号</div></div><div class="settings-value" id="currencyValue">人民币 (¥)</div><div class="settings-action" onclick="openCurrencyModal()"><i class="fas fa-chevron-right"></i></div></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">日期格式</div><div class="settings-desc">设置账单日期的显示格式</div></div><div class="settings-value" id="dateFormatValue">YYYY-MM-DD</div><div class="settings-action" onclick="openDateFormatModal()"><i class="fas fa-chevron-right"></i></div></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">周起始日</div><div class="settings-desc">设置每周从哪一天开始</div></div><div class="settings-value" id="weekStartValue">星期一</div><div class="settings-action" onclick="openWeekStartModal()"><i class="fas fa-chevron-right"></i></div></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">每页显示条数</div><div class="settings-desc">账单列表每页显示的记录数</div></div><div class="settings-value" id="pageSizeValue">10 条</div><div class="settings-action" onclick="openPageSizeModal()"><i class="fas fa-chevron-right"></i></div></div>
                    </div>
                </div>
                
                <!-- 预算与提醒 -->
                <div class="settings-card">
                    <div class="card-header"><i class="fas fa-bell"></i> 预算与提醒</div>
                    <div class="settings-list">
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">月度预算提醒</div><div class="settings-desc">当支出超过预算时发送提醒</div></div><label class="switch"><input type="checkbox" id="budgetAlert" checked><span class="slider"></span></label></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">预算阈值</div><div class="settings-desc">达到预算的多少百分比时提醒</div></div><div class="settings-value" id="budgetThresholdValue">80%</div><div class="settings-action" onclick="openBudgetThresholdModal()"><i class="fas fa-chevron-right"></i></div></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">每日账单提醒</div><div class="settings-desc">每天固定时间提醒记账</div></div><label class="switch"><input type="checkbox" id="dailyReminder"><span class="slider"></span></label></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">提醒时间</div><div class="settings-desc">设置每日提醒的具体时间</div></div><div class="settings-value" id="reminderTimeValue">20:00</div><div class="settings-action" onclick="openReminderTimeModal()"><i class="fas fa-chevron-right"></i></div></div>
                    </div>
                </div>
                
                <!-- 显示设置 -->
                <div class="settings-card">
                    <div class="card-header"><i class="fas fa-palette"></i> 显示设置</div>
                    <div class="settings-list">
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">主题模式</div><div class="settings-desc">切换亮色/暗色主题</div></div><div class="settings-value" id="themeValue">浅色模式</div><div class="settings-action" onclick="openThemeModal()"><i class="fas fa-chevron-right"></i></div></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">紧凑模式</div><div class="settings-desc">减少内边距，显示更多内容</div></div><label class="switch"><input type="checkbox" id="compactMode"><span class="slider"></span></label></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">图表动画</div><div class="settings-desc">启用/禁用图表动画效果</div></div><label class="switch"><input type="checkbox" id="chartAnimation" checked><span class="slider"></span></label></div>
                    </div>
                </div>
                
                <!-- 数据与同步 -->
                <div class="settings-card">
                    <div class="card-header"><i class="fas fa-cloud-upload-alt"></i> 数据与同步</div>
                    <div class="settings-list">
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">自动备份</div><div class="settings-desc">每周自动备份数据到云端</div></div><label class="switch"><input type="checkbox" id="autoBackup" checked><span class="slider"></span></label></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">备份频率</div><div class="settings-desc">设置自动备份的频率</div></div><div class="settings-value" id="backupFrequencyValue">每周</div><div class="settings-action" onclick="openBackupFrequencyModal()"><i class="fas fa-chevron-right"></i></div></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">立即备份</div><div class="settings-desc">手动备份当前数据</div></div><button class="btn btn-sm btn-outline-primary" onclick="backupNow()">立即备份</button></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">恢复数据</div><div class="settings-desc">从备份文件恢复数据</div></div><button class="btn btn-sm btn-outline-secondary" onclick="restoreData()">恢复数据</button></div>
                    </div>
                </div>
                
                <!-- 隐私与安全 -->
                <div class="settings-card">
                    <div class="card-header"><i class="fas fa-lock"></i> 隐私与安全</div>
                    <div class="settings-list">
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">应用锁</div><div class="settings-desc">启动时需要验证密码/指纹</div></div><label class="switch"><input type="checkbox" id="appLock"><span class="slider"></span></label></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">自动锁定时间</div><div class="settings-desc">应用退出后自动锁定时间</div></div><div class="settings-value" id="autoLockValue">5分钟</div><div class="settings-action" onclick="openAutoLockModal()"><i class="fas fa-chevron-right"></i></div></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">清除缓存</div><div class="settings-desc">清除本地缓存数据</div></div><button class="btn btn-sm btn-outline-danger" onclick="clearCache()">清除缓存</button></div>
                    </div>
                </div>
                
                <!-- 关于 -->
                <div class="settings-card">
                    <div class="card-header"><i class="fas fa-info-circle"></i> 关于</div>
                    <div class="settings-list">
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">版本号</div><div class="settings-desc">当前应用版本</div></div><div class="settings-value">v1.0.0</div></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">检查更新</div><div class="settings-desc">检查新版本</div></div><button class="btn btn-sm btn-outline-primary" onclick="checkUpdate()">检查更新</button></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">用户协议</div><div class="settings-desc">查看用户使用协议</div></div><div class="settings-action" onclick="showAgreement()"><i class="fas fa-chevron-right"></i></div></div>
                        <div class="settings-item"><div class="settings-info"><div class="settings-title">隐私政策</div><div class="settings-desc">查看隐私保护政策</div></div><div class="settings-action" onclick="showPrivacy()"><i class="fas fa-chevron-right"></i></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 各种设置模态框 -->
    <div class="modal fade" id="currencyModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">选择货币单位</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><select id="currencySelect" class="form-select"><option value="CNY">人民币 (¥)</option><option value="USD">美元 ($)</option><option value="EUR">欧元 (€)</option></select></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="saveCurrency()">保存</button></div></div></div></div>
    
    <div class="modal fade" id="dateFormatModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">选择日期格式</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><select id="dateFormatSelect" class="form-select"><option value="YYYY-MM-DD">YYYY-MM-DD (2024-01-25)</option><option value="MM/DD/YYYY">MM/DD/YYYY (01/25/2024)</option><option value="DD/MM/YYYY">DD/MM/YYYY (25/01/2024)</option></select></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="saveDateFormat()">保存</button></div></div></div></div>
    
    <div class="modal fade" id="pageSizeModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">每页显示条数</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><select id="pageSizeSelect" class="form-select"><option value="10">10 条</option><option value="20">20 条</option><option value="50">50 条</option></select></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="savePageSize()">保存</button></div></div></div></div>
    
    <div class="modal fade" id="budgetThresholdModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">预算提醒阈值</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><select id="budgetThresholdSelect" class="form-select"><option value="50">50%</option><option value="60">60%</option><option value="70">70%</option><option value="80">80%</option><option value="90">90%</option></select></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="saveBudgetThreshold()">保存</button></div></div></div></div>
    
    <div class="modal fade" id="reminderTimeModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">设置提醒时间</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="time" id="reminderTimeInput" class="form-control"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="saveReminderTime()">保存</button></div></div></div></div>
    
    <div class="modal fade" id="themeModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">选择主题</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><select id="themeSelect" class="form-select"><option value="light">浅色模式</option><option value="dark">深色模式</option></select></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="saveTheme()">保存</button></div></div></div></div>
    
    <div class="modal fade" id="backupFrequencyModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">备份频率</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><select id="backupFrequencySelect" class="form-select"><option value="daily">每天</option><option value="weekly">每周</option><option value="monthly">每月</option></select></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="saveBackupFrequency()">保存</button></div></div></div></div>
    
    <div class="modal fade" id="autoLockModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">自动锁定时间</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><select id="autoLockSelect" class="form-select"><option value="1">1分钟</option><option value="5">5分钟</option><option value="10">10分钟</option><option value="30">30分钟</option></select></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="saveAutoLock()">保存</button></div></div></div></div>
    
@include('coms.footer', ['imports' => ['js/settings.js']])