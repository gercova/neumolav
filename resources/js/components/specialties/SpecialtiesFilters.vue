<script setup>
import { ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import axios from 'axios';
import { useSpecialtiesStore } from '@/stores/specialtiesStore';
import SearchSelect from '@/components/histories/SearchSelect.vue';

const store = useSpecialtiesStore();
const { specialtyFilter, specialtyLabel } = storeToRefs(store);

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

const API = () => window.API_BASE_URL || '/admin';

const searchSpecialties = async (q) => {
    try {
        const { data } = await axios.get(`${API()}/specialties/search`, {
            params: { q: q || '' },
        });
        return Array.isArray(data) ? data.slice(0, 5) : [];
    } catch (e) {
        console.error('Error searching specialties:', e);
        return [];
    }
};

const onSpecialtySelected = (item) => {
    store.setSpecialtyFilter(item.id, item.label || item.descripcion);
};

const onSpecialtyClear = () => {
    store.setSpecialtyFilter('', '');
};

const clearFilters = () => {
    term.value = '';
    store.search = '';
    store.setSpecialtyFilter('', '');
    store.fetchPage(1);
};
</script>

<template>
    <div class="d-flex align-items-center gap-2">
        <!-- Selector Select2 de Especialidad (limitado a 5 resultados) -->
        <div class="filter-select">
            <SearchSelect
                v-model="specialtyFilter"
                :initial-label="specialtyLabel"
                placeholder="Todas las especialidades"
                search-placeholder="Buscar especialidad..."
                :search-fn="searchSpecialties"
                :min-chars="0"
                :max-results="5"
                size="sm"
                @selected="onSpecialtySelected"
                @clear="onSpecialtyClear"
            />
        </div>

        <!-- Barra de Búsqueda -->
        <div class="input-group input-group-sm search-input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
            </div>
            <input
                v-model="term"
                type="text"
                class="form-control"
                placeholder="Buscar descripción, detalle..."
            >
            <div v-if="term || specialtyFilter" class="input-group-append">
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
    min-width: 220px;
    max-width: 300px;
}
.search-input-group {
    min-width: 240px;
    max-width: 360px;
}
</style>
