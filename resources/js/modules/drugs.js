import { createApp } from 'vue';
import { createPinia } from 'pinia';
import DrugsIndex from '@/components/drugs/DrugsIndex.vue';

const el    = document.getElementById('vue-drugs');
const state = window.__DRUGS_STATE__ || {};

if (el) {
    createApp(DrugsIndex, {
        initialPermissions: state.permissions || {},
        initialCategories: state.categories || [],
        initialPresentations: state.presentations || [],
    })
        .use(createPinia())
        .mount(el);
}
