<script setup>
import { onMounted } from 'vue';
import { useHistoriesStore } from '@/stores/historiesStore';
import HistoriesFilters from './HistoriesFilters.vue';
import HistoriesTable from './HistoriesTable.vue';

const props = defineProps({
    addUrl:  { type: String, required: true },
    editUrl: { type: String, required: true }, // ej. "/admin/histories/edit"
});

const store = useHistoriesStore();

onMounted(() => store.fetchPage(1));
</script>

<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Historias</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/admin/home">Home</a></li>
                            <li class="breadcrumb-item active">Historias</li>
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
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <a :href="addUrl" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Agregar nueva historia
                                </a>
                                <HistoriesFilters />
                            </div>
                            <div class="card-body p-0">
                                <HistoriesTable :edit-url="editUrl" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>