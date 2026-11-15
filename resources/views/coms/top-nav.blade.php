<div class="top-nav">
    <div class="page-title">{{ $pageTitle ?? '财务仪表盘' }}</div>
    <div class="user-dropdown dropdown">
        <div class="user-avatar" data-bs-toggle="dropdown">明</div>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ url('/profile') }}"><i class="fas fa-user"></i> 个人资料</a></li>
            <li><a class="dropdown-item" href="{{ url('/settings') }}"><i class="fas fa-cog"></i> 系统设置</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="{{ url('/') }}"><i class="fas fa-sign-out-alt"></i> 退出登录</a></li>
        </ul>
    </div>
</div>