import { createApp } from 'vue';
import { createPinia } from 'pinia';
import ExamForm from '@/components/exams/ExamForm.vue';

const el = document.getElementById('vue-exam-form');
const state = window.__EXAM_FORM_STATE__ || {};

if (el) {
    createApp(ExamForm, { initialState: state })
        .use(createPinia())
        .mount(el);
}