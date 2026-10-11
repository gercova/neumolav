<script setup>
import { ref, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useStatisticsStore } from '@/stores/statisticsStore';
import { useHighcharts } from '@/composables/useHighcharts';

const store = useStatisticsStore();
const { bySex, byMaritalStatus, byBloodGroup, byDegree, bySmoking } = storeToRefs(store);

// ── Helper para no repetir 5 veces la misma estructura ────
const buildPie = (title, rows, keyField) => ({
    chart: { type: 'pie' },
    title: { text: title },
    subtitle: { text: 'Data Base Neumotar' },
    tooltip: { pointFormat: '{series.name}: {point.y} <b>{point.percentage:.1f}%</b>' },
    plotOptions: {
        pie: { allowPointSelect: true, cursor: 'pointer', dataLabels: { enabled: false }, showInLegend: true },
        series: { dataLabels: { enabled: true } },
    },
    series: [{ name: 'Cantidad', colorByPoint: true, data: store.toPieData(rows, keyField) }],
});

const buildColumn = (title, rows, keyField) => ({
    chart: { type: 'column' },
    title: { text: title },
    subtitle: { text: 'Data Base Neumotar' },
    xAxis: { type: 'category', labels: { rotation: -45, style: { fontSize: '13px' } } },
    yAxis: { min: 0, title: { text: 'Datos recolectados' } },
    legend: { enabled: false },
    tooltip: { pointFormat: 'Nro de pacientes: {point.y}' },
    series: [{
        name: 'Population',
        data: store.toPieData(rows, keyField),
        dataLabels: { enabled: true, rotation: -90, color: '#FFFFFF', align: 'right', y: 10 },
    }],
});

// ── 5 contenedores ────────────────────────────────────────
const cSex      = ref(null); useHighcharts(cSex,      () => buildPie('Pacientes por sexo',       bySex.value,           'sexo'));
const cMarital  = ref(null); useHighcharts(cMarital,  () => buildPie('Estado civil',              byMaritalStatus.value, 'estado_civil'));
const cBlood    = ref(null); useHighcharts(cBlood,    () => buildPie('Grupos sanguíneos de pacientes', byBloodGroup.value, 'grupo_sanguineo'));
const cDegree   = ref(null); useHighcharts(cDegree,   () => buildPie('Nivel de instrucción',      byDegree.value,        'grado_instruccion'));
const cSmoking  = ref(null); useHighcharts(cSmoking,  () => buildColumn('Tipo de Tabaquismo',     bySmoking.value,       'tabaquismo'));
</script>

<template>
    <div class="row">
        <div class="col-4"><div class="card"><div class="card-body"><div ref="cSex"></div></div></div></div>
        <div class="col-4"><div class="card"><div class="card-body"><div ref="cMarital"></div></div></div></div>
        <div class="col-4"><div class="card"><div class="card-body"><div ref="cBlood"></div></div></div></div>
        <div class="col-4"><div class="card"><div class="card-body"><div ref="cDegree"></div></div></div></div>
        <div class="col-4"><div class="card"><div class="card-body"><div ref="cSmoking"></div></div></div></div>
    </div>
</template>