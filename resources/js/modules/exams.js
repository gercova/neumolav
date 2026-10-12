import { createApp } from 'vue';
import { createPinia } from 'pinia';
import ExamsIndex from '@/components/exams/ExamsIndex.vue';

const el = document.getElementById('vue-exams');

if (el) {
    createApp(ExamsIndex)
        .use(createPinia())
        .mount(el);
}
