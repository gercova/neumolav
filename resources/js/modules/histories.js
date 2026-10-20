import { createApp } from 'vue';
import { createPinia } from 'pinia';
import HistoriesIndex from '@/components/histories/HistoriesIndex.vue';

const el    = document.getElementById('vue-histories');
const state = window.__HISTORIES_STATE__ || {};

if (el) {
    createApp(HistoriesIndex, {
        addUrl: state.addUrl,   // ruta al formulario de nueva historia
        editUrl: state.editUrl,  // base para editar (se completa con ID)
    })
        .use(createPinia())
        .mount(el);
}