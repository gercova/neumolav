<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <div class="brand-logo-container">
            @if(!empty($enterprise->logo))
                <img src="{{ asset('storage/' . $enterprise->logo) }}" alt="Logo" class="brand-image">
            @else
                <i class="bi bi-heart-pulse-fill brand-icon-fallback"></i>
            @endif
        </div>
        <div class="brand-text-wrapper">
            <span class="brand-text">{{ $enterprise->nombre_comercial ?? 'NeumoLav' }}</span>
            <span class="brand-subtext">Sistema Médico</span>
        </div>
    </a>

    <!-- Sidebar Scroll Area -->
    <div class="sidebar">
        <!-- User Panel Card -->
        <div class="user-panel-card">
            <div class="d-flex align-items-center">
                <div class="user-avatar-wrapper">
                    <a href="{{ route('profile.edit') }}" title="Editar mi perfil">
                        <img src="{{ auth()->user()?->profile_photo_url ?? asset('storage/img/anonymous.png') }}" class="user-avatar" id="profile-img" alt="Foto">
                    </a>
                    <span class="status-indicator-online"></span>
                </div>
                <div class="user-info-wrapper ml-3 overflow-hidden">
                    <a href="{{ route('profile.edit') }}" class="user-name text-truncate d-block" title="Editar mi perfil">
                        {{ auth()->user()?->formatted_name ?? auth()->user()?->name ?? 'Usuario' }}
                    </a>
                    <span class="badge badge-role text-truncate d-inline-block">
                        <i class="bi bi-shield-check mr-1"></i>{{ auth()->user()?->roles[0]?->name ?? 'Personal' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu Navigation -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- SECCIÓN: PRINCIPAL -->
                <li class="nav-header">Principal</li>

                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home', 'citas.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-grid-1x2"></i>
                        <p>Inicio / Citas</p>
                    </a>
                </li>

                @can('dashboard')
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-speedometer2"></i>
                            <p>Reportes Gráficos</p>
                        </a>
                    </li>
                @endcan

                <!-- SECCIÓN: CLÍNICA -->
                @can('documentos')
                    <li class="nav-header">Gestión Clínica</li>

                    <li class="nav-item {{ request()->routeIs('hcl.*') ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('hcl.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-journal-medical"></i>
                            <p>
                                Documentos
                                <i class="bi bi-chevron-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('historia_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('hcl.histories.home') }}" class="nav-link {{ request()->routeIs('hcl.histories.*') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Historias clínicas</p>
                                    </a>
                                </li>
                            @endcan

                            @can('examen_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('hcl.exams.home') }}" class="nav-link {{ request()->routeIs('hcl.exams.*') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Exámenes</p>
                                    </a>
                                </li>
                            @endcan

                            @can('control_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('hcl.appointments.home') }}" class="nav-link {{ request()->routeIs('hcl.appointments.*') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Controles</p>
                                    </a>
                                </li>
                            @endcan

                            @can('informe_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('hcl.reports.home') }}" class="nav-link {{ request()->routeIs('hcl.reports.*') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Informes</p>
                                    </a>
                                </li>
                            @endcan

                            @can('riesgo_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('hcl.risks.home') }}" class="nav-link {{ request()->routeIs('hcl.risks.*') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Riesgos</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- SECCIÓN: MANTENIMIENTO -->
                @can('mantenimiento')
                    <li class="nav-header">Catálogos</li>

                    <li class="nav-item {{ request()->routeIs('maintenance.*') ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-boxes"></i>
                            <p>
                                Mantenimiento
                                <i class="bi bi-chevron-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('categoria_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('maintenance.categories') }}" class="nav-link {{ request()->routeIs('maintenance.categories') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Categorías</p>
                                    </a>
                                </li>
                            @endcan

                            @can('presentacion_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('maintenance.presentations') }}" class="nav-link {{ request()->routeIs('maintenance.presentations') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Presentación fármaco</p>
                                    </a>
                                </li>
                            @endcan

                            @can('farmaco_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('maintenance.drugs') }}" class="nav-link {{ request()->routeIs('maintenance.drugs') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Fármacos</p>
                                    </a>
                                </li>
                            @endcan

                            @can('diagnostico_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('maintenance.diagnostics') }}" class="nav-link {{ request()->routeIs('maintenance.diagnostics') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Diagnósticos</p>
                                    </a>
                                </li>
                            @endcan

                            @can('ocupacion_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('maintenance.occupations') }}" class="nav-link {{ request()->routeIs('maintenance.occupations') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Ocupaciones</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- SECCIÓN: NEGOCIO & SISTEMA -->
                @can('empresa')
                    <li class="nav-header">Negocio</li>

                    <li class="nav-item {{ request()->routeIs('business.*') ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('business.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-building-gear"></i>
                            <p>
                                Negocio
                                <i class="bi bi-chevron-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('empresa_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('business.enterprise') }}" class="nav-link {{ request()->routeIs('business.enterprise') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Empresa</p>
                                    </a>
                                </li>
                            @endcan

                            <li class="nav-item">
                                <a href="{{ route('business.posts') }}" class="nav-link {{ request()->routeIs('business.posts') ? 'active' : '' }}">
                                    <i class="bi bi-chevron-right"></i>
                                    <p>Publicaciones</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                @can('seguridad')
                    <li class="nav-header">Seguridad</li>

                    <li class="nav-item {{ request()->routeIs('security.*') ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('security.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-shield-lock"></i>
                            <p>
                                Seguridad
                                <i class="bi bi-chevron-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('permiso_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('security.permissions') }}" class="nav-link {{ request()->routeIs('security.permissions') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Permisos</p>
                                    </a>
                                </li>
                            @endcan

                            @can('rol_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('security.roles') }}" class="nav-link {{ request()->routeIs('security.roles') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Roles</p>
                                    </a>
                                </li>
                            @endcan

                            @can('especialidad_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('security.specialties') }}" class="nav-link {{ request()->routeIs('security.specialties') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Especialidades</p>
                                    </a>
                                </li>
                            @endcan

                            @can('usuario_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('security.users.home') }}" class="nav-link {{ request()->routeIs('security.users.*') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Usuarios</p>
                                    </a>
                                </li>
                            @endcan

                            @can('configuracion_acceder')
                                <li class="nav-item">
                                    <a href="{{ route('security.config') }}" class="nav-link {{ request()->routeIs('security.config*') ? 'active' : '' }}">
                                        <i class="bi bi-chevron-right"></i>
                                        <p>Versiones & Config</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- SECCIÓN: SALIR -->
                <li class="nav-item mt-3">
                    <a href="#" class="nav-link exit-system">
                        <i class="nav-icon bi bi-box-arrow-right"></i>
                        <p>Cerrar sesión</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
