<script setup>
import { ref, watch, computed, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { useCitasModalsStore } from '@/stores/citasModalsStore';
import { useCitasStore } from '@/stores/citasStore';

const props = defineProps({
    documentTypes: { type: Array, default: () => [] },
    sexes:         { type: Array, default: () => [] },
});

const modals = useCitasModalsStore();
const citas  = useCitasStore();
const { scheduleVisible, scheduleTab, searchQuery, searchLoading, searchResults,
        selectedPatient, existingTipoAtencion, existingDate, quick,
        reniecLoading, reniecFeedback, quickFeedback, submitting } = storeToRefs(modals);

// ── Body scroll lock ────────────────────────────────
watch(scheduleVisible, (v) => {
    document.body.classList.toggle('modal-open', v);
});
onBeforeUnmount(() => document.body.classList.remove('modal-open'));

const onBackdrop = (e) => {
    if (e.target.classList.contains('modal')) modals.closeSchedule();
};

// ── Búsqueda con debounce ───────────────────────────
let timer = null;
watch(searchQuery, (q) => {
    clearTimeout(timer);
    timer = setTimeout(() => modals.searchPatients(q.trim()), 320);
});

// ── Chips tipo atención ─────────────────────────────
const tipos = [
    { id: 1, label: 'Nuevo',       icon: 'bi-person-plus-fill',  cls: 'btn-outline-success' },
    { id: 2, label: 'Control',     icon: 'bi-arrow-repeat',      cls: 'btn-outline-primary' },
    { id: 3, label: 'Continuador', icon: 'bi-person-check-fill', cls: 'btn-outline-warning text-dark' },
];

// ── Submit existente ────────────────────────────────
const submitExisting = async () => {
    const targetDate = existingDate.value;
    const result = await modals.submitExisting();
    if (result?.status) {
        modals.closeSchedule();
        window.alertNotify?.('success', result.messages);
        await citas.refresh();
        window.__neumoEvents?.dispatchEvent(new CustomEvent('citas:refresh'));
        await handleDateChanged(result, targetDate);
    } else if (result?.messages) {
        window.Swal.fire('Aviso', result.messages, 'warning');
    }
};

// ── Submit quick patient ────────────────────────────
const submitQuick = async () => {
    const targetDate = quick.value.fecha_cita;
    const result = await modals.submitQuick();
    if (result?.status) {
        modals.closeSchedule();
        window.alertNotify?.('success', result.messages);
        await citas.refresh();
        window.__neumoEvents?.dispatchEvent(new CustomEvent('citas:refresh'));
        await handleDateChanged(result, targetDate);
    } else if (result?.messages) {
        window.Swal.fire('Aviso', result.messages, 'warning');
    }
};

// Después de crear la cita, si es para otra fecha, ofrecer navegar
const handleDateChanged = async (result, targetDate) => {
    if (!targetDate || targetDate === citas.currentDate) return;

    const r = await window.Swal.fire({
        title: '¡Cita programada!',
        text: `La cita fue asignada para el ${formatDateSpanish(targetDate)} (Turno #${result.numero_turno}). ¿Desea ir a esa fecha?`,
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: 'Sí, ver esa fecha',
        cancelButtonText: 'Permanecer aquí',
    });
    if (r.isConfirmed) {
        await citas.setDate(targetDate);
    }
};

const formatDateSpanish = (iso) => {
    if (!iso) return '';
    const [y, m, d] = iso.split('-');
    return `${d}/${m}/${y}`;
};

// Enter en campo DNI activa RENIEC
const onDniEnter = (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        modals.searchReniec();
    }
};
</script>

<template>
    <Teleport to="body">
        <div v-if="scheduleVisible" class="modal fade show d-block" tabindex="-1"
             style="background: rgba(0,0,0,.5);"
             @click="onBackdrop">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-calendar-plus text-primary"></i> Programar Cita Médica
                        </h5>
                        <button type="button" class="close" @click="modals.closeSchedule()">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-0">
                        <!-- TABS -->
                        <ul class="nav nav-tabs nav-justified bg-light">
                            <li class="nav-item">
                                <a class="nav-link py-3" :class="{ active: scheduleTab === 'search' }"
                                   href="#" @click.prevent="modals.setScheduleTab('search')">
                                    <i class="bi bi-search"></i> Buscar Paciente Existente
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3" :class="{ active: scheduleTab === 'quick' }"
                                   href="#" @click.prevent="modals.setScheduleTab('quick')">
                                    <i class="bi bi-person-plus-fill"></i> Registro Rápido
                                </a>
                            </li>
                        </ul>

                        <div class="p-4">
                            <!-- ══════════ TAB 1: BUSCAR EXISTENTE ══════════ -->
                            <div v-show="scheduleTab === 'search'">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Buscar Paciente en Base de Datos:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        </div>
                                        <input v-model="searchQuery" type="text" class="form-control"
                                               placeholder="Escriba DNI o Apellidos y Nombres..."
                                               autocomplete="off" :disabled="!!selectedPatient">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                    @click="searchQuery = ''">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        Ingrese al menos 2 caracteres para buscar.
                                    </small>
                                </div>

                                <!-- Resultados -->
                                <div v-if="searchResults.length"
                                     class="border rounded bg-white mb-3"
                                     style="max-height:190px; overflow-y:auto;">
                                    <div v-for="p in searchResults" :key="p.id"
                                         class="patient-search-result-item p-2 border-bottom"
                                         style="cursor:pointer;"
                                         @click="modals.selectPatient(p)">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="font-weight-bold text-dark">{{ p.nombres }}</div>
                                                <div class="small text-muted">
                                                    DNI: <span class="badge badge-light border">{{ p.dni }}</span>
                                                    | Celular: {{ p.telefono }} | Edad: {{ p.edad }}
                                                </div>
                                            </div>
                                            <span class="btn btn-xs btn-outline-primary">
                                                <i class="bi bi-check-lg"></i> Seleccionar
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tarjeta paciente seleccionado -->
                                <div v-if="selectedPatient"
                                     class="selected-patient-card d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="font-weight-bold text-success mb-1">{{ selectedPatient.nombres }}</h6>
                                        <div class="small text-muted">
                                            <b>DNI:</b> {{ selectedPatient.dni }} |
                                            <b>Teléfono:</b> {{ selectedPatient.telefono }} |
                                            <b>Edad:</b> {{ selectedPatient.edad }}
                                        </div>
                                    </div>
                                    <button class="btn btn-outline-danger btn-sm" @click="modals.clearSelectedPatient()">
                                        <i class="bi bi-x-circle"></i> Cambiar
                                    </button>
                                </div>

                                <!-- Aviso no encontrado -->
                                <div v-else-if="searchQuery.length >= 2 && !searchLoading && !searchResults.length"
                                     class="alert alert-light border mb-3 py-2 text-center">
                                    <span class="text-muted">¿No encuentra al paciente?</span>
                                    <a href="#" class="font-weight-bold text-primary ml-1"
                                       @click.prevent="modals.setScheduleTab('quick')">
                                        <i class="bi bi-person-plus"></i> Registrar nuevo paciente aquí
                                    </a>
                                </div>

                                <!-- Formulario -->
                                <form @submit.prevent="submitExisting">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Fecha de Cita: <span class="text-danger">*</span></label>
                                                <input v-model="existingDate" type="date" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="d-block font-weight-bold">Tipo de Atención: <span class="text-danger">*</span></label>
                                        <div class="btn-group btn-group-toggle w-100 chip-tipo-group">
                                            <label v-for="t in tipos" :key="t.id"
                                                   class="btn chip-tipo-btn flex-fill"
                                                   :class="[t.cls, existingTipoAtencion === t.id ? 'active' : '']"
                                                   @click.prevent="existingTipoAtencion = t.id">
                                                <i class="bi" :class="t.icon"></i> <b>{{ t.label }}</b>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="button" class="btn btn-secondary mr-2"
                                                @click="modals.closeSchedule()">Cancelar</button>
                                        <button type="submit" class="btn btn-primary"
                                                :disabled="!selectedPatient || submitting">
                                            <span v-if="submitting" class="spinner-border spinner-border-sm mr-1"></span>
                                            <i v-else class="bi bi-calendar-check"></i>
                                            Agendar Cita
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- ══════════ TAB 2: REGISTRO RÁPIDO ══════════ -->
                            <div v-show="scheduleTab === 'quick'">
                                <div class="alert alert-info py-2 small mb-3 border-0">
                                    <i class="bi bi-info-circle-fill"></i> Ingrese el DNI y presione
                                    <b>Buscar RENIEC</b> para completar automáticamente los nombres.
                                </div>

                                <form @submit.prevent="submitQuick">
                                    <div class="row">
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Tipo Documento: <span class="text-danger">*</span></label>
                                                <select v-model.number="quick.id_td" class="form-control" required>
                                                    <option v-for="td in documentTypes" :key="td.id" :value="td.id">
                                                        {{ td.descripcion }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Documento / DNI: <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input v-model="quick.dni" type="text" class="form-control"
                                                           placeholder="Ingrese DNI (8 dígitos)"
                                                           maxlength="8" required
                                                           @keypress="onDniEnter">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-info" type="button"
                                                                :disabled="reniecLoading"
                                                                @click="modals.searchReniec()">
                                                            <span v-if="reniecLoading" class="spinner-border spinner-border-sm mr-1"></span>
                                                            <i v-else class="bi bi-search"></i> Buscar RENIEC
                                                        </button>
                                                    </div>
                                                </div>
                                                <div v-if="reniecFeedback" class="text-danger small mt-1">{{ reniecFeedback }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Nombres y Apellidos: <span class="text-danger">*</span></label>
                                        <input v-model="quick.nombres" type="text"
                                               class="form-control text-uppercase"
                                               placeholder="APELLIDOS Y NOMBRES" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Fecha de Nacimiento: <span class="text-danger">*</span></label>
                                                <input v-model="quick.fecha_nacimiento" type="date"
                                                       class="form-control" required
                                                       :max="new Date().toISOString().slice(0,10)">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Sexo: <span class="text-danger">*</span></label>
                                                <select v-model="quick.id_sexo" class="form-control" required>
                                                    <option value="">Seleccione...</option>
                                                    <option v-for="s in sexes" :key="s.id" :value="s.id">
                                                        {{ s.descripcion }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Teléfono / Celular: <span class="text-danger">*</span></label>
                                        <input v-model="quick.telefono" type="tel" class="form-control"
                                               placeholder="Número de contacto (9 dígitos)"
                                               maxlength="11" required>
                                    </div>

                                    <hr class="my-3">
                                    <h6 class="font-weight-bold text-dark mb-3">
                                        <i class="bi bi-calendar2-event text-primary"></i> Datos de la Cita Inicial
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Fecha de Cita: <span class="text-danger">*</span></label>
                                                <input v-model="quick.fecha_cita" type="date" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label class="d-block font-weight-bold">Tipo de Atención: <span class="text-danger">*</span></label>
                                                <div class="btn-group btn-group-toggle w-100 chip-tipo-group">
                                                    <label v-for="t in tipos" :key="t.id"
                                                           class="btn chip-tipo-btn flex-fill"
                                                           :class="[t.cls, quick.id_tipo_atencion === t.id ? 'active' : '']"
                                                           @click.prevent="quick.id_tipo_atencion = t.id">
                                                        <i class="bi" :class="t.icon"></i> <b>{{ t.label }}</b>
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">Por defecto: Nuevo</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="quickFeedback" class="alert alert-danger py-2 small">{{ quickFeedback }}</div>

                                    <div class="text-right mt-3">
                                        <button type="button" class="btn btn-secondary mr-2"
                                                @click="modals.closeSchedule()">Cancelar</button>
                                        <button type="submit" class="btn btn-success" :disabled="submitting">
                                            <span v-if="submitting" class="spinner-border spinner-border-sm mr-1"></span>
                                            <i v-else class="bi bi-person-check-fill"></i>
                                            Registrar Paciente y Agendar Cita
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.selected-patient-card {
    background-color: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    padding: 14px;
    margin-bottom: 15px;
}
</style>