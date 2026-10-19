<script setup>
import { onMounted } from 'vue';
import { useRolesStore } from '@/stores/rolesStore';
import RolesFilters from './RolesFilters.vue';
import RolesTable from './RolesTable.vue';
import RoleModal from './RoleModal.vue';
import RolePermissionsModal from './RolePermissionsModal.vue';
import RoleUsersModal from './RoleUsersModal.vue';

const props = defineProps({
    initialPermissions: {
        type: Object,
        default: () => ({}),
    },
});

const store = useRolesStore();

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
                        <h1 class="m-0">Roles</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/admin/home">Home</a></li>
                            <li class="breadcrumb-item">Seguridad</li>
                            <li class="breadcrumb-item active">Roles</li>
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
                                    <i class="bi bi-plus-circle mr-1"></i> Agregar Rol
                                </button>
                                <span v-else class="text-muted small">
                                    <i class="bi bi-info-circle mr-1"></i> Consulta de roles
                                </span>

                                <RolesFilters />
                            </div>

                            <div class="card-body p-0">
                                <RolesTable />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modales de Rol, Asignación de Permisos y Vista de Usuarios -->
        <RoleModal />
        <RolePermissionsModal />
        <RoleUsersModal />
    </div>
</template>

<style scoped>
.gap-2 {
    gap: 0.5rem;
}
</style>
