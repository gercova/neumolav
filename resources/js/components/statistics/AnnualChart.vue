<script setup>
import { ref, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useStatisticsStore } from '@/stores/statisticsStore';
import { useHighcharts } from '@/composables/useHighcharts';

const store = useStatisticsStore();
const { annualData, selectedYear } = storeToRefs(store);
const container = ref(null);

const options = computed(() => ({
    chart: { type: 'column' },
    title: { text: `Producción de Historias, Exámenes y Citas en ${selectedYear.value}` },
    subtitle: { text: `Fuente: Sistema de Salud ${window.NAME_ENTERPRISE}` },
    xAxis: {
        categories: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
        crosshair: true,
        accessibility: { description: 'Meses' },
    },
    yAxis: { min: 0, title: { text: 'Cantidad' } },
    tooltip: { valueSuffix: ' (Pacientes)' },
    plotOptions: { column: { pointPadding: 0.2, borderWidth: 0 } },
    series: annualData.value || [],
}));

useHighcharts(container, () => options.value);
</script>

<template>
    <div class="col-md-12">
        <div ref="container" style="margin: 0 auto;"></div>
    </div>
</template>