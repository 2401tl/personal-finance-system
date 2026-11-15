@php
    $active = $active ?? '';
@endphp
<div class="col-md-3 col-lg-2 px-0 sidebar">
    <div class="logo"><i class="fas fa-book"></i><h3>我的账本</h3></div>
    <nav class="nav flex-column">
        <a class="nav-link {{ $active === 'dashboard' ? 'active' : '' }}" href="{{ url('/dashboard') }}"><i class="fas fa-tachometer-alt"></i><span>仪表盘</span></a>
        <a class="nav-link {{ $active === 'transactions' ? 'active' : '' }}" href="{{ url('/transactions') }}"><i class="fas fa-list"></i><span>账单管理</span></a>
        <a class="nav-link {{ $active === 'statistics' ? 'active' : '' }}" href="{{ url('/statistics') }}"><i class="fas fa-chart-line"></i><span>统计分析</span></a>
        <a class="nav-link {{ $active === 'categories' ? 'active' : '' }}" href="{{ url('/categories') }}"><i class="fas fa-tags"></i><span>分类管理</span></a>
        <a class="nav-link {{ $active === 'accounts' ? 'active' : '' }}" href="{{ url('/accounts') }}"><i class="fas fa-credit-card"></i><span>账户管理</span></a>
        <a class="nav-link {{ $active === 'profile' ? 'active' : '' }}" href="{{ url('/profile') }}"><i class="fas fa-user"></i><span>个人中心</span></a>
        <a class="nav-link {{ $active === 'settings' ? 'active' : '' }}" href="{{ url('/settings') }}"><i class="fas fa-cog"></i><span>系统设置</span></a>
    </nav>
</div>
