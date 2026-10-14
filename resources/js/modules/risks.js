import { createApp } from 'vue';
import { createPinia } from 'pinia';
import RisksIndex from '@/components/risks/RisksIndex.vue';

const el = document.getElementById('vue-risks');

if (el) {
    createApp(RisksIndex)
        .use(createPinia())
        .mount(el);
}
