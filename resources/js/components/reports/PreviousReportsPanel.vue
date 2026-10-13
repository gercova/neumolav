<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    historyId: { type: Number, required: true },
});

const API = () => window.API_BASE_URL;

const items = ref([]);
const loading = ref(false);

const fetch = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(`${API()}/reports/listReports/${props.historyId}`);
        items.value = (data.aaData || []).map((row, i) => ({
            index:     i + 1,
            createdAt: row[1],
            id:        extractId(row[2]),
        }));
    } finally {
        loading.value = false;
    }
};

const extractId = (html) => {
    const m = String(html || '').match(/value="(\d+)"/);
    return m ? +m[1] : null;
};

onMounted(fetch);

const viewReport = (id) => window.open(`/admin/reports/print/${id}`, '_blank');
</script>

<template>
    <div class="card card-info">
        <div class="card-header"><h5 class="card-title">Informes anteriores</h5></div>
        <div class="card-body">
            <div v-if="loading" class="text-center py-3">
                <span class="spinner-border spinner-border-sm text-primary mr-2"></span>
                Cargando...
            </div>
            <table v-else class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width:10%">#</th>
                        <th style="width:45%">Fecha</th>
                        <th style="width:45%">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!items.length">
                        <td colspan="3" class="text-muted text-center py-3">
                            Sin informes previos
                        </td>
                    </tr>
                    <tr v-for="it in items" :key="it.id">
                        <td>{{ it.index }}</td>
                        <td>{{ it.createdAt }}</td>
                        <td>
                            <button class="btn btn-info btn-xs" @click="viewReport(it.id)">
                                <i class="bi bi-eye"></i> Ver informe
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>