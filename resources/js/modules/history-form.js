import { createApp } from 'vue';
import { createPinia } from 'pinia';
import HistoryForm from '@/components/histories/HistoryForm.vue';

const el = document.getElementById('vue-history-form');
const state = window.__HISTORY_FORM_STATE__ || {};

if (el) {
    createApp(HistoryForm, {
        initialState: state,
    })
        .use(createPinia())
        .mount(el);
}