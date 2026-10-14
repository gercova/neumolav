import { createApp } from 'vue';
import { createPinia } from 'pinia';
import RiskForm from '@/components/risks/RiskForm.vue';

const el    = document.getElementById('vue-risk-form');
const state = window.__RISK_FORM_STATE__ || {};

if (el) {
    createApp(RiskForm, { initialState: state })
        .use(createPinia())
        .mount(el);
}