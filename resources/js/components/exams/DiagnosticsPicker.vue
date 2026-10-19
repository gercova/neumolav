<script setup>
import { ref } from 'vue';
import { useExamFormStore } from '@/stores/examFormStore';
import SearchSelect from '@/components/histories/SearchSelect.vue';

const store = useExamFormStore();
const selected = ref(null);

const onSelected = (item) => {
    store.addDiagnostic(item);
    selected.value = null;
};

const onRemoveNew = async (i) => {
    const r = await window.Swal.fire({
        icon: 'warning',
        title: '¿Quitar este diagnóstico?',
        showCancelButton: true,
        confirmButtonText: 'Sí, quitar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
    });
    if (r.isConfirmed) {
        store.removeDiagnostic(i);
    }
};

const onDeleteExisting = async (i) => {
    const r = await window.Swal.fire({
        icon: 'warning',
        title: '¿Quitar este diagnóstico?',
        text: 'Esta acción no se puede deshacer.',
        showCancelButton: true,
        confirmButtonText: 'Sí, quitar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
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
                <div class="col-8">
                    <label class="font-weight-bold">Buscar:</label>
                    <SearchSelect
                        :model-value="selected"
                        :search-fn="store.searchDiagnostics"
                        placeholder="-- Buscar por código o nombre --"
                        @selected="onSelected"
                    />
                </div>

                <!-- NUEVOS -->
                <div class="col-12 mt-3">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width:90%">Diagnóstico</th>
                                <th style="width:10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!store.diagnostics.length && !store.existingDiagnostics?.length">
                                <td colspan="2" class="text-muted text-center py-3">Sin diagnósticos añadidos</td>
                            </tr>
                            <tr v-for="(d, i) in store.diagnostics" :key="`new-${i}`">
                                <td>{{ d.descripcion }}</td>
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
                <div v-if="store.existingDiagnostics?.length" class="col-12 mt-2">
                    <div class="text-muted small mb-1"><i class="bi bi-clock-history"></i> Diagnósticos ya guardados:</div>
                    <table class="table table-sm table-hover">
                        <tbody>
                            <tr v-for="(d, i) in store.existingDiagnostics" :key="`old-${d.id}`">
                                <td>{{ d.descripcion }}</td>
                                <td style="width:60px">
                                    <button type="button" class="btn btn-outline-danger btn-xs" @click.prevent.stop="onDeleteExisting(i)">
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