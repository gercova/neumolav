<script setup>
import { onMounted } from 'vue';
import { useCitasStore } from '@/stores/citasStore';
import GlobalStats from './GlobalStats.vue';
import DailyStats from './DailyStats.vue';
import DateNavigator from './DateNavigator.vue';
import AppointmentsTable from './AppointmentsTable.vue';

const props = defineProps({
    initialDate:  { type: String, required: true },
    globalStats:  { type: Object, required: true },
    statuses:     { type: Array, default: () => [] },
});

const citasStore = useCitasStore();

onMounted(() => {
    citasStore.currentDate = props.initialDate;
    citasStore.refresh();
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
    </div>
</template>