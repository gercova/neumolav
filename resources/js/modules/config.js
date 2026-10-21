import { createApp } from 'vue';
import { createPinia } from 'pinia';
import ConfigIndex from '@/components/config/ConfigIndex.vue';

const el = document.getElementById('vue-config');
const state = window.__CONFIG_STATE__ || {};

if (el) {
    createApp(ConfigIndex, {
        initialPermissions: state.permissions || {},
        initialCurrentVersion: state.current_version || '3.3.0',
        initialTotalVersions: state.total_versions || 0,
        initialLatestRelease: state.latest_release || null,
        initialRoutes: state.routes || {},
    })
        .use(createPinia())
        .mount(el);
}
