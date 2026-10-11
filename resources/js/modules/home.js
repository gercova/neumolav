import { createApp } from 'vue';
import { createPinia } from 'pinia';
import HomeDashboard from '@/components/home/HomeDashboard.vue';

const el = document.getElementById('vue-home');
const state = window.__HOME_STATE__ || {};

if (el) {
    createApp(HomeDashboard, {
        initialDate: state.initialDate,
        globalStats: state.globalStats || { historias: 0, examenes: 0, controles: 0, users: 0 },
        statuses: state.statuses || [],
    })
    .use(createPinia())
    .mount(el);
}