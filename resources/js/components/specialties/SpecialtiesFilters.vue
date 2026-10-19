<script setup>
import { ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useSpecialtiesStore } from '@/stores/specialtiesStore';

const store = useSpecialtiesStore();
const { occupations, occupationFilter } = storeToRefs(store);

const term = ref(store.search);
let timer = null;

watch(term, (v) => {
    clearTimeout(timer);
    timer = setTimeout(() => store.setSearch(v.trim()), 350);
});

watch(() => store.search, (v) => {
    if (v !== term.value) {
        term.value = v;
    }
});

const onOccupationChange = (e) => {
    store.setOccupationFilter(e.target.value);
};

const clearFilters = () => {
    term.value = '';
    store.occupationFilter = '';
    store.fetchPage(1);
};
</script>

<template>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <!-- Selector de Cargo / Ocupación -->
        <div class="filter-select">
            <select
                class="form-control form-control-sm"
                :value="occupationFilter"
                @change="onOccupationChange"
            >
                <option value="">Todos los cargos</option>
                <option
                    v-for="occ in occupations"
                    :key="occ.id"
                    :value="occ.id"
                >
                    {{ occ.descripcion }}
                </option>
            </select>
        </div>

        <!-- Barra de Búsqueda -->
        <div class="input-group input-group-sm search-input-group">
            <div class="input-group-prepend">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search"></i>
                </span>
            </div>
            <input
                v-model="term"
                type="text"
                class="form-control"
                placeholder="Buscar descripción, detalle..."
            >
            <div v-if="term || occupationFilter" class="input-group-append">
                <button
                    class="btn btn-outline-secondary"
                    type="button"
                    title="Limpiar filtros"
                    @click="clearFilters"
                >
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.gap-2 {
    gap: 0.5rem;
}
.filter-select {
    min-width: 170px;
}
.search-input-group {
    min-width: 260px;
    max-width: 380px;
}
</style>
