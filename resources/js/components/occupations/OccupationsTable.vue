<script setup>
import { storeToRefs } from 'pinia';
import { useOccupationsStore } from '@/stores/occupationsStore';

const store = useOccupationsStore();
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
} = storeToRefs(store);

const PAGE_SIZE_OPTIONS = [10, 25, 50, 100];

const confirmDelete = async (item) => {
    const swal = window.Swal;
    if (!swal) {
        if (confirm(`¿Está seguro de eliminar la ocupación "${item.descripcion}"?`)) {
            try {
                await store.destroy(item.id);
                window.alertNotify?.('success', 'La ocupación fue eliminada');
            } catch (e) {
                alert('No se pudo eliminar la ocupación.');
            }
        }
        return;
    }

    const result = await swal.fire({
        title: '¿Eliminar ocupación?',
        html: `¿Está seguro de eliminar la ocupación <b>${item.descripcion}</b>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
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
        window.alertNotify?.('success', 'La ocupación fue eliminada');
    } catch (e) {
        swal.fire('Error', 'No se pudo eliminar la ocupación.', 'error');
    }
};
</script>

<template>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead class="thead-light">
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 45%;">Descripción</th>
                    <th style="width: 15%;" class="text-center">Estado</th>
                    <th style="width: 20%;">Fecha Creación</th>
                    <th style="width: 15%;" class="text-center">Opciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-if="loading">
                    <td colspan="5" class="text-center py-4 text-muted">
                        <span class="spinner-border spinner-border-sm text-primary mr-2"></span>
                        Cargando ocupaciones...
                    </td>
                </tr>

                <tr v-else-if="items.length === 0">
                    <td colspan="5" class="text-center py-5">
                        <i class="bi bi-folder-x text-muted" style="font-size: 2.5rem; opacity: .5;"></i>
                        <h6 class="text-dark font-weight-bold mt-2 mb-1">Sin resultados</h6>
                        <p class="text-muted small mb-0">
                            {{ store.search ? 'No se encontraron ocupaciones con esa búsqueda.' : 'Aún no hay ocupaciones registradas.' }}
                        </p>
                    </td>
                </tr>

                <tr v-else v-for="(item, index) in items" :key="item.id || index">
                    <td class="text-center">
                        <span class="badge badge-light border font-weight-normal">
                            {{ (page - 1) * pageSize + index + 1 }}
                        </span>
                    </td>
                    <td>
                        <span class="font-weight-bold text-dark">{{ item.descripcion }}</span>
                    </td>
                    <td class="text-center">
                        <span
                            class="badge px-2 py-1 font-weight-normal"
                            :class="item.estado ? 'badge-success' : 'badge-secondary'"
                        >
                            <i class="bi mr-1" :class="item.estado ? 'bi-check-circle' : 'bi-x-circle'"></i>
                            {{ item.estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">
                            <i class="bi bi-calendar3 mr-1"></i>{{ item.created_at || '—' }}
                        </small>
                    </td>
                    <td class="text-center text-nowrap">
                        <button
                            v-if="permissions.update"
                            type="button"
                            class="btn btn-sm btn-warning mr-1"
                            title="Editar ocupación"
                            @click="store.openEditModal(item)"
                        >
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <button
                            v-if="permissions.delete"
                            type="button"
                            class="btn btn-sm btn-danger"
                            title="Eliminar ocupación"
                            @click="confirmDelete(item)"
                        >
                            <i class="bi bi-trash"></i>
                        </button>

                        <span v-if="!permissions.update && !permissions.delete" class="text-muted small">
                            Sin acciones
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Paginador y contador -->
    <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-top gap-2 bg-light">
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">
                Mostrando <b>{{ rangeStart }}</b> a <b>{{ rangeEnd }}</b> de <b>{{ total }}</b> registros
            </span>

            <div class="d-inline-flex align-items-center ml-2">
                <label for="occ_page_size" class="text-muted small mr-2 mb-0">Por página:</label>
                <select
                    id="occ_page_size"
                    class="form-control form-control-sm"
                    style="width: 75px;"
                    :value="pageSize"
                    @change="store.setPageSize($event.target.value)"
                >
                    <option v-for="size in PAGE_SIZE_OPTIONS" :key="size" :value="size">
                        {{ size }}
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
                    <button class="page-link" :disabled="page === 1" @click="store.prevPage()" title="Anterior">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </li>

                <li
                    v-for="(p, idx) in visiblePages"
                    :key="idx"
                    class="page-item"
                    :class="{ active: p === page, disabled: p === '…' }"
                >
                    <button v-if="p !== '…'" class="page-link" @click="store.goToPage(p)">
                        {{ p }}
                    </button>
                    <span v-else class="page-link">…</span>
                </li>

                <li class="page-item" :class="{ disabled: page === totalPages }">
                    <button class="page-link" :disabled="page === totalPages" @click="store.nextPage()" title="Siguiente">
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
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 1rem; }
</style>
