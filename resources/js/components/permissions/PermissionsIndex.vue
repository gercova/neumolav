<script setup>
import { onMounted } from 'vue';
import { usePermissionsStore } from '@/stores/permissionsStore';
import PermissionsFilters from './PermissionsFilters.vue';
import PermissionsTable from './PermissionsTable.vue';
import PermissionModal from './PermissionModal.vue';

const props = defineProps({
    initialPermissions: {
        type: Object,
        default: () => ({}),
    },
});

const store = usePermissionsStore();

onMounted(() => {
    if (props.initialPermissions && Object.keys(props.initialPermissions).length > 0) {
        store.permissions = { ...store.permissions, ...props.initialPermissions };
    }
    store.fetchPage(1);
});
</script>

<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Permisos</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/admin/home">Home</a></li>
                            <li class="breadcrumb-item">Seguridad</li>
                            <li class="breadcrumb-item active">Permisos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <button
                                    v-if="store.permissions.create"
                                    type="button"
                                    class="btn btn-primary"
                                    @click="store.openCreateModal()"
                                >
                                    <i class="bi bi-plus-circle mr-1"></i> Agregar nuevo permiso
                                </button>
                                <span v-else class="text-muted small">
                                    <i class="bi bi-info-circle mr-1"></i> Consulta de permisos
                                </span>

                                <PermissionsFilters />
                            </div>

                            <div class="card-body p-0">
                                <PermissionsTable />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal para Agregar y Editar Permiso -->
        <PermissionModal />
    </div>
</template>

<style scoped>
.gap-2 {
    gap: 0.5rem;
}
</style>
