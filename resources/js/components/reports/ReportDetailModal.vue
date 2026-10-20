<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    visible: {
        type: Boolean,
        default: false,
    },
    id: {
        type: Number,
        default: null,
    },
    fallbackDni: {
        type: String,
        default: '',
    },
    fallbackNombres: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['close']);

const loading = ref(false);
const error = ref(null);
const detailData = ref(null);
const includeSignature = ref(true);

const apiBase = () => window.API_BASE_URL || '/admin';

const formatDate = (dateStr) => {
    if (!dateStr) return '--';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return String(dateStr);
    const pad = (n) => String(n).padStart(2, '0');
    return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()}, ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
};

const report = computed(() => detailData.value?.rp || null);
const patient = computed(() => {
    if (!detailData.value?.hc) return null;
    return Array.isArray(detailData.value.hc) ? detailData.value.hc[0] : detailData.value.hc;
});

const dni = computed(() => report.value?.dni || patient.value?.dni || props.fallbackDni || '--');
const nombres = computed(() => patient.value?.nombres || props.fallbackNombres || '--');
const recordId = computed(() => report.value?.id || props.id || '');
const formattedDate = computed(() => formatDate(report.value?.created_at));

const diagnostics = computed(() => detailData.value?.dx || []);

const modalTitle = computed(() => `Detalles del Examen ${dni.value}-${recordId.value}`);

const formatFieldText = (val, addDash = true) => {
    if (!val || val === '-' || val === '--') return '--';
    const trimmed = String(val).trim();
    if (/<[a-z][\s\S]*>/i.test(trimmed)) {
        return trimmed;
    }
    if (addDash && !trimmed.startsWith('--')) {
        return `-- ${trimmed}`;
    }
    return trimmed;
};

const fetchDetail = async (id) => {
    if (!id) return;
    loading.value = true;
    error.value = null;
    detailData.value = null;
    try {
        const { data } = await axios.get(`${apiBase()}/reports/viewDetail/${id}`);
        detailData.value = data;
    } catch (err) {
        console.error('Error al cargar detalles del informe:', err);
        error.value = 'No se pudieron cargar los detalles del informe.';
    } finally {
        loading.value = false;
    }
};

watch(() => [props.visible, props.id], ([isVisible, newId]) => {
    if (isVisible && newId) {
        document.body.classList.add('modal-open');
        fetchDetail(newId);
    } else {
        document.body.classList.remove('modal-open');
    }
});

const onKeydown = (e) => {
    if (e.key === 'Escape' && props.visible) {
        emit('close');
    }
};

if (typeof window !== 'undefined') {
    window.addEventListener('keydown', onKeydown);
}

onBeforeUnmount(() => {
    document.body.classList.remove('modal-open');
    if (typeof window !== 'undefined') {
        window.removeEventListener('keydown', onKeydown);
    }
});

const onBackdrop = (e) => {
    if (e.target.classList.contains('modal')) {
        emit('close');
    }
};

const doPrint = () => {
    const sig = includeSignature.value ? '1' : '0';
    window.open(`${apiBase()}/reports/print/${props.id}?signature=${sig}`, '_blank');
};
</script>

<template>
    <Teleport to="body">
        <div
            v-if="visible"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0, 0, 0, 0.5); z-index: 1050;"
            @click="onBackdrop"
        >
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 8px;">
                    <!-- HEADER -->
                    <div class="modal-header py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="modal-title font-weight-bold text-dark mb-0" style="font-size: 1.15rem;">
                            {{ modalTitle }}
                        </h5>
                        <button
                            type="button"
                            class="close text-muted"
                            aria-label="Cerrar"
                            style="font-size: 1.5rem; outline: none;"
                            @click="emit('close')"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- BODY -->
                    <div class="modal-body p-4">
                        <div v-if="loading" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-muted small mt-2 mb-0">Cargando reporte clínico...</p>
                        </div>

                        <div v-else-if="error" class="alert alert-danger mb-0">
                            <i class="bi bi-exclamation-triangle mr-2"></i> {{ error }}
                        </div>

                        <div v-else-if="report">
                            <!-- Top Info Bar: DNI & Fecha -->
                            <div class="border-top border-bottom py-2 mb-3">
                                <div class="row text-dark" style="font-size: 0.95rem;">
                                    <div class="col-sm-6 font-weight-bold">
                                        DNI: <span class="font-weight-normal">{{ dni }}</span>
                                    </div>
                                    <div class="col-sm-6 font-weight-bold text-sm-right mt-1 mt-sm-0">
                                        Fecha: <span class="font-weight-normal">{{ formattedDate }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Nombres y Apellidos -->
                            <p class="text-uppercase mb-3 font-weight-bold text-dark" style="font-size: 0.95rem;">
                                NOMBRES Y APELLIDOS: <span class="font-weight-normal">{{ nombres }}</span>
                            </p>

                            <hr class="my-3">

                            <!-- Antecedentes -->
                            <div class="mb-3">
                                <p class="mb-1 font-weight-bold text-dark" style="font-size: 0.95rem;">Antecedentes:</p>
                                <div class="report-content text-dark" v-html="formatFieldText(report.antecedentes, true)"></div>
                            </div>

                            <!-- Historial enfermedad -->
                            <div class="mb-3">
                                <p class="mb-1 font-weight-bold text-dark" style="font-size: 0.95rem;">Historial enfermedad:</p>
                                <div class="report-content text-dark" v-html="formatFieldText(report.historial_enfermedad, true)"></div>
                            </div>

                            <!-- Examen físico -->
                            <div class="mb-3">
                                <p class="mb-1 font-weight-bold text-dark" style="font-size: 0.95rem;">Examen físico:</p>
                                <div class="report-content text-dark" v-html="formatFieldText(report.examen_fisico, true)"></div>
                            </div>

                            <!-- Examen complementario -->
                            <div class="mb-3">
                                <p class="mb-1 font-weight-bold text-dark" style="font-size: 0.95rem;">Examen complementario:</p>
                                <div class="report-content text-dark" v-html="formatFieldText(report.examen_complementario, true)"></div>
                            </div>

                            <!-- Diagnóstico -->
                            <div class="mb-3">
                                <p class="mb-2 font-weight-bold text-dark" style="font-size: 0.95rem;">Diagnóstico:</p>
                                <div class="table-responsive mb-0">
                                    <table class="table table-hover table-sm border-bottom mb-0">
                                        <thead>
                                            <tr class="text-dark border-top-0 border-bottom" style="font-size: 0.9rem;">
                                                <th style="width: 60px;">#</th>
                                                <th>Diagnóstico</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="!diagnostics.length">
                                                <td colspan="2" class="text-muted small py-2">No registra diagnósticos.</td>
                                            </tr>
                                            <tr v-for="(item, index) in diagnostics" :key="index" style="font-size: 0.9rem;">
                                                <td class="text-dark">{{ index + 1 }}</td>
                                                <td class="text-dark">{{ item.diagnostic }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tratamiento -->
                            <div class="mb-3">
                                <p class="mb-1 font-weight-bold text-dark" style="font-size: 0.95rem;">Tratamiento:</p>
                                <div class="report-content text-dark" v-html="formatFieldText(report.tratamiento, true)"></div>
                            </div>

                            <!-- Sugerencia -->
                            <div class="mb-3">
                                <p class="mb-1 font-weight-bold text-dark" style="font-size: 0.95rem;">Sugerencia:</p>
                                <div class="report-content text-dark" v-html="formatFieldText(report.sugerencia, false)"></div>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer py-2 px-4 border-top d-flex justify-content-between align-items-center">
                        <button
                            type="button"
                            class="btn btn-danger btn-sm px-3"
                            style="border-radius: 4px;"
                            @click="emit('close')"
                        >
                            Cerrar
                        </button>

                        <div class="d-flex align-items-center">
                            <div class="custom-control custom-checkbox mr-3 d-inline-flex align-items-center">
                                <input
                                    id="reportSignatureCheck"
                                    v-model="includeSignature"
                                    type="checkbox"
                                    class="custom-control-input"
                                >
                                <label
                                    class="custom-control-label font-weight-normal text-dark cursor-pointer user-select-none"
                                    for="reportSignatureCheck"
                                    style="font-size: 0.88rem;"
                                >
                                    Incluir firma digital
                                </label>
                            </div>

                            <button
                                type="button"
                                class="btn btn-primary btn-sm d-inline-flex align-items-center px-3"
                                style="border-radius: 4px;"
                                @click="doPrint"
                            >
                                <i class="bi bi-file-earmark-text mr-1"></i> Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.report-content :deep(p) {
    margin-bottom: 0.25rem;
}
.report-content :deep(p:last-child) {
    margin-bottom: 0;
}
.report-content :deep(ol),
.report-content :deep(ul) {
    margin-bottom: 0.25rem;
    padding-left: 1.25rem;
}
.cursor-pointer {
    cursor: pointer;
}
</style>
