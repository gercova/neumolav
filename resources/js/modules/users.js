import { createApp } from 'vue';
import { createPinia } from 'pinia';
import UsersIndex from '@/components/users/UsersIndex.vue';

const el = document.getElementById('vue-users');
const state = window.__USERS_STATE__ || {};

if (el) {
    createApp(UsersIndex, {
        initialPermissions: state.permissions || {},
        initialRoles: state.roles || [],
        initialSpecialties: state.specialties || [],
        initialCurrentUserId: state.current_user_id || null,
        initialRoutes: state.routes || {},
    })
        .use(createPinia())
        .mount(el);
}
