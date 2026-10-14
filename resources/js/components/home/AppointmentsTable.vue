<script setup>
import { storeToRefs } from 'pinia';
import { useCitasStore } from '@/stores/citasStore';
import { useQuickViewStore } from '@/stores/quickViewStore';
import AppointmentActions from './AppointmentActions.vue';

const store = useCitasStore();
const quickView = useQuickViewStore();
const { citas, loading } = storeToRefs(store);

const escapeHtml = (t) => !t ? '' : String(t)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;').replace(/'/g, '&#039;');

const statusBadge = (id, desc) => {
    const map = {
        1: ['badge-warning text-dark', 'bi-hourglass-split', 'PENDIENTE'],
        2: ['badge-primary',           'bi-calendar-check', 'CONFIRMADO'],
        3: ['badge-danger',            'bi-x-circle',       'CANCELADO'],
        4: ['badge-secondary',         'bi-arrow-repeat',   'REAGENDADO'],
        5: ['badge-dark',              'bi-person-x',       'NO ASISTIÓ'],
        6: ['badge-success',           'bi-check2-circle',  'ATENDIDO'],
        7: ['badge-info',              'bi-clock',          'EN ESPERA'],
    };
    const [cls, ico, txt] = map[+id] || ['badge-light border', '', desc];
    return { cls, ico, txt };
};

const openQuickView = (historyId, patientName) => {
    if (!historyId) {
        window.Swal?.fire({ icon: 'warning', title: 'Historia no encontrada',
            text: `El paciente ${patientName || ''} no tiene historia vinculada.` });
        return;
    }
    // Puente al modal jQuery existente
    quickView.open(historyId); 
};

const openScheduleFromEmpty = () => {
    window.$('#modal_schedule_appointment').modal('show');
};
</script>

<template>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title m-0 text-dark font-weight-bold">
                <i class="bi bi-list-ol text-primary"></i> Cola de Citas y Turnos
            </h5>
            <button class="btn btn-outline-secondary btn-sm" @click="store.refresh()">
                <i class="bi bi-arrow-clockwise" :class="{ 'fa-spin': loading }"></i> Actualizar
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:80px" class="text-center">Turno</th>
                            <th>Paciente</th>
                            <th>Documento</th>
                            <th>Teléfono</th>
                            <th>Hora / Llegada</th>
                            <th>Motivo de Cita</th>
                            <th style="width:130px" class="text-center">Estado</th>
                            <th style="width:170px" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="8" class="text-center py-4 text-muted">
                                <span class="spinner-border spinner-border-sm text-primary mr-2"></span>
                                Cargando lista de turnos...
                            </td>
                        </tr>

                        <tr v-else-if="citas.length === 0">
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-calendar-x text-muted" style="font-size:2.5rem;opacity:.6"></i>
                                <h6 class="text-dark font-weight-bold mt-2">No hay pacientes agendados para este día</h6>
                                <p class="text-muted small mb-3">La cola de atención está vacía.</p>
                                <button class="btn btn-primary btn-sm font-weight-bold" @click="openScheduleFromEmpty">
                                    <i class="bi bi-plus-circle"></i> Agendar Cita para este día
                                </button>
                            </td>
                        </tr>

                        <tr v-for="c in citas" :key="c.id"
                            @click="openQuickView(c.history_id, c.nombres)"
                            style="cursor:pointer">
                            <td class="text-center align-middle">
                                <span class="badge badge-turno shadow-xs">#{{ c.turn_number }}</span>
                            </td>
                            <td class="align-middle">
                                <span class="font-weight-bold text-primary">{{ c.nombres }}</span>
                                <span v-html="c.tipo_atencion_badge"></span>
                            </td>
                            <td class="align-middle"><span class="badge badge-light border">{{ c.dni }}</span></td>
                            <td class="align-middle text-muted">
                                <template v-if="c.telefono && c.telefono !== '--'">
                                    <i class="bi bi-telephone text-secondary mr-1"></i>{{ c.telefono }}
                                </template>
                                <template v-else>--</template>
                            </td>
                            <td class="align-middle">
                                <i class="bi bi-clock text-info mr-1"></i>
                                <span class="font-weight-bold" :class="c.hora_raw ? 'text-dark' : 'text-muted'">{{ c.hora_cita }}</span>
                            </td>
                            <td class="align-middle">
                                <span class="text-truncate d-inline-block" style="max-width:220px" :title="c.motivo">{{ c.motivo }}</span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge font-weight-bold px-2 py-1"
                                      :class="statusBadge(c.status_id, c.status_desc).cls">
                                    <i class="bi" :class="statusBadge(c.status_id, c.status_desc).ico"></i>
                                    {{ statusBadge(c.status_id, c.status_desc).txt }}
                                </span>
                            </td>
                            <td class="text-center align-middle" @click.stop>
                                <AppointmentActions :cita="c" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top py-2">
            <small class="text-muted">
                <i class="bi bi-info-circle"></i> <b>Criterio FIFO:</b> turnos ordenados estrictamente por llegada.
            </small>
        </div>
    </div>
</template>