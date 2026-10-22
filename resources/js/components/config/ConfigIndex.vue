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
                <!-- Indicadores de Versiones (Estilo small-box idéntico a /admin/home) -->
                <div class="row">
                    <!-- 1. Versión Activa -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>v{{ store.currentVersion }}</h3>
                                <p>Versión Activa</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <a href="#" class="small-box-footer" @click.prevent="store.setTypeFilter('')">
                                Ver historial <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <!-- 2. Versiones Registradas -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ store.metrics.total || store.total }}</h3>
                                <p>Versiones Registradas</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-tags-fill"></i>
                            </div>
                            <a href="#" class="small-box-footer" @click.prevent="store.setTypeFilter('PATCH')">
                                Parches ({{ store.metrics.patch }}) <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- 3. Releases Mayores / Menores -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ (store.metrics.major || 0) + (store.metrics.minor || 0) }}</h3>
                                <p>Releases Menores / Mayores</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-layers-fill"></i>
                            </div>
                            <a href="#" class="small-box-footer" @click.prevent="store.setTypeFilter('MINOR')">
                                Menores ({{ store.metrics.minor }}) &bull; Mayores ({{ store.metrics.major }}) <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- 4. Versionado SemVer Automático -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>SemVer</h3>
                                <p>Escanear & Versionar</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-rocket-takeoff-fill"></i>
                            </div>
                            <a
                                v-if="store.permissions.create"
                                href="#"
                                class="small-box-footer"
                                @click.prevent="store.openNewVersionModal()"
                            >
                                Nueva Versión <i class="fas fa-arrow-circle-right"></i>
                            </a>
                            <span v-else class="small-box-footer text-white-50">
                                <i class="bi bi-lock mr-1"></i> Permiso requerido
                            </span>
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
