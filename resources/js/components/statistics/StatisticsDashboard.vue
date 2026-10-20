<script setup>
import { onMounted } from 'vue';
import { useStatisticsStore } from '@/stores/statisticsStore';
import StatsCards from './StatsCards.vue';
import YearSelector from './YearSelector.vue';
import AnnualChart from './AnnualChart.vue';
import TopDiagnosticsChart from './TopDiagnosticsChart.vue';
import TopDrugsChart from './TopDrugsChart.vue';
import DemographicCharts from './DemographicCharts.vue';

const props = defineProps({
    initialYear: { type: Number, required: true },
    years:       { type: Array, default: () => [] },
});

const store = useStatisticsStore();

onMounted(() => {
    store.years = props.years;
    store.init(props.initialYear);
});
</script>

<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/admin/home">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- 1. Contadores -->
                <StatsCards />
                <!-- 2. Chart anual con selector -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                                <h3 class="card-title mb-0 d-flex align-items-center">
                                    Reporte de historias clínicas
                                    <span v-if="store.isMonthlyView" class="badge badge-info ml-2 font-weight-normal text-nowrap">
                                        {{ store.selectedCategory }} - {{ store.selectedMonthName }} {{ store.selectedYear }}
                                    </span>
                                </h3>
                                <div class="card-tools d-flex align-items-center m-0" style="flex-shrink: 0;">
                                    <button
                                        v-if="store.isMonthlyView"
                                        class="btn btn-outline-secondary btn-sm mr-2 text-nowrap"
                                        style="white-space: nowrap; height: 31px; display: inline-flex; align-items: center; padding: 0.25rem 0.85rem; font-size: 0.85rem;"
                                        title="Volver a reporte anual"
                                        @click="store.resetToAnnual"
                                    >
                                        <i class="bi bi-arrow-left mr-1"></i> Volver al reporte anual
                                    </button>
                                    <YearSelector />
                                </div>
                            </div>
                            <div class="card-body">
                                <AnnualChart />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 3. Diagnósticos y fármacos -->
                <div class="row">
                    <div class="col-6"><TopDiagnosticsChart /></div>
                    <div class="col-6"><TopDrugsChart /></div>
                </div>
                <!-- 4. Demográficos (5 charts) -->
                <DemographicCharts />
            </div>
        </section>
    </div>
</template>