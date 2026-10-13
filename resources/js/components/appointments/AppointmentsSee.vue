<script setup>
import { onMounted } from 'vue';
import { useAppointmentsSeeStore } from '@/stores/appointmentsSeeStore';

const props = defineProps({
    historyId: Number,
    dni: String,
    nombres: String,
    addUrl: String,
    canCreate: Boolean,
    homeUrl: String,
});

const store = useAppointmentsSeeStore();

onMounted(() => store.fetchByHistory(props.historyId));

const confirmDelete = async (id) => {
    const r = await window.Swal.fire({
        icon: 'warning',
        title: '¿Eliminar control?',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#d33',
    });
    if (!r.isConfirmed) return;
    try {
        await store.destroy(id);
        await store.fetchByHistory(props.historyId);
        window.alertNotify?.('success', 'Control eliminado');
    } catch {
        window.Swal.fire('Error', 'No se pudo eliminar.', 'error');
    }
};

const goEdit  = (id) => window.location.href = `/admin/appointments/edit/${id}`;
const goPrint = (id, f) => window.open(`/admin/appointments/print/${id}/${f}`, '_blank');
</script>

<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1 class="m-0">Listado de citas y controles clínicos</h1></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a :href="homeUrl">Home</a></li>
                            <li class="breadcrumb-item"><a href="/admin/appointments/home">Controles</a></li>
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
                                Menú de registros de citas y controles del paciente
                                <b>{{ nombres }}</b> identificado con DNI: <b>{{ dni }}</b>
                            </p>
                        </div>
                        <a v-if="canCreate" class="btn btn-primary mb-3" :href="addUrl">
                            <i class="bi bi-plus-circle"></i> Agregar nuevo control
                        </a>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th class="text-center">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="store.loading">
                                        <td colspan="3" class="text-center py-3 text-muted">
                                            <span class="spinner-border spinner-border-sm text-primary mr-2"></span>
                                            Cargando controles...
                                        </td>
                                    </tr>
                                    <tr v-else-if="!store.items.length">
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox" style="font-size:2rem;"></i>
                                            <p class="mb-0 mt-2">Este paciente no tiene controles registrados.</p>
                                        </td>
                                    </tr>
                                    <tr v-for="c in store.items" :key="c.id">
                                        <td>{{ c.index }}</td>
                                        <td>{{ c.createdAt }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-xs mr-1" @click="goEdit(c.id)">
                                                <i class="bi bi-eye"></i> Ver receta
                                            </button>
                                            <button class="btn btn-warning btn-xs mr-1" @click="goEdit(c.id)">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </button>
                                            <div class="btn-group">
                                                <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                    <i class="bi bi-printer"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#" @click.prevent="goPrint(c.id, 'a4')">
                                                        <i class="bi bi-file-pdf text-danger"></i> A4
                                                    </a>
                                                    <a class="dropdown-item" href="#" @click.prevent="goPrint(c.id, 'a5')">
                                                        <i class="bi bi-file-pdf text-info"></i> A5
                                                    </a>
                                                </div>
                                            </div>
                                            <button class="btn btn-danger btn-xs ml-1" @click="confirmDelete(c.id)">
                                                <i class="bi bi-trash"></i>
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
    </div>
</template>