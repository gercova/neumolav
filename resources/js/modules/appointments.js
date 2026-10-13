import { createApp } from 'vue';
import { createPinia } from 'pinia';
import AppointmentsIndex from '@/components/appointments/AppointmentsIndex.vue';

const el = document.getElementById('vue-appointments');

if (el) {
    createApp(AppointmentsIndex)
        .use(createPinia())
        .mount(el);
}
