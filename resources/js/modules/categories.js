import { createApp } from 'vue';
import { createPinia } from 'pinia';
import CategoriesIndex from '@/components/categories/CategoriesIndex.vue';

const el    = document.getElementById('vue-categories');
const state = window.__CATEGORIES_STATE__ || {};

if (el) {
    createApp(CategoriesIndex, {
        initialPermissions: state.permissions || {},
    })
        .use(createPinia())
        .mount(el);
}
