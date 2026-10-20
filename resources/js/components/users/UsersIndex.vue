<script setup>
import { onMounted } from 'vue';
import { useUsersStore } from '@/stores/usersStore';
import UsersFilters from './UsersFilters.vue';
import UsersTable from './UsersTable.vue';
import UserModal from './UserModal.vue';
import UserPasswordModal from './UserPasswordModal.vue';

const props = defineProps({
    initialPermissions: {
        type: Object,
        default: () => ({}),
    },
    initialRoles: {
        type: Array,
        default: () => [],
    },
    initialSpecialties: {
        type: Array,
        default: () => [],
    },
    initialCurrentUserId: {
        type: [Number, String],
        default: null,
    },
    initialRoutes: {
        type: Object,
        default: () => ({}),
    },
});

const store = useUsersStore();

onMounted(() => {
    if (props.initialPermissions && Object.keys(props.initialPermissions).length > 0) {
        store.permissions = { ...store.permissions, ...props.initialPermissions };
    }
    if (props.initialRoles && props.initialRoles.length > 0) {
        store.roles = props.initialRoles;
    }
    if (props.initialSpecialties && props.initialSpecialties.length > 0) {
        store.specialties = props.initialSpecialties;
    }
    if (props.initialCurrentUserId) {
        store.currentUserId = Number(props.initialCurrentUserId);
    }
    if (props.initialRoutes && Object.keys(props.initialRoutes).length > 0) {
        store.routes = { ...store.routes, ...props.initialRoutes };
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
                        <h1 class="m-0">Usuarios</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/admin/home">Home</a></li>
                            <li class="breadcrumb-item">Seguridad</li>
                            <li class="breadcrumb-item active">Usuarios</li>
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
                                    <i class="bi bi-person-plus mr-1"></i> Agregar Usuario
                                </button>
                                <span v-else class="text-muted small">
                                    <i class="bi bi-info-circle mr-1"></i> Consulta de usuarios
                                </span>
                                <UsersFilters />
                            </div>

                            <div class="card-body p-0">
                                <UsersTable />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Modales de Crear/Editar Usuario y Cambio Rápido de Contraseña -->
        <UserModal />
        <UserPasswordModal />
    </div>
</template>

<style scoped>
.gap-2 {
    gap: 0.5rem;
}
</style>
