<script setup>
import { onMounted } from 'vue';
import { useExamsStore } from '@/stores/examsStore';
import ExamsFilters from './ExamsFilters.vue';
import ExamsTable   from './ExamsTable.vue';

const store = useExamsStore();

onMounted(() => store.fetchPage(1));
</script>

<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Exámenes</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/admin/home">Home</a></li>
                            <li class="breadcrumb-item active">Exámenes</li>
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
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-medical mr-2 text-primary" style="font-size: 1.2rem;"></i>
                                    <span class="font-weight-bold">Seleccione un paciente para gestionar sus exámenes</span>
                                </div>
                                <ExamsFilters />
                            </div>
                            <div class="card-body p-0">
                                <ExamsTable />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
