<script setup>
import { storeToRefs } from 'pinia';
import { useCategoriesStore } from '@/stores/categoriesStore';

const store = useCategoriesStore();
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
        if (confirm(`¿Está seguro de eliminar la categoría "${item.descripcion}"?`)) {
            try {
                await store.destroy(item.id);
                window.alertNotify?.('success', 'La categoría fue eliminada');
            } catch (e) {
                alert('No se pudo eliminar la categoría.');
            }
        }
        return;
    }

    const result = await swal.fire({
        title: '¿Eliminar categoría?',
        html: `¿Está seguro de eliminar la categoría <b>${item.descripcion}</b>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
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
        window.alertNotify?.('success', 'La categoría fue eliminada');
    } catch (e) {
        swal.fire('Error', 'No se pudo eliminar la categoría.', 'error');
    }
};
</script>

<template>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead class="thead-light">
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 25%;">Descripción</th>
                    <th style="width: 40%;">Detalle</th>
                    <th style="width: 15%;">Fecha de Registro</th>
                    <th style="width: 15%;" class="text-center">Opciones</th>
                </tr>
            </thead>

            <tbody>
                <tr v-if="loading">
                    <td colspan="5" class="text-center py-4 text-muted">
                        <span class="spinner-border spinner-border-sm text-primary mr-2"></span>
                        Cargando categorías...
                    </td>
                </tr>

                <tr v-else-if="items.length === 0">
                    <td colspan="5" class="text-center py-5">
                        <i class="bi bi-folder-x text-muted" style="font-size: 2.5rem; opacity: .5;"></i>
                        <h6 class="text-dark font-weight-bold mt-2 mb-1">Sin resultados</h6>
                        <p class="text-muted small mb-0">
                            {{ store.search ? 'No se encontraron categorías con esa búsqueda.' : 'Aún no hay categorías registradas.' }}
                        </p>
                    </td>
                </tr>

                <tr v-else v-for="(cat, index) in items" :key="cat.id || index">
                    <td class="text-center">
                        <span class="badge badge-light border font-weight-normal">
                            {{ (page - 1) * pageSize + index + 1 }}
                        </span>
                    </td>
                    <td>
                        <span class="font-weight-bold text-dark d-block">
                            {{ cat.descripcion }}
                        </span>
                    </td>
                    <td>
                        <span class="text-muted">
                            {{ cat.detalle || '—' }}
                        </span>
                    </td>
                    <td>
                        <span class="text-muted small">
                            <i class="bi bi-calendar3 mr-1 text-secondary"></i>
                            {{ cat.created_at || '—' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div v-if="permissions.update || permissions.delete" class="btn-group btn-group-sm">
                            <button
                                v-if="permissions.update"
                                type="button"
                                class="btn btn-warning btn-sm"
                                title="Editar categoría"
                                @click="store.openEditModal(cat)"
                            >
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <button
                                v-if="permissions.delete"
                                type="button"
                                class="btn btn-danger btn-sm"
                                title="Eliminar categoría"
                                @click="confirmDelete(cat)"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <span v-else class="text-muted small font-italic">
                            No disponible
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Barra de paginación idéntica a HistoriesTable.vue -->
        <div v-if="!loading && total > 0" class="d-flex flex-wrap justify-content-between align-items-center px-3 py-2 border-top bg-light gap-2">
            <!-- Info + selector de tamaño de página -->
            <div class="d-flex align-items-center text-muted small">
                <span class="mr-2">
                    Mostrando <b>{{ rangeStart }}</b>–<b>{{ rangeEnd }}</b> de <b>{{ total }}</b> registros
                </span>
                <div class="input-group input-group-sm" style="width: auto;">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Por página</span>
                    </div>
                    <select
                        class="form-control form-control-sm"
                        :value="pageSize"
                        style="width: 64px;"
                        @change="store.setPageSize($event.target.value)"
                    >
                        <option v-for="n in PAGE_SIZE_OPTIONS" :key="n" :value="n">{{ n }}</option>
                    </select>
                </div>
            </div>

            <!-- Controles de navegación -->
            <nav aria-label="Paginación de categorías">
                <ul class="pagination pagination-sm mb-0">
                    <!-- Primera página -->
                    <li class="page-item" :class="{ disabled: page === 1 || loading }">
                        <button
                            class="page-link"
                            title="Primera página"
                            :disabled="page === 1 || loading"
                            @click="store.firstPage()"
                        >
                            «
                        </button>
                    </li>

                    <!-- Página anterior -->
                    <li class="page-item" :class="{ disabled: page === 1 || loading }">
                        <button
                            class="page-link"
                            title="Página anterior"
                            :disabled="page === 1 || loading"
                            @click="store.prevPage()"
                        >
                            ‹
                        </button>
                    </li>

                    <!-- Páginas visibles con elipsis -->
                    <template v-for="(p, i) in visiblePages" :key="i">
                        <li v-if="p === '…'" class="page-item disabled">
                            <span class="page-link">…</span>
                        </li>
                        <li v-else class="page-item" :class="{ active: p === page }">
                            <button
                                class="page-link"
                                :disabled="loading"
                                @click="store.goToPage(p)"
                            >
                                {{ p }}
                            </button>
                        </li>
                    </template>

                    <!-- Página siguiente -->
                    <li class="page-item" :class="{ disabled: page === totalPages || loading }">
                        <button
                            class="page-link"
                            title="Página siguiente"
                            :disabled="page === totalPages || loading"
                            @click="store.nextPage()"
                        >
                            ›
                        </button>
                    </li>

                    <!-- Última página -->
                    <li class="page-item" :class="{ disabled: page === totalPages || loading }">
                        <button
                            class="page-link"
                            title="Última página"
                            :disabled="page === totalPages || loading"
                            @click="store.lastPage()"
                        >
                            »
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>
