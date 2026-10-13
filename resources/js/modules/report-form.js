import { createApp } from 'vue';
import { createPinia } from 'pinia';
import ReportForm from '@/components/reports/ReportForm.vue';

const el = document.getElementById('vue-report-form');
const state = window.__REPORT_FORM_STATE__ || {};

if (el) {
    createApp(ReportForm, { initialState: state })
        .use(createPinia())
        .mount(el);
}