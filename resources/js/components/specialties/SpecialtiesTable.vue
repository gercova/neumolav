<script setup>
import { storeToRefs } from 'pinia';
import { useSpecialtiesStore } from '@/stores/specialtiesStore';

const store = useSpecialtiesStore();
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
        if (confirm(`¿Está seguro de eliminar la especialidad "${item.descripcion}"?`)) {
            try {
                await store.destroy(item.id);
            } catch (e) {
                // Notificación en el store
            }
        }
        return;
    }

    const result = await swal.fire({
        title: '¿Eliminar especialidad?',
        html: `¿Está seguro de eliminar la especialidad <b>${item.descripcion}</b>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
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
        // Notificación en el store
    }
};
</script>

<template>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead class="thead-light">
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 20%;">Cargo / Ocupación</th>
                    <th style="width: 25%;">Descripción</th>
                    <th style="width: 25%;">Detalle</th>
                    <th style="width: 13%;" class="text-center">Fecha</th>
                    <th style="width: 12%;" class="text-center">Opciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-if="loading">
                    <td colspan="6" class="text-center py-4 text-muted">
                        <span class="spinner-border spinner-border-sm text-primary mr-2"></span>
                        Cargando especialidades...
                    </td>
                </tr>

                <tr v-else-if="items.length === 0">
                    <td colspan="6" class="text-center py-5">
                        <i class="bi bi-award text-muted" style="font-size: 2.5rem; opacity: .5;"></i>
                        <h6 class="text-dark font-weight-bold mt-2 mb-1">Sin resultados</h6>
                        <p class="text-muted small mb-0">
                            {{ store.search || store.occupationFilter ? 'No se encontraron especialidades con esos filtros.' : 'Aún no hay especialidades registradas.' }}
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
                        <span class="badge badge-info px-2 py-1 font-weight-normal">
                            <i class="bi bi-briefcase mr-1"></i>{{ item.occupation_name || 'Sin cargo' }}
                        </span>
                    </td>

                    <td class="align-middle">
                        <span class="font-weight-bold text-dark d-block">
                            {{ item.descripcion }}
                        </span>
                    </td>

                    <td class="align-middle">
                        <span class="text-secondary small">
                            {{ item.detalle || '—' }}
                        </span>
                    </td>

                    <td class="text-center align-middle text-nowrap">
                        <span class="small text-muted">
                            <i class="bi bi-calendar3 mr-1"></i>{{ item.created_at ? item.created_at.substring(0, 10) : '—' }}
                        </span>
                    </td>

                    <td class="text-center align-middle">
                        <div class="btn-group btn-group-sm" role="group">
                            <!-- Editar especialidad -->
                            <button
                                v-if="permissions.update"
                                type="button"
                                class="btn btn-outline-warning"
                                title="Editar especialidad"
                                @click="store.openEditModal(item)"
                            >
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <!-- Eliminar especialidad -->
                            <button
                                v-if="permissions.delete"
                                type="button"
                                class="btn btn-outline-danger"
                                title="Eliminar especialidad"
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
                Mostrando <b>{{ rangeStart }}</b> a <b>{{ rangeEnd }}</b> de <b>{{ total }}</b> especialidades
            </span>
            <div class="d-flex align-items-center ml-2">
                <label class="text-muted small mb-0 mr-1" for="specialties_page_size">Por pág:</label>
                <select
                    id="specialties_page_size"
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
</style>
