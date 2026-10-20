<script setup>
import { ref, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useStatisticsStore } from '@/stores/statisticsStore';
import { useHighcharts } from '@/composables/useHighcharts';

const store = useStatisticsStore();
const { annualData, selectedYear, isMonthlyView, selectedMonth, selectedCategory, monthlyData } = storeToRefs(store);
const container = ref(null);

const monthNames = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
];

const categoriesList = ['Historias', 'Exámenes', 'Citas'];

// ── Opciones para el reporte anual (12 meses, 3 series) ────────
const annualOptions = computed(() => ({
    chart: { type: 'column' },
    title: { text: `Producción de Historias, Exámenes y Citas en ${selectedYear.value}` },
    subtitle: {
        text: `Fuente: Sistema de Salud ${window.NAME_ENTERPRISE || 'NeumoTar'} — Haga clic en una barra para ver el detalle mensual`,
    },
    xAxis: {
        categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        crosshair: true,
        accessibility: { description: 'Meses' },
    },
    yAxis: {
        min: 0,
        title: { text: 'Cantidad' },
        allowDecimals: false,
    },
    tooltip: {
        useHTML: true,
        formatter: function () {
            return `<b>${this.series.name}</b><br/>` +
                   `Mes: <b>${this.x}</b><br/>` +
                   `Cantidad: <b>${this.y}</b> (Pacientes)<br/>` +
                   `<span style="color:#007bff; font-size:11px;">🔍 Haga clic para ver el detalle diario de ${this.series.name}</span>`;
        },
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0,
            cursor: 'pointer',
            point: {
                events: {
                    click: function (e) {
                        const point = this || e?.point;
                        if (!point) return;
                        const monthIndex = point.x !== undefined ? point.x : point.index;
                        const monthNumber = (monthIndex !== undefined ? monthIndex : 0) + 1;
                        const categoryName = point.series?.name || 'Historias';
                        const color = point.color || point.series?.color;
                        store.drillDown(monthNumber, categoryName, color);
                    },
                },
            },
        },
    },
    series: annualData.value || [],
}));

// ── Opciones para el reporte mensual filtrado por categoría y mes ──
const monthlyOptions = computed(() => {
    const detail = monthlyData.value;
    const cat = selectedCategory.value || 'Historias';
    const seriesItem = detail?.series?.[cat] || { name: cat, data: [] };
    const monthName = store.selectedMonthName;
    const total = (seriesItem.data || []).reduce((a, b) => a + b, 0);

    return {
        chart: { type: 'column' },
        title: {
            text: `Producción de ${cat} — ${monthName} ${selectedYear.value}`,
        },
        subtitle: {
            text: `Total del mes: <b>${total}</b> registros | Haga clic en "Volver al reporte anual" para regresar`,
        },
        xAxis: {
            categories: detail?.categories || [],
            crosshair: true,
            title: { text: `Días de ${monthName}` },
            accessibility: { description: 'Días del mes' },
        },
        yAxis: {
            min: 0,
            title: { text: `Cantidad de ${cat}` },
            allowDecimals: false,
        },
        tooltip: {
            useHTML: true,
            formatter: function () {
                return `<b>${cat}</b><br/>` +
                       `Día ${this.x} de ${monthName} ${selectedYear.value}<br/>` +
                       `Cantidad: <b>${this.y}</b> registros`;
            },
        },
        plotOptions: {
            column: {
                pointPadding: 0.1,
                borderWidth: 0,
                color: store.selectedCategoryColor || undefined,
                dataLabels: {
                    enabled: true,
                    formatter: function () {
                        return this.y > 0 ? this.y : '';
                    },
                    style: {
                        fontSize: '10px',
                        fontWeight: 'normal',
                    },
                },
            },
        },
        series: [
            {
                name: cat,
                data: seriesItem.data || [],
                color: store.selectedCategoryColor || undefined,
            },
        ],
    };
});

// Getter dinámico según la vista actual
const options = computed(() => (isMonthlyView.value ? monthlyOptions.value : annualOptions.value));

useHighcharts(container, () => options.value);
</script>

<template>
    <div class="col-md-12">
        <!-- Barra de navegación y filtros cuando está en vista mensual -->
        <div v-if="isMonthlyView" class="d-flex flex-wrap justify-content-between align-items-center mb-3 p-2 bg-light rounded border">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <button class="btn btn-outline-secondary btn-sm mr-3" @click="store.resetToAnnual">
                    <i class="bi bi-arrow-left"></i> Volver al reporte anual
                </button>
                <span class="font-weight-bold text-dark">
                    <i class="bi bi-calendar-event text-primary mr-1"></i>
                    {{ store.selectedMonthName }} {{ selectedYear }}
                </span>
            </div>

            <!-- Selector de Categoría (Historias, Exámenes, Citas) y Mes -->
            <div class="d-flex flex-wrap align-items-center">
                <span class="text-muted small mr-2">Categoría:</span>
                <div class="btn-group btn-group-sm mr-3">
                    <button
                        v-for="cat in categoriesList"
                        :key="cat"
                        class="btn"
                        :class="selectedCategory === cat ? 'btn-primary' : 'btn-outline-primary'"
                        @click="store.setCategory(cat)"
                    >
                        {{ cat }}
                    </button>
                </div>

                <span class="text-muted small mr-2">Mes:</span>
                <select
                    class="custom-select custom-select-sm form-control form-control-sm"
                    style="width: 130px;"
                    :value="selectedMonth"
                    @change="e => store.setMonth(Number(e.target.value))"
                >
                    <option v-for="(mName, mIdx) in monthNames" :key="mIdx + 1" :value="mIdx + 1">
                        {{ mName }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Indicador de carga para el detalle mensual -->
        <div v-if="store.loading.monthly" class="text-center py-5 text-muted">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 mb-0">Cargando reporte de {{ selectedCategory }} para {{ store.selectedMonthName }}...</p>
        </div>

        <!-- Contenedor del gráfico Highcharts -->
        <div v-show="!store.loading.monthly" ref="container" style="margin: 0 auto; width: 100%; min-height: 400px;"></div>
    </div>
</template>