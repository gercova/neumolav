<script setup>
import { onMounted } from 'vue';
import { useSpecialtiesStore } from '@/stores/specialtiesStore';
import SpecialtiesFilters from './SpecialtiesFilters.vue';
import SpecialtiesTable from './SpecialtiesTable.vue';
import SpecialtyModal from './SpecialtyModal.vue';

const props = defineProps({
    initialPermissions: {
        type: Object,
        default: () => ({}),
    },
    initialOccupations: {
        type: Array,
        default: () => [],
    },
});

const store = useSpecialtiesStore();

onMounted(() => {
    if (props.initialPermissions && Object.keys(props.initialPermissions).length > 0) {
        store.permissions = { ...store.permissions, ...props.initialPermissions };
    }
    if (props.initialOccupations && props.initialOccupations.length > 0) {
        store.occupations = props.initialOccupations;
    }

    store.fetchPage(1);
});
</script>

<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Especialidades</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/admin/home">Home</a></li>
                            <li class="breadcrumb-item">Seguridad</li>
                            <li class="breadcrumb-item active">Especialidades</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <button
                                    v-if="store.permissions.create"
                                    type="button"
                                    class="btn btn-primary"
                                    @click="store.openCreateModal()"
                                >
                                    <i class="bi bi-plus-circle mr-1"></i> Agregar Especialidad
                                </button>
                                <span v-else class="text-muted small">
                                    <i class="bi bi-info-circle mr-1"></i> Consulta de especialidades
                                </span>

                                <SpecialtiesFilters />
                            </div>

                            <div class="card-body p-0">
                                <SpecialtiesTable />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Modal para Agregar y Editar Especialidad -->
        <SpecialtyModal />
    </div>
</template>

<style scoped>
.gap-2 {
    gap: 0.5rem;
}
</style>
