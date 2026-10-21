<script setup>
import { ref } from 'vue';
import { useConfigStore } from '@/stores/configStore';

const store = useConfigStore();

const searchInput = ref('');
let searchTimeout = null;

const onSearchInput = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        store.setSearch(searchInput.value);
    }, 350);
};

const onTypeChange = (e) => {
    store.setTypeFilter(e.target.value);
};

const onPageSizeChange = (e) => {
    store.setPageSize(e.target.value);
};

const confirmDelete = async (version) => {
    if (confirm(`¿Está seguro de eliminar el registro de la versión v${version.version}?`)) {
        await store.deleteVersion(version.id);
    }
};

const typeBadgeClass = (type) => {
    switch (type) {
        case 'MAJOR':
            return 'badge-danger';
        case 'MINOR':
            return 'badge-primary';
        case 'PATCH':
        default:
            return 'badge-success';
    }
};
</script>

<template>
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <!-- Filters Header -->
        <div class="card-header bg-white py-3 px-4">
            <div class="row g-2 align-items-center justify-content-between">
                <!-- Search -->
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                        </div>
                        <input
                            v-model="searchInput"
                            type="text"
                            class="form-control border-left-0 bg-light"
                            placeholder="Buscar por versión, resumen o commit..."
                            @input="onSearchInput"
                        />
                    </div>
                </div>

                <!-- Type & PageSize Filters -->
                <div class="col-12 col-md-8 d-flex justify-content-md-end align-items-center gap-2 flex-wrap">
                    <div class="d-flex align-items-center gap-1">
                        <label class="text-muted small mb-0 mr-1">Tipo:</label>
                        <select
                            class="form-control form-control-sm"
                            style="width: 130px;"
                            :value="store.typeFilter"
                            @change="onTypeChange"
                        >
                            <option value="">Todos los tipos</option>
                            <option value="MAJOR">MAJOR</option>
                            <option value="MINOR">MINOR</option>
                            <option value="PATCH">PATCH</option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <label class="text-muted small mb-0 mr-1">Mostrar:</label>
                        <select
                            class="form-control form-control-sm"
                            style="width: 80px;"
                            :value="store.pageSize"
                            @change="onPageSizeChange"
                        >
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                        </select>
                    </div>

                    <!-- Reload Button -->
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary"
                        title="Actualizar lista"
                        @click="store.fetchVersions()"
                        :disabled="store.loading"
                    >
                        <i class="bi bi-arrow-clockwise" :class="{ 'spin-animation': store.loading }"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="thead-light" style="font-size: 0.85rem;">
                    <tr>
                        <th style="width: 120px;">Versión</th>
                        <th style="width: 90px;">Tipo</th>
                        <th>Resumen de Cambios</th>
                        <th>Métricas Capturadas</th>
                        <th>Autor & Fecha</th>
                        <th style="width: 140px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loading state -->
                    <tr v-if="store.loading">
                        <td colspan="6" class="text-center py-5">
                            <div class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div>
                            <span class="text-muted">Cargando versiones...</span>
                        </td>
                    </tr>

                    <!-- Empty state -->
                    <tr v-else-if="store.versions.length === 0">
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 text-secondary d-block mb-2"></i>
                            No se encontraron registros de versiones con los filtros aplicados.
                        </td>
                    </tr>

                    <!-- Rows -->
                    <tr v-for="item in store.versions" :key="item.id">
                        <!-- Version tag -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-light border text-dark font-monospace px-2 py-1" style="font-size: 0.9rem;">
                                    <i class="bi bi-tag mr-1 text-primary"></i>v{{ item.version }}
                                </span>
                            </div>
                            <small v-if="item.commit_hash" class="text-muted d-block mt-1 font-monospace">
                                <i class="bi bi-git mr-1 text-danger"></i>{{ item.commit_hash }}
                            </small>
                        </td>

                        <!-- SemVer Type -->
                        <td>
                            <span class="badge" :class="typeBadgeClass(item.version_type)">
                                {{ item.version_type }}
                            </span>
                        </td>

                        <!-- Summary & Description preview -->
                        <td>
                            <strong class="text-dark d-block mb-1">{{ item.summary }}</strong>
                            <p
                                v-if="item.description"
                                class="text-muted small mb-0 text-truncate"
                                style="max-width: 380px;"
                                :title="item.description"
                            >
                                {{ item.description }}
                            </p>
                        </td>

                        <!-- Metrics tags -->
                        <td>
                            <div class="d-flex flex-wrap gap-1" style="font-size: 0.75rem;">
                                <span class="badge badge-light border text-primary" title="Clases cambiadas">
                                    <i class="bi bi-code-slash mr-1"></i>{{ item.classes_count }} Clases
                                </span>
                                <span class="badge badge-light border text-indigo" title="Métodos capturados con parámetros">
                                    <i class="bi bi-braces mr-1"></i>{{ item.methods_count }} Métodos
                                </span>
                                <span class="badge badge-light border text-success" title="Vistas Blade modificadas">
                                    <i class="bi bi-file-earmark-code mr-1"></i>{{ item.views_count }} Vistas
                                </span>
                                <span class="badge badge-light border text-warning" title="Scripts y Vue">
                                    <i class="bi bi-filetype-vue mr-1"></i>{{ item.scripts_count }} Scripts
                                </span>
                                <span v-if="item.updates_count > 0" class="badge badge-light border text-secondary" title="Actualizaciones del sistema">
                                    <i class="bi bi-gear mr-1"></i>{{ item.updates_count }} Updates
                                </span>
                            </div>
                        </td>

                        <!-- Author & Date -->
                        <td>
                            <div class="d-flex align-items-center gap-1 small text-dark">
                                <i class="bi bi-person text-secondary mr-1"></i>{{ item.author }}
                            </div>
                            <small class="text-muted d-block" :title="item.created_at">
                                <i class="bi bi-clock mr-1"></i>{{ item.created_at_human }}
                            </small>
                        </td>

                        <!-- Actions -->
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary"
                                    title="Ver registro detallado de cambios"
                                    @click="store.openDetailModal(item.id)"
                                >
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                <button
                                    v-if="store.permissions.delete"
                                    type="button"
                                    class="btn btn-outline-danger"
                                    title="Eliminar del historial"
                                    @click="confirmDelete(item)"
                                    :disabled="store.deleting"
                                >
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        <div class="card-footer bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">
                Mostrando <strong class="text-dark">{{ store.rangeStart }}</strong> a <strong class="text-dark">{{ store.rangeEnd }}</strong> de <strong class="text-dark">{{ store.total }}</strong> versiones
            </span>

            <nav v-if="store.totalPages > 1" aria-label="Navegación de versiones">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{ disabled: store.page === 1 }">
                        <button class="page-link" @click="store.prevPage()" :disabled="store.page === 1">
                            &laquo; Anterior
                        </button>
                    </li>
                    <li
                        v-for="(p, idx) in store.visiblePages"
                        :key="idx"
                        class="page-item"
                        :class="{ active: p === store.page, disabled: p === '…' }"
                    >
                        <span v-if="p === '…'" class="page-link">…</span>
                        <button v-else class="page-link" @click="store.goToPage(p)">
                            {{ p }}
                        </button>
                    </li>
                    <li class="page-item" :class="{ disabled: store.page === store.totalPages }">
                        <button class="page-link" @click="store.nextPage()" :disabled="store.page === store.totalPages">
                            Siguiente &raquo;
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<style scoped>
.spin-animation {
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    100% {
        transform: rotate(360deg);
    }
}
</style>
