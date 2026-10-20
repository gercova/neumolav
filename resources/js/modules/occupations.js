import { createApp } from 'vue';
import { createPinia } from 'pinia';
import OccupationsIndex from '@/components/occupations/OccupationsIndex.vue';

const el    = document.getElementById('vue-occupations');
const state = window.__OCCUPATIONS_STATE__ || {};

if (el) {
    createApp(OccupationsIndex, {
        initialPermissions: state.permissions || {},
    })
        .use(window.__pinia || createPinia())
        .mount(el);
}
