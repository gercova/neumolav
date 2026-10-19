<script setup>
import { ref } from 'vue';
import { useExamFormStore } from '@/stores/examFormStore';
import SearchSelect from '@/components/histories/SearchSelect.vue';

const store = useExamFormStore();
const selected = ref(null);

const onSelected = (item) => {
    store.addDrug(item);
    selected.value = null;
};

const onRemoveNew = async (i) => {
    const r = await window.Swal.fire({
        icon: 'warning',
        title: '¿Quitar este medicamento?',
        showCancelButton: true,
        confirmButtonText: 'Sí, quitar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
    });
    if (r.isConfirmed) {
        store.removeDrug(i);
    }
};

const onDeleteExisting = async (i) => {
    const r = await window.Swal.fire({
        icon: 'warning',
        title: '¿Quitar este medicamento?',
        text: 'Esta acción no se puede deshacer.',
        showCancelButton: true,
        confirmButtonText: 'Sí, quitar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
    });
    if (!r.isConfirmed) return;
    try {
        const data = await store.deleteExistingMedication(i);
        window.alertNotify?.('success', data?.messages || 'Medicamento eliminado');
    } catch {
        window.Swal.fire('Error', 'No se pudo eliminar.', 'error');
    }
};
</script>

<template>
    <div class="card card-info">
        <div class="card-header"><h5 class="card-title">Tratamiento</h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-8">
                    <label class="font-weight-bold">Buscar:</label>
                    <SearchSelect
                        :model-value="selected"
                        :search-fn="store.searchDrugs"
                        placeholder="-- Buscar por descripción --"
                        @selected="onSelected"
                    />
                </div>

                <!-- NUEVOS -->
                <div class="col-12 mt-3">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width:30%">Fármaco</th>
                                <th style="width:60%">Receta</th>
                                <th style="width:10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!store.drugs.length && !store.existingMedications?.length">
                                <td colspan="3" class="text-muted text-center py-3">Sin fármacos añadidos</td>
                            </tr>
                            <tr v-for="(d, i) in store.drugs" :key="`new-${i}`">
                                <td>{{ d.descripcion }}</td>
                                <td>
                                    <input v-model="d.rp" type="text" class="form-control form-control-sm"
                                           placeholder="Posología / indicaciones">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-xs" @click.prevent.stop="onRemoveNew(i)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- EXISTENTES (solo en edit) -->
                <div v-if="store.existingMedications?.length" class="col-12 mt-2">
                    <div class="text-muted small mb-1"><i class="bi bi-clock-history"></i> Medicamentos ya guardados:</div>
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Fármaco</th>
                                <th>Receta</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(m, i) in store.existingMedications" :key="`old-${m.id}`">
                                <td>{{ m.descripcion }}</td>
                                <td class="text-muted small">{{ m.rp }}</td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-xs" @click.prevent.stop="onDeleteExisting(i)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-12 mt-3">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label font-weight-bold">Recomendaciones:</label>
                        <div class="col-sm-10">
                            <textarea v-model="store.form.recomendaciones" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>