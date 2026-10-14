import { createApp } from 'vue';
import { createPinia } from 'pinia';
import RisksSee from '@/components/risks/RisksSee.vue';

const el    = document.getElementById('vue-risks-see');
const state = window.__RISKS_SEE_STATE__ || {};

if (el) {
    createApp(RisksSee, state)
        .use(createPinia())
        .mount(el);
}