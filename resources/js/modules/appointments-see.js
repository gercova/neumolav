import { createApp } from 'vue';
import { createPinia } from 'pinia';
import AppointmentsSee from '@/components/appointments/AppointmentsSee.vue';

const el    = document.getElementById('vue-appointments-see');
const state = window.__APPOINTMENTS_SEE_STATE__ || {};

if (el) {
    createApp(AppointmentsSee, state)
        .use(createPinia())
        .mount(el);
}