import { createApp } from 'vue';
import { createPinia } from 'pinia';
import StatisticsDashboard from '@/components/statistics/StatisticsDashboard.vue';

const el    = document.getElementById('vue-statistics');
const state = window.__STATISTICS_STATE__ || {};

if (el) {
    createApp(StatisticsDashboard, {
        initialYear: state.initialYear || new Date().getFullYear(),
        years: state.years || [],
    })
        .use(createPinia())
        .mount(el);
}