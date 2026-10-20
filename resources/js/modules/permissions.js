import { createApp } from 'vue';
import { createPinia } from 'pinia';
import PermissionsIndex from '@/components/permissions/PermissionsIndex.vue';

const el    = document.getElementById('vue-permissions');
const state = window.__PERMISSIONS_STATE__ || {};

if (el) {
    createApp(PermissionsIndex, {
        initialPermissions: state.permissions || {},
    })
        .use(createPinia())
        .mount(el);
}
