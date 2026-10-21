<script setup>
import { computed } from 'vue';
import { useConfigStore } from '@/stores/configStore';

const store = useConfigStore();

const version = computed(() => store.selectedVersion);

const typeBadgeClass = (type) => {
    switch (type) {
        case 'MAJOR':
            return 'badge-danger';
        case 'MINOR':
            return 'badge-primary';
        case 'PATCH':
        default:
            return 'badge-success';
    }
};

const changeTypeBadge = (changeType) => {
    switch (changeType) {
        case 'added':
            return 'badge-success';
        case 'deleted':
            return 'badge-danger';
        case 'signature_changed':
            return 'badge-warning';
        case 'modified':
        default:
            return 'badge-info';
    }
};

const changeTypeLabel = (changeType) => {
    switch (changeType) {
        case 'added':
            return 'Agregado';
        case 'deleted':
            return 'Eliminado';
        case 'signature_changed':
            return 'Firma Alterada';
        case 'modified':
        default:
            return 'Modificado';
    }
};
</script>

<template>
    <div
        v-if="store.detailModalVisible"
        class="modal fade show d-block"
        tabindex="-1"
        style="background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); z-index: 1050;"
        @click.self="store.closeDetailModal"
    >
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                <!-- Header -->
                <div class="modal-header bg-navy text-white px-4 py-3 align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white text-navy p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-tag-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title font-weight-bold mb-0">
                                    Detalles de Versión {{ version ? `v${version.version}` : '' }}
                                </h5>
                                <span v-if="version" class="badge" :class="typeBadgeClass(version.version_type)">
                                    {{ version.version_type }}
                                </span>
                            </div>
                            <small class="text-white-50">
                                Registro de cambios automáticos y análisis SemVer
                            </small>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="btn btn-sm btn-link text-white text-decoration-none"
                        style="font-size: 1.25rem;"
                        @click="store.closeDetailModal"
                    >
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4 bg-light">
                    <!-- Loading state -->
                    <div v-if="store.detailLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando detalles...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando información de la versión...</p>
                    </div>

                    <!-- Detail Content -->
                    <div v-else-if="version">
                        <!-- Summary Banner -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.75rem;">Resumen de la Versión</h6>
                                        <h5 class="fw-bold text-dark mb-2">{{ version.summary }}</h5>
                                        <p v-if="version.description" class="text-secondary mb-0" style="white-space: pre-line;">
                                            {{ version.description }}
                                        </p>
                                        <p v-else class="text-muted fst-italic mb-0">Sin descripción adicional.</p>
                                    </div>
                                    <div class="col-md-4 border-start-md ps-md-4">
                                        <div class="d-flex flex-column gap-2 text-sm">
                                            <div>
                                                <i class="bi bi-person text-primary mr-1"></i>
                                                <strong class="text-dark">Autor:</strong>
                                                <span class="text-secondary ml-1">{{ version.author }}</span>
                                            </div>
                                            <div>
                                                <i class="bi bi-calendar-event text-info mr-1"></i>
                                                <strong class="text-dark">Fecha:</strong>
                                                <span class="text-secondary ml-1">{{ version.created_at }}</span>
                                            </div>
                                            <div v-if="version.commit_hash">
                                                <i class="bi bi-git text-danger mr-1"></i>
                                                <strong class="text-dark">Commit:</strong>
                                                <code class="text-danger ml-1 font-monospace">{{ version.commit_hash }}</code>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Metrics Row -->
                        <div class="row g-2 mb-4">
                            <div class="col-6 col-md-2">
                                <div class="p-2 bg-white rounded shadow-sm text-center border">
                                    <span class="d-block text-muted" style="font-size: 0.75rem;">Clases</span>
                                    <strong class="fs-5 text-primary">{{ version.classes_changed?.length || 0 }}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="p-2 bg-white rounded shadow-sm text-center border">
                                    <span class="d-block text-muted" style="font-size: 0.75rem;">Métodos</span>
                                    <strong class="fs-5 text-indigo">{{ version.methods_changed?.length || 0 }}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="p-2 bg-white rounded shadow-sm text-center border">
                                    <span class="d-block text-muted" style="font-size: 0.75rem;">Vistas Blade</span>
                                    <strong class="fs-5 text-success">{{ version.views_changed?.length || 0 }}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="p-2 bg-white rounded shadow-sm text-center border">
                                    <span class="d-block text-muted" style="font-size: 0.75rem;">Scripts/Vue</span>
                                    <strong class="fs-5 text-warning">{{ version.scripts_changed?.length || 0 }}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="p-2 bg-white rounded shadow-sm text-center border">
                                    <span class="d-block text-muted" style="font-size: 0.75rem;">Dependencias</span>
                                    <strong class="fs-5 text-info">{{ version.dependencies_changed?.length || 0 }}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="p-2 bg-white rounded shadow-sm text-center border">
                                    <span class="d-block text-muted" style="font-size: 0.75rem;">Actualizaciones</span>
                                    <strong class="fs-5 text-secondary">{{ version.updates?.length || 0 }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Nav Tabs for Categorized Changes -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white p-2">
                                <ul class="nav nav-pills" id="versionTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active btn-sm" id="methods-tab" data-toggle="pill" href="#tab-methods" role="tab">
                                            <i class="bi bi-code-slash mr-1"></i> Clases & Métodos ({{ version.methods_changed?.length || 0 }})
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn-sm" id="views-tab" data-toggle="pill" href="#tab-views" role="tab">
                                            <i class="bi bi-file-earmark-code mr-1"></i> Vistas Blade ({{ version.views_changed?.length || 0 }})
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn-sm" id="scripts-tab" data-toggle="pill" href="#tab-scripts" role="tab">
                                            <i class="bi bi-filetype-vue mr-1"></i> Scripts & Assets ({{ version.scripts_changed?.length || 0 }})
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn-sm" id="deps-tab" data-toggle="pill" href="#tab-deps" role="tab">
                                            <i class="bi bi-box-seam mr-1"></i> Dependencias ({{ version.dependencies_changed?.length || 0 }})
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn-sm" id="updates-tab" data-toggle="pill" href="#tab-updates" role="tab">
                                            <i class="bi bi-database-gear mr-1"></i> Actualizaciones ({{ version.updates?.length || 0 }})
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body p-3">
                                <div class="tab-content" id="versionTabContent">
                                    <!-- TAB 1: Clases y Métodos -->
                                    <div class="tab-pane fade show active" id="tab-methods" role="tabpanel">
                                        <div v-if="!version.methods_changed || version.methods_changed.length === 0" class="text-center py-4 text-muted">
                                            <i class="bi bi-check-circle fs-3 text-secondary d-block mb-1"></i>
                                            No se registraron cambios en métodos o clases para esta versión.
                                        </div>
                                        <div v-else class="accordion" id="methodsAccordion">
                                            <div
                                                v-for="(method, idx) in version.methods_changed"
                                                :key="idx"
                                                class="card mb-2 border shadow-none"
                                            >
                                                <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge mr-2" :class="changeTypeBadge(method.change_type)">
                                                            {{ changeTypeLabel(method.change_type) }}
                                                        </span>
                                                        <span v-if="method.is_breaking" class="badge badge-danger mr-2">
                                                            <i class="bi bi-exclamation-triangle mr-1"></i> INCOMPATIBLE (MAJOR)
                                                        </span>
                                                        <code class="text-primary font-weight-bold">{{ method.class }}</code>
                                                        <span class="text-muted mx-1">&rarr;</span>
                                                        <strong class="text-dark">{{ method.method }}()</strong>
                                                        <span v-if="method.visibility" class="badge badge-light border text-muted ml-2">
                                                            {{ method.visibility }}
                                                        </span>
                                                    </div>
                                                    <span class="badge badge-secondary badge-pill">
                                                        {{ method.parameters?.length || 0 }} params
                                                    </span>
                                                </div>
                                                <div class="card-body p-3">
                                                    <p v-if="method.description" class="text-muted small mb-2">
                                                        <i class="bi bi-info-circle mr-1"></i> {{ method.description }}
                                                    </p>

                                                    <!-- Parameter table -->
                                                    <div v-if="method.parameters && method.parameters.length > 0" class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0 bg-white" style="font-size: 0.85rem;">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th style="width: 50px;">#</th>
                                                                    <th>Parámetro</th>
                                                                    <th>Tipo de Dato</th>
                                                                    <th>Opcional</th>
                                                                    <th>Valor por Defecto</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(p, pIdx) in method.parameters" :key="pIdx">
                                                                    <td>{{ pIdx + 1 }}</td>
                                                                    <td>
                                                                        <code class="text-danger font-weight-bold">{{ p.name }}</code>
                                                                    </td>
                                                                    <td>
                                                                        <span v-if="p.type" class="badge badge-info">{{ p.type }}</span>
                                                                        <span v-else class="text-muted fst-italic">no tipado</span>
                                                                    </td>
                                                                    <td>
                                                                        <span v-if="p.is_optional" class="badge badge-success">Sí</span>
                                                                        <span v-else class="badge badge-secondary">Obligatorio</span>
                                                                    </td>
                                                                    <td>
                                                                        <code v-if="p.default !== null && p.default !== undefined" class="text-secondary">{{ p.default }}</code>
                                                                        <span v-else class="text-muted">—</span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div v-else class="text-muted small fst-italic">
                                                        Este método no recibe parámetros.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TAB 2: Vistas Blade -->
                                    <div class="tab-pane fade" id="tab-views" role="tabpanel">
                                        <div v-if="!version.views_changed || version.views_changed.length === 0" class="text-center py-4 text-muted">
                                            No se modificaron vistas Blade en esta versión.
                                        </div>
                                        <ul v-else class="list-group list-group-flush">
                                            <li
                                                v-for="(view, vIdx) in version.views_changed"
                                                :key="vIdx"
                                                class="list-group-item d-flex justify-content-between align-items-center py-2 px-3"
                                            >
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-file-earmark-code text-primary mr-2 fs-5"></i>
                                                    <div>
                                                        <strong class="text-dark">{{ view.file || view.name }}</strong>
                                                        <span v-if="view.name" class="d-block text-muted small">{{ view.name }}</span>
                                                    </div>
                                                </div>
                                                <span class="badge" :class="changeTypeBadge(view.change_type)">
                                                    {{ changeTypeLabel(view.change_type) }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- TAB 3: Scripts & Assets -->
                                    <div class="tab-pane fade" id="tab-scripts" role="tabpanel">
                                        <div v-if="!version.scripts_changed || version.scripts_changed.length === 0" class="text-center py-4 text-muted">
                                            No se registraron cambios en scripts o estilos.
                                        </div>
                                        <ul v-else class="list-group list-group-flush">
                                            <li
                                                v-for="(script, sIdx) in version.scripts_changed"
                                                :key="sIdx"
                                                class="list-group-item d-flex justify-content-between align-items-center py-2 px-3"
                                            >
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-file-earmark-text text-warning mr-2 fs-5"></i>
                                                    <div>
                                                        <strong class="text-dark">{{ script.file }}</strong>
                                                        <span class="badge badge-light border ml-2 text-uppercase">{{ script.type }}</span>
                                                    </div>
                                                </div>
                                                <span class="badge" :class="changeTypeBadge(script.change_type)">
                                                    {{ changeTypeLabel(script.change_type) }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- TAB 4: Dependencias -->
                                    <div class="tab-pane fade" id="tab-deps" role="tabpanel">
                                        <div v-if="!version.dependencies_changed || version.dependencies_changed.length === 0" class="text-center py-4 text-muted">
                                            No hubo modificaciones en librerías o dependencias.
                                        </div>
                                        <ul v-else class="list-group list-group-flush">
                                            <li
                                                v-for="(dep, dIdx) in version.dependencies_changed"
                                                :key="dIdx"
                                                class="list-group-item d-flex justify-content-between align-items-center py-2 px-3"
                                            >
                                                <div>
                                                    <strong class="text-dark">{{ dep.package }}</strong>
                                                    <span class="badge badge-secondary ml-2">{{ dep.manager }}</span>
                                                    <div class="text-muted small">Versión: {{ dep.version }}</div>
                                                </div>
                                                <span class="badge" :class="changeTypeBadge(dep.change_type)">
                                                    {{ dep.change_type }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- TAB 5: Actualizaciones del Sistema -->
                                    <div class="tab-pane fade" id="tab-updates" role="tabpanel">
                                        <div v-if="!version.updates || version.updates.length === 0" class="text-center py-4 text-muted">
                                            No se registraron migraciones o cambios de rutas en esta versión.
                                        </div>
                                        <ul v-else class="list-group list-group-flush">
                                            <li
                                                v-for="(upd, uIdx) in version.updates"
                                                :key="uIdx"
                                                class="list-group-item d-flex justify-content-between align-items-center py-2 px-3"
                                            >
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-gear-wide-connected text-secondary mr-2 fs-5"></i>
                                                    <div>
                                                        <strong class="text-dark">{{ upd.file }}</strong>
                                                        <span class="badge badge-info ml-2 text-uppercase">{{ upd.type }}</span>
                                                    </div>
                                                </div>
                                                <span class="badge" :class="changeTypeBadge(upd.change_type)">
                                                    {{ changeTypeLabel(upd.change_type) }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-white px-4 py-3">
                    <button type="button" class="btn btn-secondary" @click="store.closeDetailModal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
