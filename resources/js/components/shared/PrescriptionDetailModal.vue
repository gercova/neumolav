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
    type: {
        type: String,
        required: true, // 'appointments' | 'exams'
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

const apiBase = () => window.API_BASE_URL || '/admin';

const formatDate = (dateStr) => {
    if (!dateStr) return '--';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return String(dateStr);
    const pad = (n) => String(n).padStart(2, '0');
    return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()}, ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
};

const record = computed(() => {
    if (!detailData.value) return null;
    return props.type === 'appointments' ? detailData.value.ap : detailData.value.ex;
});

const patient = computed(() => {
    if (!detailData.value?.hc) return null;
    return Array.isArray(detailData.value.hc) ? detailData.value.hc[0] : detailData.value.hc;
});

const dni = computed(() => record.value?.dni || patient.value?.dni || props.fallbackDni || '--');
const nombres = computed(() => patient.value?.nombres || props.fallbackNombres || '--');
const recordId = computed(() => record.value?.id || props.id || '');
const formattedDate = computed(() => formatDate(record.value?.created_at));

const diagnostics = computed(() => detailData.value?.diagnostic || []);
const medications = computed(() => detailData.value?.medication || []);

const printUrlA4 = computed(() => `${apiBase()}/${props.type}/print/${props.id}/a4`);
const printUrlA5 = computed(() => `${apiBase()}/${props.type}/print/${props.id}/a5`);

const modalTitle = computed(() => {
    const prefix = props.type === 'appointments' ? 'Detalles de la Cita' : 'Detalles del Examen';
    return `${prefix} ${dni.value}-${recordId.value}`;
});

const fetchDetail = async (id) => {
    if (!id) return;
    loading.value = true;
    error.value = null;
    detailData.value = null;
    try {
        const { data } = await axios.get(`${apiBase()}/${props.type}/viewDetail/${id}`);
        detailData.value = data;
    } catch (err) {
        console.error(`Error al cargar detalles de ${props.type}:`, err);
        error.value = 'No se pudieron cargar los detalles del registro.';
    } finally {
        loading.value = false;
    }
};

watch(() => [props.visible, props.id], ([isVisible, newId]) => {
    if (isVisible && newId) {
        document.body.classList.add('modal-open');
        fetchDetail(newId);
    } else {
        setTimeout(() => {
            if (!document.querySelector('.modal.show')) {
                document.body.classList.remove('modal-open');
            }
        }, 50);
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
    setTimeout(() => {
        if (!document.querySelector('.modal.show')) {
            document.body.classList.remove('modal-open');
        }
    }, 50);
    if (typeof window !== 'undefined') {
        window.removeEventListener('keydown', onKeydown);
    }
});

const onBackdrop = (e) => {
    if (e.target.classList.contains('modal')) {
        emit('close');
    }
};
</script>

<template>
    <Teleport to="body">
        <div
            v-if="visible"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0, 0, 0, 0.5); z-index: 1060;"
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
                            <p class="text-muted small mt-2 mb-0">Cargando detalles...</p>
                        </div>

                        <div v-else-if="error" class="alert alert-danger mb-0">
                            <i class="bi bi-exclamation-triangle mr-2"></i> {{ error }}
                        </div>

                        <div v-else>
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

                            <!-- Diagnóstico -->
                            <h6 class="font-weight-bold text-dark mb-2" style="font-size: 0.95rem;">Diagnóstico:</h6>
                            <div class="table-responsive mb-4">
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

                            <!-- Receta -->
                            <h6 class="font-weight-bold text-dark mb-2" style="font-size: 0.95rem;">Receta:</h6>
                            <div class="table-responsive mb-2">
                                <table class="table table-hover table-sm border-bottom mb-0">
                                    <thead>
                                        <tr class="text-dark border-top-0 border-bottom" style="font-size: 0.9rem;">
                                            <th style="width: 60px;">#</th>
                                            <th style="width: 45%;">Fármaco</th>
                                            <th>Receta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!medications.length">
                                            <td colspan="3" class="text-muted small py-2">No registra medicamentos.</td>
                                        </tr>
                                        <tr v-for="(item, index) in medications" :key="index" style="font-size: 0.9rem;">
                                            <td class="text-dark">{{ index + 1 }}</td>
                                            <td class="text-dark font-weight-normal">{{ item.drug }}</td>
                                            <td class="text-dark font-weight-normal">{{ item.rp || '---' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer py-2 px-4 border-top d-flex justify-content-between align-items-center">
                        <button
                            type="button"
                            class="btn btn-outline-danger btn-sm d-inline-flex align-items-center px-3"
                            style="border-radius: 4px;"
                            @click="emit('close')"
                        >
                            <i class="bi bi-x-lg mr-1 text-danger"></i> Cerrar
                        </button>

                        <div class="d-flex flex-column align-items-end">
                            <span class="text-muted small mb-1" style="font-size: 0.72rem;">Formato de impresión</span>
                            <div class="d-flex align-items-center">
                                <a
                                    :href="printUrlA4"
                                    target="_blank"
                                    class="btn btn-primary btn-sm d-inline-flex align-items-center px-2 py-1 mr-2"
                                    style="border-radius: 4px; font-weight: 500;"
                                >
                                    <i class="bi bi-printer mr-1"></i> A4
                                    <span class="badge badge-light text-primary font-weight-bold ml-1 px-2 py-1" style="font-size: 0.72rem; border-radius: 3px;">
                                        Carta
                                    </span>
                                </a>
                                <a
                                    :href="printUrlA5"
                                    target="_blank"
                                    class="btn btn-info btn-sm d-inline-flex align-items-center px-2 py-1 text-white"
                                    style="border-radius: 4px; font-weight: 500; background-color: #17a2b8; border-color: #17a2b8;"
                                >
                                    <i class="bi bi-printer mr-1"></i> A5
                                    <span class="badge badge-light text-info font-weight-bold ml-1 px-2 py-1" style="font-size: 0.72rem; border-radius: 3px;">
                                        Medio
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
