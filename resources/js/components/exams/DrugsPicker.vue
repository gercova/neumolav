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
                            <tr v-if="!store.drugs.length">
                                <td colspan="3" class="text-muted text-center py-3">Sin fármacos añadidos</td>
                            </tr>
                            <tr v-for="(d, i) in store.drugs" :key="i">
                                <td>{{ d.descripcion }}</td>
                                <td>
                                    <input v-model="d.rp" type="text" class="form-control form-control-sm"
                                           placeholder="Posología / indicaciones">
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-xs" @click="store.removeDrug(i)">
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