<script setup>
import { computed, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useCitasStore } from '@/stores/citasStore';
import { useCitasModalsStore } from '@/stores/citasModalsStore';

const props     = defineProps({ statuses: Array });
const store     = useCitasStore();
const modals    = useCitasModalsStore();
const { currentDate, filterStatus } = storeToRefs(store);

const fmt = (d) => {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
};

const today    = fmt(new Date());
const tomorrow = (() => { const d = new Date(); d.setDate(d.getDate() + 1); return fmt(d); })();

const goPrev = () => { const d = new Date(currentDate.value + 'T00:00:00'); d.setDate(d.getDate() - 1); store.setDate(fmt(d)); };
const goNext = () => { const d = new Date(currentDate.value + 'T00:00:00'); d.setDate(d.getDate() + 1); store.setDate(fmt(d)); };
const goToday    = () => store.setDate(today);
const goTomorrow = () => store.setDate(tomorrow);

const onDateChange = (e) => store.setDate(e.target.value);
const onStatusChange = (e) => store.setStatus(e.target.value);

const labelCurrent = computed(() => {
    const parts = currentDate.value?.split('-');
    if (!parts || parts.length !== 3) return '';
    const d = new Date(+parts[0], +parts[1] - 1, +parts[2]);
    const s = d.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    return s.charAt(0).toUpperCase() + s.slice(1);
});

// Notifica a componentes hijos (o a jQuery legacy si hace falta)
watch(currentDate, (v) => window.__neumoEvents.dispatchEvent(new CustomEvent('citas:date-changed', { detail: v })));

// Expone para Blade/legacy
defineExpose({ labelCurrent });
const openSchedule = () => modals.openSchedule(currentDate.value);
</script>
<template>
    <div class="date-navigation-bar">
        <div class="row align-items-center">
            <div class="col-lg-5 col-md-6 col-12 mb-2 mb-lg-0">
                <div class="d-flex align-items-center flex-wrap">
                    <div class="btn-group mr-2 mb-1" role="group">
                        <button class="btn btn-outline-secondary btn-sm" @click="goPrev"><i class="bi bi-chevron-left"></i></button>
                        <button class="btn btn-sm" :class="currentDate === today ? 'btn-primary' : 'btn-outline-secondary'" @click="goToday">
                            <i class="bi bi-calendar-check"></i> Hoy
                        </button>
                        <button class="btn btn-sm" :class="currentDate === tomorrow ? 'btn-primary' : 'btn-outline-secondary'" @click="goTomorrow">Mañana</button>
                        <button class="btn btn-outline-secondary btn-sm" @click="goNext"><i class="bi bi-chevron-right"></i></button>
                    </div>
                    <div class="input-group input-group-sm mb-1" style="width: 170px;">
                        <div class="input-group-prepend"><span class="input-group-text bg-white"><i class="bi bi-calendar3"></i></span></div>
                        <input type="date" class="form-control" :value="currentDate" @change="onDateChange">
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12 mb-2 mb-lg-0">
                <div class="d-flex align-items-center">
                    <label class="mr-2 mb-0 text-muted text-nowrap">Estado:</label>
                    <select class="form-control form-control-sm" :value="filterStatus" @change="onStatusChange">
                        <option value="">Todos los estados</option>
                        <option v-for="s in statuses" :key="s.id" :value="s.id">{{ s.descripcion }}</option>
                    </select>
                </div>
            </div>

            <!-- <div class="col-lg-4 col-12 text-lg-right text-left">
                <div class="btn-group flex-wrap">
                    <a href="/admin/histories/add" class="btn btn-primary btn-sm mr-2 mb-1">
                        <i class="bi bi-plus-circle"></i> Agregar nueva historia
                    </a>
                    <button type="button" class="btn btn-success btn-sm mb-1" id="btn_open_schedule_modal"
                            data-toggle="modal" data-target="#modal_schedule_appointment">
                        <i class="bi bi-calendar-plus"></i> Agendar Cita
                    </button>
                </div>
            </div> -->
            <div class="col-lg-4 col-12 text-lg-right text-left">
                <div class="btn-group flex-wrap">
                    <a href="/admin/histories/add" class="btn btn-primary btn-sm mr-2 mb-1">
                        <i class="bi bi-plus-circle"></i> Agregar nueva historia
                    </a>
                    <button type="button" class="btn btn-success btn-sm mb-1" @click="openSchedule">
                        <i class="bi bi-calendar-plus"></i> Agendar Cita
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-2 px-1">
            <span class="badge badge-light border font-weight-normal">
                <i class="bi bi-calendar3 text-primary"></i> {{ labelCurrent }}
            </span>
        </div>
    </div>
</template>