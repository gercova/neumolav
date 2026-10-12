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
                <div class="col-12 mt-3">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width:90%">Diagnóstico</th>
                                <th style="width:10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!store.diagnostics.length">
                                <td colspan="2" class="text-muted text-center py-3">Sin diagnósticos añadidos</td>
                            </tr>
                            <tr v-for="(d, i) in store.diagnostics" :key="d.id">
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
            </div>
        </div>
    </div>
</template>