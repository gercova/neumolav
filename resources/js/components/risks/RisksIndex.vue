<script setup>
import { onMounted } from 'vue';
import { useRisksStore } from '@/stores/risksStore';
import RisksFilters from './RisksFilters.vue';
import RisksTable   from './RisksTable.vue';

const store = useRisksStore();

onMounted(() => store.fetchPage(1));
</script>

<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Informes de Riesgo</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/admin/home">Home</a></li>
                            <li class="breadcrumb-item active">Informes de Riesgo</li>
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
                                    <i class="bi bi-shield-check mr-2 text-primary" style="font-size: 1.2rem;"></i>
                                    <span class="font-weight-bold">Seleccione un paciente para gestionar sus informes de riesgo</span>
                                </div>
                                <RisksFilters />
                            </div>
                            <div class="card-body p-0">
                                <RisksTable />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
