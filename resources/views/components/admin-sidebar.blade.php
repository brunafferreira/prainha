<header class="mobile-header">
    <a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark">PV</span><span class="brand-name">Prainha <strong>Vôlei</strong></span></a>
    <button class="mobile-menu-button" type="button" wire:click="$toggle('sidebarOpen')" aria-label="Abrir menu">☰</button>
</header>
<button type="button" class="sidebar-overlay {{ $sidebarOpen ? 'is-visible' : '' }}" wire:click="$set('sidebarOpen', false)" aria-label="Fechar menu"></button>
<aside class="admin-sidebar {{ $sidebarOpen ? 'is-open' : '' }}">
    <a class="brand sidebar-brand" href="{{ route('dashboard') }}">
        <span class="brand-mark">PV</span>
        <span class="brand-name">Prainha <strong>Vôlei</strong></span>
    </a>
    <div class="sidebar-caption">GERENCIAMENTO</div>
    <nav class="sidebar-nav" aria-label="Menu principal">
        <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <span class="nav-icon">▦</span> Visão geral
        </a>
        <a class="sidebar-link {{ request()->route('section') === 'alunos' ? 'active' : '' }}" href="{{ route('management.index', 'alunos') }}">
            <span class="nav-icon">♙</span> Alunos
        </a>
        <a class="sidebar-link {{ request()->route('section') === 'turmas' ? 'active' : '' }}" href="{{ route('management.index', 'turmas') }}">
            <span class="nav-icon">▤</span> Turmas
        </a>
        <a class="sidebar-link {{ request()->route('section') === 'mensalidades' ? 'active' : '' }}" href="{{ route('management.index', 'mensalidades') }}">
            <span class="nav-icon">R$</span> Mensalidades
        </a>
        <a class="sidebar-link {{ request()->route('section') === 'reservas' ? 'active' : '' }}" href="{{ route('management.index', 'reservas') }}">
            <span class="nav-icon">◷</span> Reservas
        </a>
        <a class="sidebar-link {{ request()->routeIs('settings') ? 'active' : '' }}" href="{{ route('settings') }}">
            <span class="nav-icon">⚙</span> Configurações
        </a>
    </nav>
    <div class="sidebar-bottom">
        <div class="sidebar-user">
            <span class="user-initial">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="sidebar-user-info"><strong>{{ auth()->user()->name }}</strong><small>Administrador</small></span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link logout-link"><span class="nav-icon">↪</span> Sair</button>
        </form>
    </div>
</aside>
