<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav align-items-center">
        <li class="nav-item">
            <button type="button" class="btn btn-icon-modern" id="toggleMenuIcon" data-widget="pushmenu" role="button" title="Contraer / Expandir menú">
                <i class="bi bi-layout-sidebar-inset"></i>
            </button>
        </li>
        <li class="nav-item d-none d-md-flex align-items-center">
            <span class="navbar-brand-badge">
                <i class="bi bi-hospital text-primary mr-1"></i>
                <span id="spa-current-title">{{ $enterprise->nombre_comercial ?? 'NeumoLav' }}</span>
            </span>
        </li>
        <li class="nav-item d-none d-lg-flex align-items-center ml-2">
            <span class="badge-success-soft">
                <span class="pulse-dot mr-1"></span> En línea
            </span>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto align-items-center">
        <!-- Quick Agenda Action -->
        <li class="nav-item d-none d-sm-block mr-2">
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center" title="Ir a Citas y Agenda">
                <i class="bi bi-calendar2-check mr-1"></i>
                <span>Citas</span>
            </a>
        </li>

        <!-- Pantalla Completa -->
        <li class="nav-item mr-2">
            <button type="button" class="btn btn-icon-modern" data-widget="fullscreen" role="button" title="Pantalla completa">
                <i class="bi bi-arrows-fullscreen"></i>
            </button>
        </li>

        <!-- User Profile Dropdown -->
        <li class="nav-item dropdown">
            <a class="navbar-user-trigger dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{ auth()->user()?->profile_photo_url ?? asset('storage/img/anonymous.png') }}" alt="{{ auth()->user()?->name }}" class="navbar-avatar mr-2">
                <div class="d-none d-md-flex flex-column text-left">
                    <span class="font-weight-bold text-dark small line-height-1 mb-1">
                        {{ auth()->user()?->formatted_name ?? auth()->user()?->name ?? 'Usuario' }}
                    </span>
                    <span class="text-muted" style="font-size: 0.72rem; line-height: 1;">
                        {{ auth()->user()?->roles[0]?->name ?? 'Personal' }}
                    </span>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown-menu">
                <div class="dropdown-user-header d-flex align-items-center">
                    <img src="{{ auth()->user()?->profile_photo_url ?? asset('storage/img/anonymous.png') }}" class="user-avatar mr-3" style="width: 44px; height: 44px;">
                    <div class="overflow-hidden">
                        <div class="font-weight-bold text-dark text-truncate">{{ auth()->user()?->name }}</div>
                        <small class="text-muted text-truncate d-block">{{ auth()->user()?->email }}</small>
                        <span class="badge badge-role mt-1">{{ auth()->user()?->roles[0]?->name ?? 'Usuario' }}</span>
                    </div>
                </div>

                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person-gear mr-2 text-primary"></i> Mi perfil
                </a>

                @can('empresa_acceder')
                    <a class="dropdown-item" href="{{ route('business.enterprise') }}">
                        <i class="bi bi-building mr-2 text-info"></i> Empresa
                    </a>
                @endcan

                <div class="dropdown-divider my-2"></div>

                <a class="dropdown-item text-danger exit-system" href="#">
                    <i class="bi bi-box-arrow-right mr-2"></i> Cerrar sesión
                </a>
            </div>
        </li>
    </ul>
</nav>
