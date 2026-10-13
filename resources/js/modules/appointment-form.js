import { createApp } from 'vue';
import { createPinia } from 'pinia';
import AppointmentForm from '@/components/appointments/AppointmentForm.vue';

const el    = document.getElementById('vue-appointment-form');
const state = window.__APPOINTMENT_FORM_STATE__ || {};

if (el) {
    createApp(AppointmentForm, { initialState: state })
        .use(createPinia())
        .mount(el);
}