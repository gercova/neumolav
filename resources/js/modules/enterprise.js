import { createApp } from 'vue';
import { createPinia } from 'pinia';
import EnterpriseIndex from '@/components/enterprise/EnterpriseIndex.vue';

const el    = document.getElementById('vue-enterprise');
const state = window.__ENTERPRISE_STATE__ || {};

if (el) {
    createApp(EnterpriseIndex, {
        initialState: state,
    })
        .use(createPinia())
        .mount(el);
}
