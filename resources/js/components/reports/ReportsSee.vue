<script setup>
import { ref, onMounted } from 'vue';
import { useReportsSeeStore } from '@/stores/reportsSeeStore';
import ReportDetailModal from '@/components/reports/ReportDetailModal.vue';

const props = defineProps({
    historyId: Number,
    dni: String,
    nombres: String,
    addUrl: String,
    canCreate: Boolean,
    homeUrl: String,
});

const store         = useReportsSeeStore();
const selectedId    = ref(null);
const modalVisible  = ref(false);

onMounted(() => store.fetchByHistory(props.historyId));

const openDetail        = (id) => {
    selectedId.value    = id;
    modalVisible.value  = true;
};

const confirmDelete = async (id) => {
    const r = await window.Swal.fire({
        icon: 'warning',
        title: '¿Eliminar informe?',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#d33',
    });
    if (!r.isConfirmed) return;
    try {
        await store.destroy(id);
        await store.fetchByHistory(props.historyId);
        window.alertNotify?.('success', 'Informe eliminado');
    } catch {
        window.Swal.fire('Error', 'No se pudo eliminar.', 'error');
    }
};

const goEdit  = (id) => window.location.href = `/admin/reports/edit/${id}`;
const goPrint = (id) => window.open(`/admin/reports/print/${id}`, '_blank');
</script>

<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1 class="m-0">Listado de reportes clínicos</h1></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a :href="homeUrl">Home</a></li>
                            <li class="breadcrumb-item"><a href="/admin/reports/home">Informes</a></li>
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
                                Menú de registros de reportes del paciente
                                <b>{{ nombres }}</b> identificado con DNI: <b>{{ dni }}</b>
                            </p>
                        </div>
                        <a v-if="canCreate" class="btn btn-primary mb-3" :href="addUrl">
                            <i class="bi bi-plus-circle"></i> Agregar nuevo reporte
                        </a>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>DNI</th>
                                        <th class="text-center">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="store.loading">
                                        <td colspan="4" class="text-center py-3 text-muted">
                                            <span class="spinner-border spinner-border-sm text-primary mr-2"></span>
                                            Cargando reportes...
                                        </td>
                                    </tr>
                                    <tr v-else-if="!store.items.length">
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox" style="font-size:2rem;"></i>
                                            <p class="mb-0 mt-2">Este paciente no tiene informes registrados.</p>
                                        </td>
                                    </tr>
                                    <tr v-for="r in store.items" :key="r.id">
                                        <td>{{ r.index }}</td>
                                        <td>{{ r.createdAt }}</td>
                                        <td><span class="badge badge-light border">{{ dni }}</span></td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-xs mr-1" @click="openDetail(r.id)">
                                                <i class="bi bi-eye"></i> Ver informe
                                            </button>
                                            <button class="btn btn-warning btn-xs mr-1" @click="goEdit(r.id)">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </button>
                                            <button class="btn btn-danger btn-xs" @click="confirmDelete(r.id)">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal de Detalles de Informe Clínico -->
        <ReportDetailModal
            :visible="modalVisible"
            :id="selectedId"
            :fallback-dni="dni"
            :fallback-nombres="nombres"
            @close="modalVisible = false"
        />
    </div>
</template>