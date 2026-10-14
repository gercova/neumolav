<script setup>
import { onMounted, onBeforeUnmount } from 'vue';
import { useCitasStore } from '@/stores/citasStore';
import { openChangeTipoAtencionModal } from '@/composables/useTipoAtencionModal';
import GlobalStats from './GlobalStats.vue';
import DailyStats from './DailyStats.vue';
import DateNavigator from './DateNavigator.vue';
import AppointmentsTable from './AppointmentsTable.vue';
import ScheduleAppointmentModal from './ScheduleAppointmentModal.vue';
import RescheduleAppointmentModal from './RescheduleAppointmentModal.vue';

const props = defineProps({
    initialDate:  { type: String, required: true },
    globalStats:  { type: Object, required: true },
    statuses:     { type: Array, default: () => [] },
    documentTypes: { type: Array, default: () => [] },
    sexes:         { type: Array, default: () => [] },
});

const citasStore = useCitasStore();
let pollInterval = null;

const onRefreshEvent = () => {
    citasStore.refresh(true);
};

const onVisibilityChange = () => {
    if (!document.hidden) {
        citasStore.refresh(true);
    }
};

const onOpenChangeTipo = (event) => {
    if (event?.detail) {
        openChangeTipoAtencionModal(event.detail, () => citasStore.refresh(false));
    }
};

onMounted(() => {
    citasStore.currentDate = props.initialDate;
    citasStore.refresh(false);

    // Actualización automática periódica (cada 15s) en segundo plano
    pollInterval = setInterval(() => {
        if (!document.hidden && !citasStore.loading) {
            citasStore.refresh(true);
        }
    }, 15000);

    document.addEventListener('visibilitychange', onVisibilityChange);
    window.__neumoEvents?.addEventListener('citas:refresh', onRefreshEvent);
    window.__neumoEvents?.addEventListener('open-change-tipo', onOpenChangeTipo);
});

onBeforeUnmount(() => {
    if (pollInterval) clearInterval(pollInterval);
    document.removeEventListener('visibilitychange', onVisibilityChange);
    window.__neumoEvents?.removeEventListener('citas:refresh', onRefreshEvent);
    window.__neumoEvents?.removeEventListener('open-change-tipo', onOpenChangeTipo);
});
</script>

<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0 text-dark">
                    <i class="bi bi-speedometer2 text-primary"></i> Dashboard
                    <small class="text-muted font-weight-normal ml-2" style="font-size: 1rem;">
                        | Panel de Control y Citas
                    </small>
                </h1>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- 1. Indicadores globales (server-rendered, pasa como prop) -->
                <GlobalStats :stats="globalStats" />

                <!-- 2. Estadísticas del día (fetch AJAX) -->
                <DailyStats />

                <!-- 3. Barra de fecha + filtro + acciones -->
                <DateNavigator :statuses="statuses" />

                <!-- 4. Tabla principal FIFO -->
                <AppointmentsTable />
            </div>
        </section>

        <!-- Modales Vue (fuera del content-wrapper) -->
        <ScheduleAppointmentModal :document-types="documentTypes" :sexes="sexes" />
        <RescheduleAppointmentModal />
    </div>
</template>