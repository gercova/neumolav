<script setup>
import { ref, watch, computed } from 'vue';
import { useConfigStore } from '@/stores/configStore';

const store = useConfigStore();

const form = ref({
    version: '',
    version_type: 'PATCH',
    summary: '',
    description: '',
    commit_hash: '',
    classes_changed: [],
    methods_changed: [],
    views_changed: [],
    scripts_changed: [],
    dependencies_changed: [],
    updates: [],
    stats: {},
});

// Sync form with detected changes when detection finishes
watch(
    () => store.pendingDetection,
    (detection) => {
        if (detection) {
            form.value.version = detection.next_version || '';
            form.value.version_type = detection.recommended_type || 'PATCH';
            form.value.summary = detection.auto_summary || '';
            form.value.commit_hash = detection.commit_hash || '';
            form.value.classes_changed = detection.classes_changed || [];
            form.value.methods_changed = detection.methods_changed || [];
            form.value.views_changed = detection.views_changed || [];
            form.value.scripts_changed = detection.scripts_changed || [];
            form.value.dependencies_changed = detection.dependencies_changed || [];
            form.value.updates = detection.updates || [];
            form.value.stats = detection.stats || {};
        }
    },
    { immediate: true }
);

const onTypeChange = () => {
    // Recompute recommended version based on selected type if user changes dropdown
    if (store.pendingDetection && store.pendingDetection.current_version) {
        const cur = store.pendingDetection.current_version;
        const parts = cur.replace(/^v/i, '').split('.').map(Number);
        const major = parts[0] || 1;
        const minor = parts[1] || 0;
        const patch = parts[2] || 0;

        if (form.value.version_type === 'MAJOR') {
            form.value.version = `${major + 1}.0.0`;
        } else if (form.value.version_type === 'MINOR') {
            form.value.version = `${major}.${minor + 1}.0`;
        } else {
            form.value.version = `${major}.${minor}.${patch + 1}`;
        }
    }
};

const submitForm = async () => {
    await store.createVersion(form.value);
};

const hasChanges = computed(() => store.pendingDetection?.has_changes ?? false);
</script>

<template>
    <div
        v-if="store.newVersionModalVisible"
        class="modal fade show d-block"
        tabindex="-1"
        style="background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); z-index: 1050;"
        @click.self="store.closeNewVersionModal"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                <!-- Header -->
                <div class="modal-header bg-primary text-white px-4 py-3 align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white text-primary p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-rocket-takeoff-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title font-weight-bold mb-0">
                                Generar Versión Automática SemVer
                            </h5>
                            <small class="text-white-50">
                                Escaneo de cambios en clases, métodos, vistas, scripts y dependencias
                            </small>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="btn btn-sm btn-link text-white text-decoration-none"
                        style="font-size: 1.25rem;"
                        @click="store.closeNewVersionModal"
                    >
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4 bg-light">
                    <!-- Scanning State -->
                    <div v-if="store.detecting" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Escaneando...</span>
                        </div>
                        <h6 class="mt-3 font-weight-bold text-dark">Analizando cambios del código fuente...</h6>
                        <p class="text-muted small">
                            Inspeccionando firmas de métodos, parámetros de funciones, vistas Blade, componentes Vue y dependencias.
                        </p>
                    </div>

                    <!-- Detection Form -->
                    <div v-else>
                        <!-- Detection Summary Box -->
                        <div class="card border-0 shadow-sm mb-4" style="border-left: 4px solid #0284c7 !important;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small">Versión Base Actual:</span>
                                        <strong class="badge badge-light border text-dark font-monospace fs-6">
                                            v{{ store.pendingDetection?.current_version || store.currentVersion }}
                                        </strong>
                                        <span class="text-muted mx-1">&rarr;</span>
                                        <span class="text-muted small">Sugerida:</span>
                                        <strong class="badge badge-primary font-monospace fs-6">
                                            v{{ store.pendingDetection?.next_version }}
                                        </strong>
                                    </div>
                                    <span
                                        class="badge font-weight-bold text-uppercase"
                                        :class="{
                                            'badge-danger': store.pendingDetection?.recommended_type === 'MAJOR',
                                            'badge-primary': store.pendingDetection?.recommended_type === 'MINOR',
                                            'badge-success': store.pendingDetection?.recommended_type === 'PATCH',
                                        }"
                                    >
                                        {{ store.pendingDetection?.recommended_type }}
                                    </span>
                                </div>
                                <p class="mb-2 text-dark small">
                                    <i class="bi bi-lightbulb-fill text-warning mr-1"></i>
                                    <strong>Criterio SemVer:</strong> {{ store.pendingDetection?.rationale }}
                                </p>

                                <!-- Mini Stats badges -->
                                <div class="d-flex flex-wrap gap-2 pt-1 border-top" style="font-size: 0.8rem;">
                                    <span class="badge badge-light border text-secondary">
                                        {{ store.pendingDetection?.stats?.classes_count || 0 }} Clases
                                    </span>
                                    <span class="badge badge-light border text-secondary">
                                        {{ store.pendingDetection?.stats?.methods_count || 0 }} Métodos inspeccionados
                                    </span>
                                    <span class="badge badge-light border text-secondary">
                                        {{ store.pendingDetection?.stats?.views_count || 0 }} Vistas
                                    </span>
                                    <span class="badge badge-light border text-secondary">
                                        {{ store.pendingDetection?.stats?.scripts_count || 0 }} Scripts
                                    </span>
                                    <span class="badge badge-light border text-secondary">
                                        {{ store.pendingDetection?.stats?.updates_count || 0 }} Actualizaciones
                                    </span>
                                    <span v-if="store.pendingDetection?.stats?.breaking_count > 0" class="badge badge-danger">
                                        {{ store.pendingDetection?.stats?.breaking_count }} Cambios Incompatibles
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submitForm">
                            <div class="row g-3 mb-3">
                                <!-- Version Number -->
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold text-dark small">
                                        Número de Versión <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text font-monospace">v</span>
                                        </div>
                                        <input
                                            v-model="form.version"
                                            type="text"
                                            class="form-control font-monospace"
                                            placeholder="3.4.0"
                                            required
                                        />
                                    </div>
                                    <small class="text-muted">Formato Semantic Versioning (MAJOR.MINOR.PATCH)</small>
                                    <div v-if="store.formErrors.version" class="text-danger small mt-1">
                                        {{ store.formErrors.version[0] }}
                                    </div>
                                </div>

                                <!-- Version Type -->
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold text-dark small">
                                        Tipo de Incremento SemVer <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        v-model="form.version_type"
                                        class="form-control"
                                        @change="onTypeChange"
                                        required
                                    >
                                        <option value="PATCH">PATCH (Correcciones, refactorizaciones compatibles)</option>
                                        <option value="MINOR">MINOR (Nuevas clases, métodos, vistas compatibles)</option>
                                        <option value="MAJOR">MAJOR (Cambios incompatibles o firmas alteradas)</option>
                                    </select>
                                    <div v-if="store.formErrors.version_type" class="text-danger small mt-1">
                                        {{ store.formErrors.version_type[0] }}
                                    </div>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="mb-3">
                                <label class="form-label font-weight-bold text-dark small">
                                    Título / Resumen de la Versión <span class="text-danger">*</span>
                                </label>
                                <input
                                    v-model="form.summary"
                                    type="text"
                                    class="form-control"
                                    placeholder="Ej: Nuevas funcionalidades de reportes y optimización de citas"
                                    maxlength="255"
                                    required
                                />
                                <div v-if="store.formErrors.summary" class="text-danger small mt-1">
                                    {{ store.formErrors.summary[0] }}
                                </div>
                            </div>

                            <!-- Description / Changelog Notes -->
                            <div class="mb-3">
                                <label class="form-label font-weight-bold text-dark small">
                                    Notas de Versión / Changelog (Opcional)
                                </label>
                                <textarea
                                    v-model="form.description"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Detalles sobre las modificaciones realizadas, mejoras de rendimiento o incidencias resueltas..."
                                ></textarea>
                            </div>

                            <!-- Collapsible accordion to review detected items before recording -->
                            <div class="mb-3">
                                <a
                                    class="btn btn-outline-secondary btn-sm text-decoration-none d-flex justify-content-between align-items-center"
                                    data-toggle="collapse"
                                    href="#collapseDetectedChanges"
                                    role="button"
                                >
                                    <span>
                                        <i class="bi bi-eye mr-1"></i> Inspeccionar elementos que se guardarán en esta versión
                                    </span>
                                    <i class="bi bi-chevron-down"></i>
                                </a>

                                <div class="collapse mt-2" id="collapseDetectedChanges">
                                    <div class="card card-body bg-white p-3 border">
                                        <!-- Methods preview -->
                                        <h6 class="font-weight-bold text-dark mb-2" style="font-size: 0.85rem;">
                                            Métodos Inspeccionados con Parámetros ({{ form.methods_changed?.length || 0 }}):
                                        </h6>
                                        <div v-if="form.methods_changed && form.methods_changed.length > 0" class="mb-3" style="max-height: 180px; overflow-y: auto;">
                                            <div
                                                v-for="(m, mIdx) in form.methods_changed.slice(0, 15)"
                                                :key="mIdx"
                                                class="d-flex justify-content-between align-items-center py-1 border-bottom small"
                                            >
                                                <div>
                                                    <code class="text-primary">{{ m.class }}@{{ m.method }}</code>
                                                    <span class="text-muted ml-1">({{ m.parameters?.length || 0 }} params)</span>
                                                </div>
                                                <span class="badge badge-light border">{{ m.change_type }}</span>
                                            </div>
                                            <small v-if="form.methods_changed.length > 15" class="text-muted mt-1 d-block">
                                                ...y {{ form.methods_changed.length - 15 }} métodos más registrados.
                                            </small>
                                        </div>
                                        <p v-else class="text-muted small fst-italic">Sin métodos modificados.</p>

                                        <!-- Views preview -->
                                        <h6 class="font-weight-bold text-dark mb-2" style="font-size: 0.85rem;">
                                            Vistas & Componentes ({{ (form.views_changed?.length || 0) + (form.scripts_changed?.length || 0) }}):
                                        </h6>
                                        <div class="d-flex flex-wrap gap-1">
                                            <span
                                                v-for="(v, vIdx) in form.views_changed"
                                                :key="vIdx"
                                                class="badge badge-info text-truncate"
                                                style="max-width: 280px;"
                                            >
                                                {{ v.file }}
                                            </span>
                                            <span
                                                v-for="(s, sIdx) in form.scripts_changed"
                                                :key="sIdx"
                                                class="badge badge-warning text-truncate"
                                                style="max-width: 280px;"
                                            >
                                                {{ s.file }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    @click="store.closeNewVersionModal"
                                    :disabled="store.submitting"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    class="btn btn-primary d-flex align-items-center gap-2"
                                    :disabled="store.submitting"
                                >
                                    <span v-if="store.submitting" class="spinner-border spinner-border-sm" role="status"></span>
                                    <i v-else class="bi bi-check-circle-fill"></i>
                                    <span>Guardar y Publicar Versión</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
