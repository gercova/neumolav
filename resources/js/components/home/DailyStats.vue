<script setup>
import { storeToRefs } from 'pinia';
import { useCitasStore } from '@/stores/citasStore';

const store = useCitasStore();
const { stats } = storeToRefs(store);
</script>

<template>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="citas-stat-card stat-bg-total">
                <div><div class="stat-value">{{ stats.total }}</div><div class="stat-label">Total Citas del Día</div></div>
                <div class="stat-icon"><i class="bi bi-calendar-event"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="citas-stat-card stat-bg-espera">
                <div><div class="stat-value">{{ stats.en_espera }}</div><div class="stat-label">En Espera (Sala)</div></div>
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="citas-stat-card stat-bg-atendidos">
                <div><div class="stat-value">{{ stats.atendidos }}</div><div class="stat-label">Atendidos</div></div>
                <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="citas-stat-card stat-bg-pendientes">
                <div><div class="stat-value">{{ stats.pendientes }}</div><div class="stat-label">Pendientes</div></div>
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            </div>
        </div>
    </div>
</template>