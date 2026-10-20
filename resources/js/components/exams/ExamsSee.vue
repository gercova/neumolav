<script setup>
import { ref, onMounted } from 'vue';
import { useExamsSeeStore } from '@/stores/examsSeeStore';
import { useClientPagination } from '@/composables/useClientPagination';
import PrescriptionDetailModal from '@/components/shared/PrescriptionDetailModal.vue';

const props = defineProps({
    historyId: Number,
    dni: String,
    nombres: String,
    addUrl: String,
    canCreate: Boolean,
    homeUrl: String,
});

const store = useExamsSeeStore();
const selectedId = ref(null);
const modalVisible = ref(false);

const {
    currentPage,
    pageSize,
    pageSizeOptions,
    searchQuery,
    filteredItems,
    totalRecords,
    totalPages,
    paginatedItems,
    startRecord,
    endRecord,
    visiblePages,
    goToPage,
    prevPage,
    nextPage,
} = useClientPagination(() => store.items, 10);

onMounted(() => store.fetchByHistory(props.historyId));

const openDetail = (id) => {
    selectedId.value = id;
    modalVisible.value = true;
};

const confirmDelete = async (id) => {
    const r = await window.Swal.fire({
        icon: 'warning',
        title: '¿Eliminar examen?',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#d33',
    });
    if (!r.isConfirmed) return;
    try {
        await store.destroy(id);
        await store.fetchByHistory(props.historyId);
        window.alertNotify?.('success', 'Examen eliminado');
    } catch {
        window.Swal.fire('Error', 'No se pudo eliminar.', 'error');
    }
};

const goEdit = (id) => window.location.href = `/admin/exams/edit/${id}`;
const goPrint = (id, format) => window.open(`/admin/exams/print/${id}/${format}`, '_blank');
</script>

<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1 class="m-0">Listado de exámenes</h1></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a :href="homeUrl">Home</a></li>
                            <li class="breadcrumb-item"><a href="/admin/exams/home">Exámenes</a></li>
                            <li class="breadcrumb-item active">Listado</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">Información del paciente</div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <p class="mb-0">
                                Menú de registros de exámenes del paciente
                                <b>{{ nombres }}</b> identificado con DNI: <b>{{ dni }}</b>
                            </p>
                        </div>
                        <a v-if="canCreate" class="btn btn-primary mb-3" :href="addUrl">
                            <i class="bi bi-plus-circle"></i> Agregar nuevo examen
                        </a>

                        <!-- Controles de Tabla: PageSize y Búsqueda -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <label class="text-muted small mb-0 d-flex align-items-center">
                                    Mostrar
                                    <select v-model="pageSize" class="custom-select custom-select-sm form-control form-control-sm mx-2" style="width: auto;">
                                        <option v-for="opt in pageSizeOptions" :key="opt" :value="opt">{{ opt }}</option>
                                    </select>
                                    registros
                                </label>
                            </div>
                            <div class="d-flex align-items-center mt-2 mt-sm-0">
                                <label class="text-muted small mb-0 d-flex align-items-center">
                                    Buscar:
                                    <input
                                        v-model="searchQuery"
                                        type="search"
                                        class="form-control form-control-sm ml-2"
                                        placeholder=""
                                        style="width: 180px;"
                                    >
                                </label>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">#</th>
                                        <th>Fecha</th>
                                        <th>DNI</th>
                                        <th>Tipo Examen</th>
                                        <th class="text-center" style="width: 250px;">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="store.loading">
                                        <td colspan="5" class="text-center py-3 text-muted">
                                             <span class="spinner-border spinner-border-sm text-primary mr-2"></span>
                                             Cargando exámenes...
                                        </td>
                                    </tr>
                                    <tr v-else-if="!store.items.length">
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox" style="font-size:2rem;"></i>
                                            <p class="mb-0 mt-2">Este paciente no tiene exámenes registrados.</p>
                                        </td>
                                    </tr>
                                    <tr v-else-if="!filteredItems.length">
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-search mr-1"></i> No se encontraron exámenes que coincidan con la búsqueda.
                                        </td>
                                    </tr>
                                    <tr v-for="(ex, idx) in paginatedItems" :key="ex.id || idx">
                                        <td>{{ (currentPage - 1) * pageSize + idx + 1 }}</td>
                                        <td>{{ ex.createdAt }}</td>
                                        <td><span class="badge badge-light border">{{ ex.dni }}</span></td>
                                        <td>{{ ex.tipo }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-xs mr-1" @click="openDetail(ex.id)">
                                                <i class="bi bi-eye"></i> Ver receta
                                            </button>
                                            <button class="btn btn-warning btn-xs mr-1" @click="goEdit(ex.id)">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </button>
                                            <div class="btn-group">
                                                <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                    <i class="bi bi-printer"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#" @click.prevent="goPrint(ex.id, 'a4')">
                                                        <i class="bi bi-file-pdf text-danger"></i> A4
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="goPrint(ex.id, 'a5')">
                                                        <i class="bi bi-file-pdf text-info"></i> A5
                                                    </a>
                                                </div>
                                            </div>
                                            <button class="btn btn-danger btn-xs ml-1" @click="confirmDelete(ex.id)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Barra de Paginación y Resumen -->
                        <div v-if="store.items.length > 0" class="d-flex flex-wrap justify-content-between align-items-center mt-3 pt-2 border-top">
                            <div class="text-muted small mb-2 mb-sm-0">
                                Mostrando {{ startRecord }} a {{ endRecord }} de {{ totalRecords }} registros
                            </div>
                            <nav aria-label="Navegación de páginas">
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                        <button class="page-link" :disabled="currentPage === 1" @click="prevPage">
                                            Anterior
                                        </button>
                                    </li>
                                    <li
                                        v-for="(p, pIdx) in visiblePages"
                                        :key="pIdx"
                                        class="page-item"
                                        :class="{ active: p === currentPage, disabled: p === '…' }"
                                    >
                                        <span v-if="p === '…'" class="page-link">…</span>
                                        <button v-else class="page-link" @click="goToPage(p)">
                                            {{ p }}
                                        </button>
                                    </li>
                                    <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                        <button class="page-link" :disabled="currentPage === totalPages" @click="nextPage">
                                            Siguiente
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal de Detalles de Examen / Receta -->
        <PrescriptionDetailModal
            :visible="modalVisible"
            :id="selectedId"
            type="exams"
            :fallback-dni="dni"
            :fallback-nombres="nombres"
            @close="modalVisible = false"
        />
    </div>
</template>