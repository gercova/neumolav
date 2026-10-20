<script setup>
import { storeToRefs } from 'pinia';
import { useAppointmentsStore } from '@/stores/appointmentsStore';
import AppointmentRowActions from './AppointmentRowActions.vue';

const store = useAppointmentsStore();
const { items, loading, page, pageSize, total, totalPages, visiblePages } = storeToRefs(store);

const rangeStart = () => total.value === 0 ? 0 : (page.value - 1) * pageSize.value + 1;
const rangeEnd   = () => Math.min(page.value * pageSize.value, total.value);

const PAGE_SIZE_OPTIONS = [10, 25, 50, 100];
</script>

<template>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead class="thead-light">
                <tr>
                    <th style="width:8%">Fecha R.</th>
                    <th style="width:8%">DNI</th>
                    <th style="width:30%">Paciente</th>
                    <th style="width:10%">F. Nac.</th>
                    <th style="width:6%">Edad</th>
                    <th style="width:7%">Sexo</th>
                    <th style="width:21%" class="text-center">Opciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Cargando -->
                <tr v-if="loading">
                    <td colspan="7" class="text-center py-4 text-muted">
                        <span class="spinner-border spinner-border-sm text-primary mr-2"></span>
                        Cargando pacientes...
                    </td>
                </tr>
                <!-- Sin resultados -->
                <tr v-else-if="items.length === 0">
                    <td colspan="7" class="text-center py-5">
                        <i class="bi bi-folder-x text-muted" style="font-size: 2.5rem; opacity: .5;"></i>
                        <h6 class="text-dark font-weight-bold mt-2 mb-1">Sin resultados</h6>
                        <p class="text-muted small mb-0">
                            {{ store.search
                                ? 'No se encontraron pacientes con esa búsqueda.'
                                : 'Aún no hay historias clínicas registradas.' }}
                        </p>
                    </td>
                </tr>
                <!-- Filas -->
                <tr v-else v-for="h in items" :key="h.id">
                    <td class="text-muted small">{{ h.fecha }}</td>
                    <td><span class="badge badge-light border">{{ h.dni }}</span></td>
                    <td class="font-weight-bold text-dark">{{ h.nombres }}</td>
                    <td class="text-muted small">{{ h.fecha_nacimiento }}</td>
                    <td><span class="badge badge-info">{{ h.edad }}</span></td>
                    <td class="text-muted small">{{ h.sexo }}</td>
                    <td class="text-center">
                        <AppointmentRowActions :record="h" />
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Barra de paginación -->
        <div v-if="!loading && total > 0" class="d-flex flex-wrap justify-content-between align-items-center px-3 py-2 border-top bg-light gap-2">
            <!-- Info + selector de tamaño de página -->
            <div class="d-flex align-items-center text-muted small">
                <span class="mr-2">
                    Mostrando <b>{{ rangeStart() }}</b>–<b>{{ rangeEnd() }}</b> de <b>{{ total }}</b> registros
                </span>
                <div class="input-group input-group-sm" style="width: auto;">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Por página</span>
                    </div>
                    <select
                        class="form-control form-control-sm"
                        :value="pageSize"
                        @change="store.setPageSize($event.target.value)"
                        style="width: 64px;"
                    >
                        <option v-for="n in PAGE_SIZE_OPTIONS" :key="n" :value="n">{{ n }}</option>
                    </select>
                </div>
            </div>
            <!-- Controles de navegación -->
            <nav aria-label="Paginación de controles">
                <ul class="pagination pagination-sm mb-0">
                    <!-- Primera -->
                    <li class="page-item" :class="{ disabled: page === 1 || loading }">
                        <button class="page-link" title="Primera página" @click="store.firstPage()" :disabled="page === 1 || loading">«</button>
                    </li>
                    <!-- Anterior -->
                    <li class="page-item" :class="{ disabled: page === 1 || loading }">
                        <button class="page-link" title="Página anterior" @click="store.prevPage()" :disabled="page === 1 || loading">‹</button>
                    </li>
                    <!-- Páginas con elipsis -->
                    <template v-for="p in visiblePages" :key="p">
                        <li v-if="p === '…'" class="page-item disabled">
                            <span class="page-link">…</span>
                        </li>
                        <li v-else class="page-item" :class="{ active: p === page }">
                            <button class="page-link" :disabled="loading" @click="store.goToPage(p)">{{ p }}</button>
                        </li>
                    </template>
                    <!-- Siguiente -->
                    <li class="page-item" :class="{ disabled: page === totalPages || loading }">
                        <button class="page-link" title="Página siguiente" @click="store.nextPage()" :disabled="page === totalPages || loading">›</button>
                    </li>
                    <!-- Última -->
                    <li class="page-item" :class="{ disabled: page === totalPages || loading }">
                        <button class="page-link" title="Última página" @click="store.lastPage()" :disabled="page === totalPages || loading">»</button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>
