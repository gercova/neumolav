<script setup>
import { ref } from 'vue';
import { useAppointmentFormStore } from '@/stores/appointmentFormStore';
import SearchSelect from '@/components/histories/SearchSelect.vue';

const store = useAppointmentFormStore();
const selected = ref(null);

const onSelected = (item) => {
    store.addDiagnostic(item);
    selected.value = null;
};

const onDeleteExisting = async (i) => {
    const r = await window.Swal.fire({
        icon: 'warning', title: '¿Quitar este diagnóstico?',
        showCancelButton: true, confirmButtonText: 'Sí, quitar',
    });
    if (!r.isConfirmed) return;
    try {
        const data = await store.deleteExistingDiagnostic(i);
        window.alertNotify?.('success', data?.messages || 'Diagnóstico eliminado');
    } catch {
        window.Swal.fire('Error', 'No se pudo eliminar.', 'error');
    }
};
</script>

<template>
    <div class="card card-info">
        <div class="card-header"><h5 class="card-title">Diagnósticos</h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Buscar:</label>
                        <div class="col-sm-9">
                            <SearchSelect
                                :model-value="selected"
                                :search-fn="store.searchDiagnostics"
                                placeholder="-- Buscar por código o nombre --"
                                @selected="onSelected"
                            />
                        </div>
                    </div>
                </div>

                <!-- NUEVOS -->
                <div class="col-12">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width:90%">Diagnóstico</th>
                                <th style="width:10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(d, i) in store.diagnostics" :key="`new-${i}`">
                                <td>{{ d.descripcion }}</td>
                                <td>
                                    <button class="btn btn-danger btn-xs" @click="store.removeDiagnostic(i)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- EXISTENTES (solo en edit) -->
                <div v-if="store.existingDiagnostics.length" class="col-12 mt-2">
                    <div class="text-muted small mb-1"><i class="bi bi-clock-history"></i> Diagnósticos ya guardados:</div>
                    <table class="table table-sm table-hover">
                        <tbody>
                            <tr v-for="(d, i) in store.existingDiagnostics" :key="`old-${d.id}`">
                                <td>{{ d.descripcion }}</td>
                                <td style="width:60px">
                                    <button class="btn btn-outline-danger btn-xs" @click="onDeleteExisting(i)">
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
</template>