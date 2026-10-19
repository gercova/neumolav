import { createApp } from 'vue';
import { createPinia } from 'pinia';
import RolesIndex from '@/components/roles/RolesIndex.vue';

const el = document.getElementById('vue-roles');
const state = window.__ROLES_STATE__ || {};

if (el) {
    createApp(RolesIndex, {
        initialPermissions: state.permissions || {},
    })
        .use(createPinia())
        .mount(el);
}
