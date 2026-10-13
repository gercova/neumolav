<script setup>
import { onMounted } from 'vue';
import { useAppointmentsStore } from '@/stores/appointmentsStore';
import AppointmentsFilters from './AppointmentsFilters.vue';
import AppointmentsTable   from './AppointmentsTable.vue';

const store = useAppointmentsStore();

onMounted(() => store.fetchPage(1));
</script>

<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Controles</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/admin/home">Home</a></li>
                            <li class="breadcrumb-item active">Controles</li>
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
                                    <i class="bi bi-calendar2-check mr-2 text-primary" style="font-size: 1.2rem;"></i>
                                    <span class="font-weight-bold">Seleccione un paciente para gestionar sus controles</span>
                                </div>
                                <AppointmentsFilters />
                            </div>
                            <div class="card-body p-0">
                                <AppointmentsTable />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
