<script setup>
import { storeToRefs } from 'pinia';
import { useUsersStore } from '@/stores/usersStore';

const store = useUsersStore();
const {
    items,
    loading,
    page,
    pageSize,
    total,
    totalPages,
    visiblePages,
    permissions,
    rangeStart,
    rangeEnd,
    currentUserId,
    routes,
} = storeToRefs(store);

const PAGE_SIZE_OPTIONS = [10, 25, 50, 100];

const confirmDelete = async (item) => {
    if (currentUserId.value === item.id) {
        window.alertNotify?.('warning', 'No puede eliminar su propia cuenta de usuario.');
        return;
    }

    const swal = window.Swal;
    if (!swal) {
        if (confirm(`¿Está seguro de eliminar al usuario "${item.name}"?`)) {
            try {
                await store.destroy(item.id);
            } catch (e) {
                // Notificación manejada en el store
            }
        }
        return;
    }

    const result = await swal.fire({
        title: '¿Eliminar usuario?',
        html: `¿Está seguro de eliminar al usuario <b>${item.name}</b>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash mr-1"></i> Sí, borrar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
    });

    if (!result.isConfirmed) return;

    try {
        await store.destroy(item.id);
    } catch (e) {
        // Notificación manejada en el store
    }
};
</script>

<template>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead class="thead-light">
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 30%;">Usuario</th>
                    <th style="width: 25%;">Email</th>
                    <th style="width: 15%;" class="text-center">Rol</th>
                    <th style="width: 12%;" class="text-center">Último Acceso</th>
                    <th style="width: 13%;" class="text-center">Opciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-if="loading">
                    <td colspan="6" class="text-center py-4 text-muted">
                        <span class="spinner-border spinner-border-sm text-primary mr-2"></span>
                        Cargando usuarios...
                    </td>
                </tr>

                <tr v-else-if="items.length === 0">
                    <td colspan="6" class="text-center py-5">
                        <i class="bi bi-people text-muted" style="font-size: 2.5rem; opacity: .5;"></i>
                        <h6 class="text-dark font-weight-bold mt-2 mb-1">Sin resultados</h6>
                        <p class="text-muted small mb-0">
                            {{ store.search || store.roleFilter ? 'No se encontraron usuarios con esos filtros.' : 'Aún no hay usuarios registrados.' }}
                        </p>
                    </td>
                </tr>

                <tr v-else v-for="(item, index) in items" :key="item.id || index">
                    <td class="text-center align-middle">
                        <span class="badge badge-light border font-weight-normal">
                            {{ (page - 1) * pageSize + index + 1 }}
                        </span>
                    </td>

                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <img
                                :src="item.avatar_url || '/images/default-avatar.png'"
                                alt="Avatar"
                                class="rounded-circle mr-2 border shadow-xs"
                                style="width: 38px; height: 38px; object-fit: cover;"
                                @error="(e) => (e.target.src = '/dist/img/user2-160x160.jpg')"
                            >
                            <div>
                                <span class="font-weight-bold text-dark d-block">
                                    {{ item.name }}
                                    <span v-if="currentUserId === item.id" class="badge badge-pill badge-primary ml-1" style="font-size: 0.65rem;">
                                        Tú
                                    </span>
                                </span>
                                <small class="text-muted">
                                    <i class="bi bi-at"></i>{{ item.username || 'sin_usuario' }}
                                </small>
                            </div>
                        </div>
                    </td>

                    <td class="align-middle text-nowrap">
                        <span class="text-secondary small">
                            <i class="bi bi-envelope mr-1 text-muted"></i>{{ item.email }}
                        </span>
                    </td>

                    <td class="text-center align-middle">
                        <span
                            class="badge px-2 py-1 font-weight-normal"
                            :class="item.role_name === 'administrador' ? 'badge-primary' : 'badge-info'"
                        >
                            <i class="bi bi-shield-check mr-1"></i>{{ item.role_name }}
                        </span>
                    </td>

                    <td class="text-center align-middle text-nowrap">
                        <span class="small" :class="item.last_login === 'Nunca' ? 'text-muted' : 'text-dark'">
                            <i class="bi bi-clock-history mr-1 text-muted"></i>{{ item.last_login }}
                        </span>
                    </td>

                    <td class="text-center align-middle">
                        <div class="btn-group btn-group-sm" role="group">
                            <!-- Asignar roles / permisos -->
                            <a
                                v-if="permissions.assign_roles"
                                :href="`${routes.role_prefix}/${item.id}`"
                                class="btn btn-outline-info"
                                title="Asignar roles y permisos"
                            >
                                <i class="bi bi-person-gear"></i>
                            </a>

                            <!-- Cambiar contraseña -->
                            <button
                                v-if="permissions.update"
                                type="button"
                                class="btn btn-outline-secondary"
                                title="Cambiar contraseña rápida"
                                @click="store.openPasswordModal(item)"
                            >
                                <i class="bi bi-key"></i>
                            </button>

                            <!-- Editar usuario -->
                            <button
                                v-if="permissions.update"
                                type="button"
                                class="btn btn-outline-warning"
                                title="Editar usuario"
                                @click="store.openEditModal(item)"
                            >
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <!-- Eliminar usuario -->
                            <button
                                v-if="permissions.delete"
                                type="button"
                                class="btn btn-outline-danger"
                                :disabled="currentUserId === item.id"
                                :title="currentUserId === item.id ? 'No puede eliminar su propia cuenta' : 'Eliminar usuario'"
                                @click="confirmDelete(item)"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Paginación y Resumen -->
    <div
        v-if="total > 0"
        class="card-footer d-flex flex-wrap justify-content-between align-items-center bg-white border-top py-2 px-3 gap-2"
    >
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">
                Mostrando <b>{{ rangeStart }}</b> a <b>{{ rangeEnd }}</b> de <b>{{ total }}</b> usuarios
            </span>
            <div class="d-flex align-items-center ml-2">
                <label class="text-muted small mb-0 mr-1" for="users_page_size">Por pág:</label>
                <select
                    id="users_page_size"
                    class="form-control form-control-sm"
                    style="width: auto;"
                    :value="pageSize"
                    @change="store.setPageSize($event.target.value)"
                >
                    <option v-for="opt in PAGE_SIZE_OPTIONS" :key="opt" :value="opt">
                        {{ opt }}
                    </option>
                </select>
            </div>
        </div>

        <nav v-if="totalPages > 1" aria-label="Navegación de páginas">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item" :class="{ disabled: page === 1 }">
                    <button class="page-link" :disabled="page === 1" @click="store.firstPage()" title="Primera página">
                        <i class="bi bi-chevron-double-left"></i>
                    </button>
                </li>
                <li class="page-item" :class="{ disabled: page === 1 }">
                    <button class="page-link" :disabled="page === 1" @click="store.prevPage()" title="Página anterior">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </li>

                <li
                    v-for="(p, idx) in visiblePages"
                    :key="idx"
                    class="page-item"
                    :class="{ active: p === page, disabled: p === '…' }"
                >
                    <span v-if="p === '…'" class="page-link">…</span>
                    <button v-else class="page-link" @click="store.goToPage(p)">
                        {{ p }}
                    </button>
                </li>

                <li class="page-item" :class="{ disabled: page === totalPages }">
                    <button class="page-link" :disabled="page === totalPages" @click="store.nextPage()" title="Página siguiente">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </li>
                <li class="page-item" :class="{ disabled: page === totalPages }">
                    <button class="page-link" :disabled="page === totalPages" @click="store.lastPage()" title="Última página">
                        <i class="bi bi-chevron-double-right"></i>
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</template>

<style scoped>
.gap-2 {
    gap: 0.5rem;
}
.shadow-xs {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}
</style>
