<script setup>
import { storeToRefs } from 'pinia';
import { useStatisticsStore } from '@/stores/statisticsStore';

const store = useStatisticsStore();
const { counts, loading } = storeToRefs(store);
</script>

<template>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="bi bi-person-lines-fill"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Historias</span>
                    <span class="info-box-number">
                        <template v-if="loading.counts"><i class="fas fa-spinner fa-spin"></i></template>
                        <template v-else>{{ counts.hc }} <small>historias</small></template>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="bi bi-file-medical-fill"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Exámenes</span>
                    <span class="info-box-number">
                        <template v-if="loading.counts"><i class="fas fa-spinner fa-spin"></i></template>
                        <template v-else>{{ counts.ex }} <small>exámenes</small></template>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="bi bi-file-earmark-medical-fill"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Controles y Citas</span>
                    <span class="info-box-number">
                        <template v-if="loading.counts"><i class="fas fa-spinner fa-spin"></i></template>
                        <template v-else>{{ counts.ap }} <small>controles</small></template>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Historias diarias</span>
                    <span class="info-box-number">
                        <template v-if="loading.counts"><i class="fas fa-spinner fa-spin"></i></template>
                        <template v-else>{{ counts.cd }} <small>pacientes</small></template>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>