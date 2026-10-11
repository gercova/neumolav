<script setup>
import axios from 'axios';
import { ref } from 'vue';
import { useCitasStore } from '@/stores/citasStore';

const props = defineProps({ cita: Object });
const store = useCitasStore();
const busy = ref(false);

const API = window.API_BASE_URL;

const emitChange = () => store.refresh();

const changeStatus = async (statusId) => {
    busy.value = true;
    try {
        await axios.post(`${API}/citas/${props.cita.id}/status`, { id_estado: statusId, _token: window.token });
        window.alertNotify?.('success', 'Estado actualizado');
        emitChange();
    } catch (e) {
        window.Swal?.fire('Error', 'No se pudo cambiar el estado.', 'error');
    } finally { busy.value = false; }
};

const reschedule = () => {
    // Dispara el modal jQuery existente
    window.__neumoEvents.dispatchEvent(new CustomEvent('open-reschedule', { detail: props.cita }));
};

const changeTipo = () => {
    window.__neumoEvents.dispatchEvent(new CustomEvent('open-change-tipo', { detail: props.cita }));
};

const remove = async () => {
    const r = await window.Swal.fire({
        title: '¿Eliminar cita?', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc2626',
    });
    if (!r.isConfirmed) return;
    try {
        await axios.delete(`${API}/citas/${props.cita.id}`, { data: { _token: window.token } });
        window.alertNotify?.('success', 'Cita eliminada');
        emitChange();
    } catch { window.Swal?.fire('Error', 'No se pudo eliminar.', 'error'); }
};
</script>

<template>
    <div class="dropdown d-inline-block">
        <button class="btn btn-sm btn-outline-secondary" data-toggle="dropdown" :disabled="busy">
            <i class="bi bi-three-dots-vertical"></i> Acciones
        </button>
        <div class="dropdown-menu dropdown-menu-right">
            <a v-if="cita.status_id !== 7" class="dropdown-item" href="#" @click.prevent="changeStatus(7)">
                <i class="bi bi-clock text-info"></i> Pasar a 'En Espera'
            </a>
            <a v-if="cita.status_id !== 6" class="dropdown-item" href="#" @click.prevent="changeStatus(6)">
                <i class="bi bi-check2-circle text-success"></i> Marcar 'Atendido'
            </a>
            <a v-if="cita.status_id !== 1" class="dropdown-item" href="#" @click.prevent="changeStatus(1)">
                <i class="bi bi-hourglass-split text-warning"></i> Marcar 'Pendiente'
            </a>
            <a v-if="cita.status_id !== 3" class="dropdown-item text-danger" href="#" @click.prevent="changeStatus(3)">
                <i class="bi bi-x-circle"></i> Cancelar Cita
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" @click.prevent="changeTipo">
                <i class="bi bi-tag-fill text-primary"></i> Cambiar Tipo de Atención
            </a>
            <a class="dropdown-item" href="#" @click.prevent="reschedule">
                <i class="bi bi-arrow-repeat text-warning"></i> Reagendar Fecha
            </a>
            <div class="dropdown-divider"></div>
            <a v-if="cita.routes?.control_add" class="dropdown-item" :href="cita.routes.control_add">
                <i class="bi bi-journal-plus text-primary"></i> Nuevo Control
            </a>
            <a v-if="cita.routes?.exam_add" class="dropdown-item" :href="cita.routes.exam_add">
                <i class="bi bi-file-earmark-medical text-primary"></i> Nuevo Examen
            </a>
            <a v-if="cita.routes?.report_add" class="dropdown-item" :href="cita.routes.report_add">
                <i class="bi bi-file-earmark-text text-primary"></i> Nuevo Informe
            </a>
            <a v-if="cita.routes?.history_edit" class="dropdown-item" :href="cita.routes.history_edit">
                <i class="bi bi-person-lines-fill text-primary"></i> Editar Historia
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" href="#" @click.prevent="remove">
                <i class="bi bi-trash"></i> Eliminar Registro
            </a>
        </div>
    </div>
</template>