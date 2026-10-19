import { createApp } from 'vue';
import { createPinia } from 'pinia';
import SpecialtiesIndex from '@/components/specialties/SpecialtiesIndex.vue';

const el = document.getElementById('vue-specialties');
const state = window.__SPECIALTIES_STATE__ || {};

if (el) {
    createApp(SpecialtiesIndex, {
        initialPermissions: state.permissions || {},
        initialOccupations: state.occupations || [],
    })
        .use(createPinia())
        .mount(el);
}
