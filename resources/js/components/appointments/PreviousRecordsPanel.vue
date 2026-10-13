<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    dni: { type: String, required: true },
});

const API = () => window.API_BASE_URL;

const selected = ref('');
const html = ref('');
const loading = ref(false);

const fetchTable = async () => {
    if (!selected.value || !props.dni) {
        html.value = '';
        return;
    }
    loading.value = true;
    try {
        const { data } = await axios.post(`${API()}/appointments/view-table`, {
            table: selected.value,
            dni: props.dni,
        });
        html.value = data.html || '';
    } catch {
        html.value = '<div class="alert alert-danger">Error al cargar los datos.</div>';
    } finally {
        loading.value = false;
    }
};

watch(selected, fetchTable);
</script>

<template>
    <div class="card card-info">
        <div class="card-header"><h5 class="card-title">Ver recetas anteriores</h5></div>
        <div class="card-body">
            <h4 class="text-center">Ver datos</h4>
            <hr>
            <div class="form-group">
                <select v-model="selected" class="form-control">
                    <option value="">Selecciona tabla</option>
                    <option value="controles">Controles</option>
                    <option value="examenes">Exámenes</option>
                </select>
            </div>

            <div v-if="loading" class="text-center py-3">
                <span class="spinner-border spinner-border-sm text-primary mr-2"></span>
                Cargando...
            </div>

            <div v-else class="table-responsive" v-html="html"></div>
        </div>
    </div>
</template>