<script setup>
import { ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { usePostsStore } from '@/stores/postsStore';

const store = usePostsStore();
const { search, filterType, pageSize, postTypes } = storeToRefs(store);

const localSearch = ref(search.value);
let debounceTimer = null;

const onSearch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        store.setSearch(localSearch.value);
    }, 400);
};

const onTypeChange = (e) => store.setFilter(e.target.value);
const onPageSizeChange = (e) => store.setPageSize(e.target.value);

watch(search, (val) => {
    localSearch.value = val;
});
</script>

<template>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <!-- Buscar -->
        <div class="input-group input-group-sm" style="min-width: 200px; max-width: 280px;">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
            </div>
            <input
                v-model="localSearch"
                type="text"
                class="form-control"
                placeholder="Buscar publicación..."
                @input="onSearch"
            >
            <div v-if="localSearch" class="input-group-append">
                <button
                    class="btn btn-outline-secondary"
                    type="button"
                    title="Limpiar búsqueda"
                    @click="localSearch = ''; store.setSearch('')"
                >
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>

        <!-- Filtro tipo -->
        <select
            class="form-control form-control-sm"
            style="max-width: 160px;"
            :value="filterType"
            @change="onTypeChange"
        >
            <option value="">Todos los tipos</option>
            <option
                v-for="pt in postTypes"
                :key="pt.id"
                :value="pt.id"
            >
                {{ pt.description }}
            </option>
        </select>

        <!-- Resultados por página -->
        <select
            class="form-control form-control-sm"
            style="max-width: 110px;"
            :value="pageSize"
            @change="onPageSizeChange"
        >
            <option value="10">10 / pág.</option>
            <option value="25">25 / pág.</option>
            <option value="50">50 / pág.</option>
        </select>
    </div>
</template>
