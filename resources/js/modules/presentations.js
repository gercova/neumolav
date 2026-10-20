import { createApp } from 'vue';
import { createPinia } from 'pinia';
import PresentationsIndex from '@/components/presentations/PresentationsIndex.vue';

const el    = document.getElementById('vue-presentations');
const state = window.__PRESENTATIONS_STATE__ || {};

if (el) {
    createApp(PresentationsIndex, {
        initialPermissions: state.permissions || {},
    })
        .use(createPinia())
        .mount(el);
}
