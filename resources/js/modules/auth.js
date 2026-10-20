import { createApp } from 'vue';
import { createPinia } from 'pinia';
import LoginIndex from '@/components/auth/LoginIndex.vue';

const el    = document.getElementById('vue-login');
const state = window.__LOGIN_STATE__ || window.__AUTH_STATE__ || {};

if (el) {
    createApp(LoginIndex, {
        initialState: state,
    })
        .use(window.__pinia || createPinia())
        .mount(el);
}
