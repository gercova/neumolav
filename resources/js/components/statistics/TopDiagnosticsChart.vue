<script setup>
import { ref, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useStatisticsStore } from '@/stores/statisticsStore';
import { useHighcharts } from '@/composables/useHighcharts';

const store             = useStatisticsStore();
const { diagnostics }   = storeToRefs(store);
const container         = ref(null);

const options           = computed(() => ({
    chart:      { type: 'pie' },
    title:      { text: 'Diagnósticos' },
    subtitle:   { text: 'Diagnósticos de pacientes' },
    tooltip:    {
        pointFormat: '{series.name}: <b>{point.y}</b><br>Porcentaje: <b>{point.percentage:.1f}%</b>',
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: { enabled: false },
            showInLegend: true,
        },
        series: { dataLabels: { enabled: true } },
    },
    series: [{
        name: 'Nro. de diagnósticos',
        colorByPoint: true,
        data: store.toPieData(diagnostics.value, 'diagnostico'),
    }],
}));

useHighcharts(container, () => options.value);
</script>

<template>
    <div class="card">
        <div class="card-header"><h3 class="card-title">Reporte de Diagnósticos</h3></div>
        <div class="card-body">
            <div ref="container" style="margin: 0 auto;"></div>
        </div>
    </div>
</template>