import { createApp } from 'vue';
import { createPinia } from 'pinia';
import ExamsSee from '@/components/exams/ExamsSee.vue';

const el    = document.getElementById('vue-exams-see');
const state = window.__EXAMS_SEE_STATE__ || {};

if (el) {
    createApp(ExamsSee, state)
        .use(createPinia())
        .mount(el);
}