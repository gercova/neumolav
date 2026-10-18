import { createApp } from 'vue';
import { createPinia } from 'pinia';
import DiagnosticsIndex from '@/components/diagnostics/DiagnosticsIndex.vue';

const el = document.getElementById('vue-diagnostics');
const state = window.__DIAGNOSTICS_STATE__ || {};

if (el) {
    createApp(DiagnosticsIndex, {
        initialPermissions: state.permissions || {},
    })
        .use(window.__pinia || createPinia())
        .mount(el);
}
