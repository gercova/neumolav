<script setup>
import { onMounted } from 'vue';
import { useConfigStore } from '@/stores/configStore';
import VersionTable from './VersionTable.vue';
import VersionDetailModal from './VersionDetailModal.vue';
import NewVersionModal from './NewVersionModal.vue';

const props = defineProps({
    initialPermissions: {
        type: Object,
        default: () => ({}),
    },
    initialCurrentVersion: {
        type: String,
        default: '3.3.0',
    },
    initialTotalVersions: {
        type: Number,
        default: 0,
    },
    initialLatestRelease: {
        type: Object,
        default: null,
    },
    initialRoutes: {
        type: Object,
        default: () => ({}),
    },
});

const store = useConfigStore();

onMounted(() => {
    if (props.initialPermissions) {
        store.permissions = { ...store.permissions, ...props.initialPermissions };
    }
    if (props.initialCurrentVersion) {
        store.currentVersion = props.initialCurrentVersion;
    }
    if (props.initialRoutes) {
        store.routes = { ...store.routes, ...props.initialRoutes };
    }
    if (props.initialLatestRelease) {
        store.latestRelease = props.initialLatestRelease;
    }

    store.fetchVersions(1);
});
</script>

<template>
    <div class="content-wrapper">
        <!-- Content Header / Breadcrumb -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0 font-weight-bold text-dark d-flex align-items-center gap-2">
                            Versiones & Configuración del Sistema
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/admin/home">Home</a></li>
                            <li class="breadcrumb-item">Seguridad</li>
                            <li class="breadcrumb-item active">Versiones & Config</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Section -->
        <section class="content pb-4">
            <div class="container-fluid">
                <!-- Top Summary Cards -->
                <div class="row g-3 mb-4">
                    <!-- Card 1: Versión Actual -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #0284c7 !important;">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Versión Activa</span>
                                    <h3 class="fw-bold text-dark font-monospace mb-1">
                                        v{{ store.currentVersion }}
                                    </h3>
                                    <span class="badge badge-success px-2 py-1">
                                        <i class="bi bi-check2-circle mr-1"></i> Producción
                                    </span>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                                    <i class="bi bi-shield-check fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Total Versiones -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #10b981 !important;">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Historial SemVer</span>
                                    <h3 class="fw-bold text-dark mb-1">
                                        {{ store.metrics.total || store.total }}
                                    </h3>
                                    <div class="d-flex gap-1" style="font-size: 0.75rem;">
                                        <span class="badge badge-danger">{{ store.metrics.major }} Maj</span>
                                        <span class="badge badge-primary">{{ store.metrics.minor }} Min</span>
                                        <span class="badge badge-success">{{ store.metrics.patch }} Pat</span>
                                    </div>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-success d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                                    <i class="bi bi-tags-fill fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Último Despliegue -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #6366f1 !important;">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Último Registro</span>
                                    <h6 class="fw-bold text-dark mb-1 text-truncate" style="max-width: 170px;">
                                        {{ store.latestRelease?.summary || 'Consolidación del sistema' }}
                                    </h6>
                                    <small class="text-muted d-block">
                                        {{ store.latestRelease?.created_at || 'Actualizado' }}
                                    </small>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-indigo d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                                    <i class="bi bi-clock-history fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Acciones -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100 bg-primary text-white" style="border-radius: 12px;">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <span class="text-white-50 text-uppercase fw-bold" style="font-size: 0.75rem;">Acciones de Versión</span>
                                    <h6 class="font-weight-bold mb-2">Versionado Automático</h6>
                                </div>
                                <button
                                    v-if="store.permissions.create"
                                    type="button"
                                    class="btn btn-light text-primary font-weight-bold d-flex align-items-center justify-content-center gap-2 shadow-sm"
                                    @click="store.openNewVersionModal()"
                                >
                                    <i class="bi bi-rocket-takeoff-fill"></i>
                                    <span>Escanear y Versionar</span>
                                </button>
                                <span v-else class="text-white-50 small">
                                    <i class="bi bi-lock mr-1"></i> Permiso de creación requerido
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Component -->
                <VersionTable />
            </div>
        </section>

        <!-- Modals -->
        <VersionDetailModal />
        <NewVersionModal />
    </div>
</template>
