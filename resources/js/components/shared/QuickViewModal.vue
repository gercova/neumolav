<script setup>
import { watch, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { useQuickViewStore } from '@/stores/quickViewStore';

const store = useQuickViewStore();
const {
    visible, loading, error, history, appointments, exams, routes, activeTab,
    appointmentsCount, examsCount, tipoAtencion, tipoAtencionColor, isArchived,
} = storeToRefs(store);

// Bloquea scroll del body cuando el modal está abierto
watch(visible, (v) => {
    document.body.classList.toggle('modal-open', v);
});
onBeforeUnmount(() => document.body.classList.remove('modal-open'));

const onBackdrop = (e) => {
    if (e.target.classList.contains('modal')) store.close();
};

const goTo = (url) => {
    if (!url || url === '#') return;
    window.open(url, '_blank');
};

const antecedenteBadge = (val) => {
    if (!store.isPositive(val)) {
        return { cls: 'badge-light border text-muted', text: 'No refiere' };
    }
    return { cls: 'badge-danger font-weight-bold', text: String(val) };
};
</script>

<template>
    <Teleport to="body">
        <div v-if="visible" class="modal fade show d-block" tabindex="-1"
             style="background: rgba(0,0,0,.5);"
             @click="onBackdrop" @keydown.esc="store.close()">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content border-0 shadow-lg">

                    <!-- ══════════════ HEADER ══════════════ -->
                    <div class="modal-header bg-light py-3 border-bottom">
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="bg-primary text-white rounded p-2 mr-3 d-flex align-items-center justify-content-center"
                                 style="width:40px; height:40px;">
                                <i class="bi bi-file-earmark-person-fill" style="font-size:1.3rem;"></i>
                            </div>
                            <div>
                                <h5 class="modal-title font-weight-bold text-dark mb-0 d-inline-block">
                                    {{ history?.nombres || 'Cargando...' }}
                                </h5>
                                <span class="badge badge-light border text-secondary ml-2 font-weight-normal py-1 px-2">
                                    <i class="bi bi-card-text mr-1"></i>
                                    {{ history?.tipo_documento || 'DNI' }}: {{ history?.dni || '--' }}
                                </span>
                                <span v-if="tipoAtencion"
                                      class="badge ml-1 font-weight-normal py-1 px-2"
                                      :class="tipoAtencionColor">
                                    <i class="bi bi-tag-fill mr-1"></i>{{ tipoAtencion }}
                                </span>
                                <span v-if="isArchived"
                                      class="badge badge-danger ml-1 font-weight-normal py-1 px-2">
                                    <i class="bi bi-archive-fill mr-1"></i>Historia Archivada
                                </span>
                            </div>
                        </div>
                        <button type="button" class="close" @click="store.close()">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- ══════════════ BODY ══════════════ -->
                    <div class="modal-body p-4">
                        <!-- LOADING -->
                        <div v-if="loading" class="text-center py-5">
                            <div class="spinner-border text-primary" style="width:3rem; height:3rem;"></div>
                            <h6 class="text-muted font-weight-bold mt-3">Cargando historia clínica y controles...</h6>
                            <p class="text-muted small">Recuperando registros históricos del paciente.</p>
                        </div>

                        <!-- ERROR -->
                        <div v-else-if="error" class="text-center py-5 text-danger">
                            <i class="bi bi-exclamation-triangle-fill" style="font-size:3rem;"></i>
                            <h5 class="font-weight-bold mt-2">No se pudo cargar la historia clínica</h5>
                            <p class="small text-muted mb-3">{{ error }}</p>
                            <button class="btn btn-secondary btn-sm" @click="store.close()">Cerrar</button>
                        </div>

                        <!-- CONTENT -->
                        <template v-else-if="history">
                            <!-- Resumen rápido -->
                            <div class="p-3 mb-3" style="background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;">
                                <div class="row text-secondary small">
                                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                                        <div class="text-muted mb-1"><i class="bi bi-cake2 text-info mr-1"></i> Edad / Nacimiento:</div>
                                        <div class="font-weight-bold text-dark h6 mb-0">
                                            {{ history.edad ?? '--' }} años
                                            <small class="text-muted font-weight-normal">({{ history.fecha_nacimiento || '--' }})</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 mb-2 mb-md-0">
                                        <div class="text-muted mb-1"><i class="bi bi-gender-ambiguous text-secondary mr-1"></i> Sexo:</div>
                                        <div class="font-weight-bold text-dark mb-0">{{ history.sexo || '--' }}</div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                                        <div class="text-muted mb-1"><i class="bi bi-telephone text-success mr-1"></i> Teléfono:</div>
                                        <div class="font-weight-bold text-dark mb-0">
                                            <a v-if="history.telefono && history.telefono !== '--'"
                                               :href="`tel:${history.telefono}`"
                                               class="text-dark text-decoration-none">
                                                {{ history.telefono }}
                                            </a>
                                            <span v-else class="text-muted">--</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 mb-2 mb-md-0">
                                        <div class="text-muted mb-1"><i class="bi bi-droplet-half text-danger mr-1"></i> Gr. Sanguíneo:</div>
                                        <div class="font-weight-bold text-dark mb-0">{{ history.grupo_sanguineo || '--' }}</div>
                                    </div>
                                    <div class="col-md-2 col-12">
                                        <div class="text-muted mb-1"><i class="bi bi-briefcase text-primary mr-1"></i> Ocupación:</div>
                                        <div class="font-weight-bold text-dark text-truncate mb-0">{{ history.ocupacion || '--' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabs -->
                            <ul class="nav nav-tabs font-weight-bold mb-3">
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-3"
                                       :class="{ active: activeTab === 'controles' }"
                                       href="#" @click.prevent="store.setTab('controles')">
                                        <i class="bi bi-clock-history text-primary mr-1"></i> Controles Clínicos Previos
                                        <span class="badge badge-primary ml-1">{{ appointmentsCount }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-3"
                                       :class="{ active: activeTab === 'examenes' }"
                                       href="#" @click.prevent="store.setTab('examenes')">
                                        <i class="bi bi-file-earmark-medical text-success mr-1"></i> Exámenes Clínicos
                                        <span class="badge badge-success ml-1">{{ examsCount }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-3"
                                       :class="{ active: activeTab === 'antecedentes' }"
                                       href="#" @click.prevent="store.setTab('antecedentes')">
                                        <i class="bi bi-clipboard2-pulse text-danger mr-1"></i> Antecedentes y Perfil Clínico
                                    </a>
                                </li>
                            </ul>

                            <!-- ═════ TAB CONTROLES ═════ -->
                            <div v-show="activeTab === 'controles'">
                                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                    <div class="text-muted small">
                                        <i class="bi bi-info-circle text-info mr-1"></i>
                                        Listado ordenado del más reciente al más antiguo.
                                    </div>
                                    <button class="btn btn-sm btn-outline-success font-weight-bold"
                                            @click="goTo(routes.control_add)">
                                        <i class="bi bi-plus-circle mr-1"></i> Nuevo Control
                                    </button>
                                </div>

                                <div v-if="appointmentsCount > 0" class="table-responsive border rounded bg-white">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th class="text-center" style="width:45px;">#</th>
                                                <th style="width:175px;">Fecha / Atención</th>
                                                <th style="width:260px;">Diagnóstico(s)</th>
                                                <th>Síntomas / Motivo</th>
                                                <th>Plan / Tratamiento</th>
                                                <th class="text-center" style="width:140px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small">
                                            <tr v-for="ap in appointments" :key="ap.id"
                                                :class="{ 'table-danger': ap.is_deleted }">
                                                <td class="text-center align-middle font-weight-bold text-muted">{{ ap.index }}</td>
                                                <td class="align-middle">
                                                    <div class="font-weight-bold text-dark">
                                                        <i class="bi bi-calendar3 text-primary mr-1"></i>{{ ap.fecha_formato }}
                                                    </div>
                                                    <span v-if="ap.antiguedad"
                                                          class="badge badge-light border text-muted font-weight-normal d-block mt-1">
                                                        {{ ap.antiguedad }}
                                                    </span>
                                                    <span v-if="ap.is_deleted" class="badge badge-danger ml-1">Eliminado</span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="font-weight-bold text-dark">{{ ap.diagnostico || '--' }}</div>
                                                </td>
                                                <td class="align-middle text-muted">
                                                    <span class="d-inline-block text-truncate"
                                                          style="max-width:250px;" :title="ap.sintomas">
                                                        {{ ap.sintomas || '--' }}
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="d-inline-block text-truncate"
                                                          style="max-width:250px;"
                                                          :title="ap.tratamiento || ap.plan">
                                                        {{ ap.tratamiento || ap.plan || '--' }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="btn-group">
                                                        <button type="button"
                                                                class="btn btn-info btn-xs mr-1 qv-view-appointment"
                                                                :value="ap.id"
                                                                title="Ver receta e indicaciones">
                                                            <i class="bi bi-eye"></i> Receta
                                                        </button>
                                                        <div class="dropdown d-inline-block">
                                                            <button class="btn btn-default btn-xs dropdown-toggle"
                                                                    data-toggle="dropdown">
                                                                <i class="bi bi-printer"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a class="dropdown-item" :href="ap.print_a4" target="_blank">
                                                                    <i class="bi bi-file-earmark-pdf text-danger mr-1"></i> Imprimir A4
                                                                </a>
                                                                <a class="dropdown-item" :href="ap.print_a5" target="_blank">
                                                                    <i class="bi bi-file-earmark-pdf text-info mr-1"></i> Imprimir A5
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div v-else class="text-center py-5 border rounded bg-white mt-2">
                                    <i class="bi bi-clipboard-x text-muted" style="font-size:2.5rem; opacity:.6;"></i>
                                    <h6 class="font-weight-bold text-dark mt-2 mb-1">No registra controles clínicos previos</h6>
                                    <p class="text-muted small mb-3">Este paciente no cuenta con atenciones o recetas registradas anteriormente.</p>
                                    <button class="btn btn-sm btn-primary" @click="goTo(routes.control_add)">
                                        <i class="bi bi-plus-circle mr-1"></i> Registrar Primer Control Clínico
                                    </button>
                                </div>
                            </div>

                            <!-- ═════ TAB EXÁMENES ═════ -->
                            <div v-show="activeTab === 'examenes'">
                                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                    <div class="text-muted small">
                                        <i class="bi bi-info-circle text-info mr-1"></i>
                                        Exámenes activos ordenados del más reciente al más antiguo.
                                    </div>
                                    <button class="btn btn-sm btn-outline-success font-weight-bold"
                                            @click="goTo(routes.exam_add)">
                                        <i class="bi bi-plus-circle mr-1"></i> Nuevo Examen
                                    </button>
                                </div>

                                <div v-if="examsCount > 0" class="table-responsive border rounded bg-white">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th class="text-center" style="width:45px;">#</th>
                                                <th style="width:175px;">Fecha / Registro</th>
                                                <th style="width:120px;">Tipo</th>
                                                <th>Diagnóstico(s)</th>
                                                <th>Plan / Observaciones</th>
                                                <th class="text-center" style="width:140px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small">
                                            <tr v-for="ex in exams" :key="ex.id">
                                                <td class="text-center align-middle font-weight-bold text-muted">{{ ex.index }}</td>
                                                <td class="align-middle">
                                                    <div class="font-weight-bold text-dark">
                                                        <i class="bi bi-calendar3 text-success mr-1"></i>{{ ex.fecha_formato }}
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge badge-light border text-dark font-weight-normal">{{ ex.tipo }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="font-weight-bold text-dark">{{ ex.diagnostico || '--' }}</div>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="d-inline-block text-truncate"
                                                          style="max-width:220px;"
                                                          :title="ex.plan || ex.otros">
                                                        {{ ex.plan || ex.otros || '--' }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="btn-group">
                                                        <a :href="ex.edit_url" target="_blank"
                                                           class="btn btn-info btn-xs mr-1" title="Ver / Editar examen">
                                                            <i class="bi bi-eye"></i> Ver
                                                        </a>
                                                        <div class="dropdown d-inline-block">
                                                            <button class="btn btn-default btn-xs dropdown-toggle"
                                                                    data-toggle="dropdown">
                                                                <i class="bi bi-printer"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a class="dropdown-item" :href="ex.print_a4" target="_blank">
                                                                    <i class="bi bi-file-earmark-pdf text-danger mr-1"></i> Imprimir A4
                                                                </a>
                                                                <a class="dropdown-item" :href="ex.print_a5" target="_blank">
                                                                    <i class="bi bi-file-earmark-pdf text-info mr-1"></i> Imprimir A5
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div v-else class="text-center py-5 border rounded bg-white mt-2">
                                    <i class="bi bi-file-earmark-x text-muted" style="font-size:2.5rem; opacity:.6;"></i>
                                    <h6 class="font-weight-bold text-dark mt-2 mb-1">No registra exámenes clínicos</h6>
                                    <p class="text-muted small mb-3">Este paciente no cuenta con exámenes registrados.</p>
                                    <button class="btn btn-sm btn-success" @click="goTo(routes.exam_add)">
                                        <i class="bi bi-plus-circle mr-1"></i> Registrar Primer Examen
                                    </button>
                                </div>
                            </div>

                            <!-- ═════ TAB ANTECEDENTES ═════ -->
                            <div v-show="activeTab === 'antecedentes'">
                                <div class="row">
                                    <!-- Patología Respiratoria -->
                                    <div class="col-md-6 col-12 mb-3">
                                        <div class="card card-outline card-info h-100 mb-0">
                                            <div class="card-header py-2">
                                                <h6 class="card-title font-weight-bold text-dark mb-0">
                                                    Patología Respiratoria y Pulmonar
                                                </h6>
                                            </div>
                                            <div class="card-body p-3 small">
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Asma Bronquial:</div>
                                                    <div class="col-6">
                                                        <span class="badge"
                                                              :class="antecedenteBadge(history.antecedentes?.asma).cls">
                                                            {{ antecedenteBadge(history.antecedentes?.asma).text }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">EPOC:</div>
                                                    <div class="col-6">
                                                        <span class="badge"
                                                              :class="antecedenteBadge(history.antecedentes?.epoc).cls">
                                                            {{ antecedenteBadge(history.antecedentes?.epoc).text }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Tuberculosis (TBC):</div>
                                                    <div class="col-6">
                                                        <span class="badge"
                                                              :class="antecedenteBadge(history.antecedentes?.tuberculosis).cls">
                                                            {{ antecedenteBadge(history.antecedentes?.tuberculosis).text }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Cáncer de Pulmón:</div>
                                                    <div class="col-6">
                                                        <span class="badge"
                                                              :class="antecedenteBadge(history.antecedentes?.cancerpulmon).cls">
                                                            {{ antecedenteBadge(history.antecedentes?.cancerpulmon).text }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Neumonías previas:</div>
                                                    <div class="col-6">
                                                        <span class="badge"
                                                              :class="antecedenteBadge(history.antecedentes?.neumonias).cls">
                                                            {{ antecedenteBadge(history.antecedentes?.neumonias).text }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6 text-muted">Efusión Pleural:</div>
                                                    <div class="col-6">
                                                        <span class="badge"
                                                              :class="antecedenteBadge(history.antecedentes?.efusionpleural).cls">
                                                            {{ antecedenteBadge(history.antecedentes?.efusionpleural).text }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tabaquismo y Exposición -->
                                    <div class="col-md-6 col-12 mb-3">
                                        <div class="card card-outline card-warning h-100 mb-0">
                                            <div class="card-header py-2">
                                                <h6 class="card-title font-weight-bold text-dark mb-0">
                                                    Tabaquismo y Factores de Exposición
                                                </h6>
                                            </div>
                                            <div class="card-body p-3 small">
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Condición de Tabaquismo:</div>
                                                    <div class="col-6 font-weight-bold text-dark">
                                                        {{ history.antecedentes?.tabaquismo || 'No' }}
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Índice Paquetes-Año (IPA):</div>
                                                    <div class="col-6">
                                                        <span v-if="history.antecedentes?.ipa"
                                                              class="badge badge-warning text-dark font-weight-bold">
                                                            {{ history.antecedentes.ipa }} paquetes/año
                                                        </span>
                                                        <span v-else class="text-muted">No registrado</span>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Contacto TBC:</div>
                                                    <div class="col-6">
                                                        <span class="badge"
                                                              :class="antecedenteBadge(history.antecedentes?.contactotbc).cls">
                                                            {{ antecedenteBadge(history.antecedentes?.contactotbc).text }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Exposición a Biomasa:</div>
                                                    <div class="col-6">
                                                        <span class="badge"
                                                              :class="antecedenteBadge(history.antecedentes?.biomasa).cls">
                                                            {{ antecedenteBadge(history.antecedentes?.biomasa).text }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6 text-muted">Alergias / RAM:</div>
                                                    <div class="col-6 font-weight-bold text-danger">
                                                        {{ history.antecedentes?.alergias_drogas || 'No refiere alergias' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hospitalizaciones y Otros -->
                                    <div class="col-12 mb-3">
                                        <div class="card card-outline card-secondary mb-0">
                                            <div class="card-header py-2">
                                                <h6 class="card-title font-weight-bold text-dark mb-0">
                                                    Hospitalizaciones, Cirugías y Medicación Habitual
                                                </h6>
                                            </div>
                                            <div class="card-body p-3 small">
                                                <div class="row">
                                                    <div class="col-md-6 col-12 mb-2">
                                                        <div class="text-muted font-weight-bold mb-1">Cirugías previas:</div>
                                                        <div class="text-dark border rounded p-2 bg-light">
                                                            {{ history.antecedentes?.cirugias || 'No refiere cirugías' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-2">
                                                        <div class="text-muted font-weight-bold mb-1">Hospitalizaciones:</div>
                                                        <div class="text-dark border rounded p-2 bg-light">
                                                            {{ history.antecedentes?.hospitalizaciones || 'No refiere hospitalizaciones previas' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-2">
                                                        <div class="text-muted font-weight-bold mb-1">Medicación habitual:</div>
                                                        <div class="text-dark border rounded p-2 bg-light">
                                                            {{ history.antecedentes?.medicacion_habitual || 'Ninguna medicación continua registrada' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-2">
                                                        <div class="text-muted font-weight-bold mb-1">Otros / Transfusiones:</div>
                                                        <div class="text-dark border rounded p-2 bg-light">
                                                            {{ history.antecedentes?.otros || 'Sin antecedentes adicionales' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Motivo y Relato -->
                                    <div class="col-12">
                                        <div class="card card-outline card-primary mb-0">
                                            <div class="card-header py-2">
                                                <h6 class="card-title font-weight-bold text-dark mb-0">
                                                    Motivo de Consulta y Relato Cronológico Inicial
                                                </h6>
                                            </div>
                                            <div class="card-body p-3 small">
                                                <div class="mb-2">
                                                    <div class="text-muted font-weight-bold mb-1">Motivo de consulta:</div>
                                                    <div class="text-dark font-italic">
                                                        {{ history.antecedentes?.motivoconsulta || 'No especificado en filiación inicial' }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="text-muted font-weight-bold mb-1">Relato cronológico:</div>
                                                    <div class="text-dark">
                                                        {{ history.antecedentes?.relatocronologico || 'Sin relato cronológico registrado' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- ══════════════ FOOTER ══════════════ -->
                    <div class="modal-footer bg-light py-2 justify-content-between">
                        <div>
                            <button class="btn btn-primary mr-2" @click="goTo(routes.history_edit)">
                                Ver / Editar Historia Completa
                            </button>
                            <button class="btn btn-outline-secondary mr-2" @click="goTo(routes.exam_add)">
                                <i class="bi bi-activity mr-1"></i> Nuevo Examen
                            </button>
                            <button class="btn btn-outline-secondary" @click="goTo(routes.report_add)">
                                <i class="bi bi-file-earmark-text mr-1"></i> Nuevo Informe
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-secondary" @click="store.close()">
                                <i class="bi bi-x-circle mr-1"></i> Cerrar
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </Teleport>
</template>