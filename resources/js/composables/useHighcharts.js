import { onMounted, onBeforeUnmount, ref, watch } from 'vue';

/**
 * Composable para usar Highcharts desde Vue.
 * - Recibe un ref al contenedor DOM.
 * - Recibe un getter reactivo que retorna el objeto de opciones completo.
 * - Crea el chart al montar, lo destruye al desmontar.
 * - Re-renderiza automáticamente cuando el getter cambia.
 */
export function useHighcharts(containerRef, optionsGetter) {
    const chart = ref(null);

    const render = () => {
        if (!containerRef.value || !window.Highcharts) return;
        if (chart.value) chart.value.destroy();
        const options = typeof optionsGetter === 'function' ? optionsGetter() : optionsGetter;
        if (!options) return;
        chart.value = window.Highcharts.chart(containerRef.value, options);
    };

    onMounted(render);
    onBeforeUnmount(() => chart.value?.destroy());

    watch(optionsGetter, render, { deep: true });

    return { chart, render };
}