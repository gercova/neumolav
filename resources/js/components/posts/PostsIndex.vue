<script setup>
import { onMounted } from 'vue';
import { usePostsStore } from '@/stores/postsStore';
import PostsFilters from './PostsFilters.vue';
import PostsTable from './PostsTable.vue';
import PostPanel from './PostPanel.vue';

const props = defineProps({
    initialPermissions: {
        type: Object,
        default: () => ({}),
    },
    initialPostTypes: {
        type: Array,
        default: () => [],
    },
});

const store = usePostsStore();

onMounted(() => {
    if (props.initialPermissions && Object.keys(props.initialPermissions).length > 0) {
        store.permissions = { ...store.permissions, ...props.initialPermissions };
    }
    if (props.initialPostTypes && props.initialPostTypes.length > 0) {
        store.postTypes = props.initialPostTypes;
    }
    store.fetchPage(1);
});
</script>

<template>
    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Publicaciones</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/admin/home">Home</a></li>
                            <li class="breadcrumb-item">Negocio</li>
                            <li class="breadcrumb-item active">Publicaciones</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <button
                                    v-if="store.permissions.create"
                                    type="button"
                                    class="btn btn-primary"
                                    @click="store.openCreatePanel()"
                                >
                                    <i class="bi bi-plus-circle mr-1"></i> Nueva publicación
                                </button>
                                <span v-else class="text-muted small">
                                    <i class="bi bi-info-circle mr-1"></i> Consulta de publicaciones
                                </span>

                                <PostsFilters />
                            </div>

                            <div class="card-body p-0">
                                <PostsTable />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Slide-in panel -->
        <PostPanel />
    </div>
</template>
