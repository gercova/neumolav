import { createApp } from 'vue';
import { createPinia } from 'pinia';
import ReportsIndex from '@/components/reports/ReportsIndex.vue';

const el = document.getElementById('vue-reports');

if (el) {
    createApp(ReportsIndex)
        .use(createPinia())
        .mount(el);
}
