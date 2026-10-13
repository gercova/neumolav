import { createApp } from 'vue';
import { createPinia } from 'pinia';
import ReportsSee from '@/components/reports/ReportsSee.vue';

const el = document.getElementById('vue-reports-see');
const state = window.__REPORTS_SEE_STATE__ || {};

if (el) {
    createApp(ReportsSee, state)
        .use(createPinia())
        .mount(el);
}