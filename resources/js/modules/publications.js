import { createApp } from 'vue';
import { createPinia } from 'pinia';
import PostsIndex from '@/components/posts/PostsIndex.vue';

const el = document.getElementById('vue-publications');
const state = window.__PUBLICATIONS_STATE__ || {};

if (el) {
    createApp(PostsIndex, {
        initialPermissions: state.permissions || {},
        initialPostTypes: state.postTypes || [],
    })
        .use(createPinia())
        .mount(el);
}
