<script setup>
import { onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { useRolesStore } from '@/stores/rolesStore';

const store = useRolesStore();
const {
    permissionsModalVisible,
    permRoleName,
    availableSearch,
    assignedSearch,
    selectedAvailable,
    selectedAssigned,
    filteredAvailable,
    filteredAssigned,
    loadingPermissions,
    syncingPermissions,
} = storeToRefs(store);

const onBackdropClick = (e) => {
    if (e.target.classList.contains('modal') && !syncingPermissions.value) {
        store.closePermissionsModal();
    }
};

const onKeydown = (e) => {
    if (e.key === 'Escape' && permissionsModalVisible.value && !syncingPermissions.value) {
        store.closePermissionsModal();
    }
};

if (typeof window !== 'undefined') {
    window.addEventListener('keydown', onKeydown);
}
onBeforeUnmount(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('keydown', onKeydown);
    }
});
</script>

<template>
    <Teleport to="body">
        <div
            v-if="permissionsModalVisible"
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background: rgba(0, 0, 0, 0.55);"
            @click="onBackdropClick"
        >
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title font-weight-bold mb-0">
                            <i class="bi bi-key-fill mr-1"></i>
                            Permisos del Rol: <span class="badge badge-light text-dark ml-1">{{ permRoleName }}</span>
                        </h5>
                        <button
                            type="button"
                            class="close text-white"
                            aria-label="Cerrar"
                            :disabled="syncingPermissions"
                            @click="store.closePermissionsModal()"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-3">
                        <div v-if="loadingPermissions" class="text-center py-5 text-muted">
                            <span class="spinner-border spinner-border-lg text-info mr-2"></span>
                            <p class="mt-2 mb-0">Cargando permisos del sistema...</p>
                        </div>

                        <div v-else class="row align-items-start">
                            <!-- Panel izquierdo: Disponibles -->
                            <div class="col-md-5">
                                <div class="card mb-0 shadow-sm border">
                                    <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="card-title font-weight-bold mb-0">
                                            Disponibles
                                            <span class="badge badge-secondary ml-1">{{ filteredAvailable.length }}</span>
                                        </h6>
                                        <small v-if="selectedAvailable.length > 0" class="text-primary font-weight-bold">
                                            {{ selectedAvailable.length }} selec.
                                        </small>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="input-group input-group-sm mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                            </div>
                                            <input
                                                v-model="availableSearch"
                                                type="text"
                                                class="form-control"
                                                placeholder="Buscar disponible..."
                                            >
                                            <div v-if="availableSearch" class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" @click="availableSearch = ''">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="permission-panel border rounded">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 10%;">#</th>
                                                        <th style="width: 70%;">Nombre</th>
                                                        <th style="width: 20%;" class="text-center">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-if="filteredAvailable.length === 0">
                                                        <td colspan="3" class="text-center text-muted py-4 small">
                                                            No hay permisos disponibles
                                                        </td>
                                                    </tr>
                                                    <tr
                                                        v-else
                                                        v-for="(perm, idx) in filteredAvailable"
                                                        :key="perm.id"
                                                        :class="{ 'row-highlight': selectedAvailable.includes(perm.id) }"
                                                        style="cursor: pointer;"
                                                        @click="store.toggleSelectAvailable(perm.id)"
                                                    >
                                                        <td class="text-muted small">{{ idx + 1 }}</td>
                                                        <td>
                                                            <code class="text-primary">{{ perm.name }}</code>
                                                            <div v-if="perm.description" class="text-muted small text-truncate" style="max-width: 200px;">
                                                                {{ perm.description }}
                                                            </div>
                                                        </td>
                                                        <td class="text-center" @click.stop>
                                                            <button
                                                                type="button"
                                                                class="btn btn-xs btn-primary"
                                                                title="Agregar permiso"
                                                                @click="store.transferPermission(perm, 'add')"
                                                            >
                                                                <i class="bi bi-plus-lg"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Columna central de transferencia -->
                            <div class="col-md-2">
                                <div class="transfer-col">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary w-100"
                                        title="Agregar todos los visibles"
                                        :disabled="filteredAvailable.length === 0"
                                        @click="store.transferAll('add')"
                                    >
                                        Todos <i class="bi bi-chevron-double-right ml-1"></i>
                                    </button>

                                    <button
                                        v-if="selectedAvailable.length > 0"
                                        type="button"
                                        class="btn btn-sm btn-primary w-100"
                                        title="Agregar permisos seleccionados"
                                        @click="store.transferSelected('add')"
                                    >
                                        Selec. <i class="bi bi-chevron-right ml-1"></i>
                                    </button>

                                    <button
                                        v-if="selectedAssigned.length > 0"
                                        type="button"
                                        class="btn btn-sm btn-warning w-100 text-dark font-weight-bold"
                                        title="Quitar permisos seleccionados"
                                        @click="store.transferSelected('remove')"
                                    >
                                        <i class="bi bi-chevron-left mr-1"></i> Selec.
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-warning w-100"
                                        title="Quitar todos los visibles"
                                        :disabled="filteredAssigned.length === 0"
                                        @click="store.transferAll('remove')"
                                    >
                                        <i class="bi bi-chevron-double-left mr-1"></i> Ninguno
                                    </button>
                                </div>
                            </div>

                            <!-- Panel derecho: Asignados -->
                            <div class="col-md-5">
                                <div class="card mb-0 shadow-sm border">
                                    <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="card-title font-weight-bold mb-0">
                                            Asignados
                                            <span class="badge badge-success ml-1">{{ filteredAssigned.length }}</span>
                                        </h6>
                                        <small v-if="selectedAssigned.length > 0" class="text-warning font-weight-bold">
                                            {{ selectedAssigned.length }} selec.
                                        </small>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="input-group input-group-sm mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                            </div>
                                            <input
                                                v-model="assignedSearch"
                                                type="text"
                                                class="form-control"
                                                placeholder="Buscar asignado..."
                                            >
                                            <div v-if="assignedSearch" class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" @click="assignedSearch = ''">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="permission-panel border rounded">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 10%;">#</th>
                                                        <th style="width: 70%;">Nombre</th>
                                                        <th style="width: 20%;" class="text-center">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-if="filteredAssigned.length === 0">
                                                        <td colspan="3" class="text-center text-muted py-4 small">
                                                            No hay permisos asignados a este rol
                                                        </td>
                                                    </tr>
                                                    <tr
                                                        v-else
                                                        v-for="(perm, idx) in filteredAssigned"
                                                        :key="perm.id"
                                                        :class="{ 'row-highlight': selectedAssigned.includes(perm.id) }"
                                                        style="cursor: pointer;"
                                                        @click="store.toggleSelectAssigned(perm.id)"
                                                    >
                                                        <td class="text-muted small">{{ idx + 1 }}</td>
                                                        <td>
                                                            <code class="text-success">{{ perm.name }}</code>
                                                            <div v-if="perm.description" class="text-muted small text-truncate" style="max-width: 200px;">
                                                                {{ perm.description }}
                                                            </div>
                                                        </td>
                                                        <td class="text-center" @click.stop>
                                                            <button
                                                                type="button"
                                                                class="btn btn-xs btn-danger"
                                                                title="Quitar permiso"
                                                                @click="store.transferPermission(perm, 'remove')"
                                                            >
                                                                <i class="bi bi-dash-lg"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light justify-content-between">
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            :disabled="syncingPermissions"
                            @click="store.closePermissionsModal()"
                        >
                            <i class="bi bi-x-circle mr-1"></i> Cerrar
                        </button>

                        <button
                            type="button"
                            class="btn btn-success font-weight-bold"
                            :disabled="syncingPermissions || loadingPermissions"
                            @click="store.saveRolePermissions()"
                        >
                            <span v-if="syncingPermissions" class="spinner-border spinner-border-sm mr-1"></span>
                            <i v-else class="bi bi-save mr-1"></i>
                            {{ syncingPermissions ? 'Guardando Permisos...' : 'Guardar Permisos' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.permission-panel {
    max-height: 380px;
    overflow-y: auto;
}

.permission-panel .table thead th {
    position: sticky;
    top: 0;
    background-color: #f8f9fa;
    z-index: 5;
}

.row-highlight {
    background-color: #e8f4fd !important;
    transition: background-color 0.2s ease;
}

.transfer-col {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 10px;
    padding-top: 40px;
}
</style>
