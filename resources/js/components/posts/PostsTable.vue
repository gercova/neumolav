<script setup>
import { storeToRefs } from 'pinia';
import { usePostsStore } from '@/stores/postsStore';

const store = usePostsStore();
const { items, loading, permissions, page, total, rangeStart, rangeEnd, totalPages, visiblePages } = storeToRefs(store);

const confirmDelete = async (post) => {
    const swal = window.Swal;
    if (!swal) {
        if (confirm(`¿Está seguro de eliminar la publicación "${post.titulo}"?`)) {
            try {
                await store.destroy(post.id);
            } catch (e) {
                alert('No se pudo eliminar la publicación.');
            }
        }
        return;
    }

    const result = await swal.fire({
        title: '¿Eliminar publicación?',
        html: `¿Está seguro de eliminar la publicación <b>${post.titulo}</b>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
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
        await store.destroy(post.id);
    } catch (e) {
        swal.fire('Error', 'No se pudo eliminar la publicación.', 'error');
    }
};

const truncate = (str, len = 60) => (str && str.length > len ? str.substring(0, len) + '…' : str ?? '—');
</script>

<template>
    <div>
        <!-- Loading skeleton -->
        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="text-muted mt-2 small">Cargando publicaciones...</p>
        </div>

        <template v-else>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0 align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th class="pl-3" style="width: 50px;">#</th>
                            <th>Título</th>
                            <th style="width: 110px;">Tipo</th>
                            <th style="width: 80px;">Imagen</th>
                            <th style="width: 140px;">Fecha</th>
                            <th style="width: 100px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="items.length === 0">
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                No se encontraron publicaciones
                            </td>
                        </tr>
                        <tr v-for="(post, idx) in items" :key="post.id">
                            <td class="pl-3 text-muted small">{{ rangeStart + idx }}</td>
                            <td>
                                <div class="font-weight-semibold text-dark">{{ truncate(post.titulo, 70) }}</div>
                                <div v-if="post.titulo_corto" class="text-muted small">{{ truncate(post.titulo_corto, 60) }}</div>
                            </td>
                            <td>
                                <span class="badge badge-secondary px-2 py-1">
                                    {{ post.post_type ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <img
                                    v-if="post.img_url"
                                    :src="post.img_url"
                                    :alt="post.alt_img || post.titulo"
                                    class="rounded border"
                                    style="width: 48px; height: 36px; object-fit: cover;"
                                >
                                <span v-else class="text-muted small">Sin imagen</span>
                            </td>
                            <td class="text-muted small">{{ post.created_at ?? '—' }}</td>
                            <td class="text-center">
                                <button
                                    v-if="permissions.update"
                                    type="button"
                                    class="btn btn-sm btn-warning mr-1"
                                    title="Editar"
                                    @click="store.openEditPanel(post)"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button
                                    v-if="permissions.delete"
                                    type="button"
                                    class="btn btn-sm btn-danger"
                                    title="Eliminar"
                                    @click="confirmDelete(post)"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                                <span v-if="!permissions.update && !permissions.delete" class="text-muted small">—</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div v-if="total > 0" class="d-flex flex-wrap align-items-center justify-content-between px-3 py-2 border-top bg-light">
                <small class="text-muted">
                    Mostrando {{ rangeStart }}–{{ rangeEnd }} de {{ total }} publicaciones
                </small>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item" :class="{ disabled: page <= 1 }">
                            <button class="page-link" @click="store.prevPage()">‹</button>
                        </li>
                        <li
                            v-for="p in visiblePages"
                            :key="p"
                            class="page-item"
                            :class="{ active: p === page, disabled: p === '…' }"
                        >
                            <button class="page-link" @click="p !== '…' && store.goToPage(p)">
                                {{ p }}
                            </button>
                        </li>
                        <li class="page-item" :class="{ disabled: page >= totalPages }">
                            <button class="page-link" @click="store.nextPage()">›</button>
                        </li>
                    </ul>
                </nav>
            </div>
        </template>
    </div>
</template>

<style scoped>
.font-weight-semibold {
    font-weight: 600;
}
</style>
