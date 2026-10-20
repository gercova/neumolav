<script setup>
import { ref, watch } from 'vue';
import { useRolesStore } from '@/stores/rolesStore';

const store = useRolesStore();
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
</script>

<template>
    <div class="input-group" style="max-width: 380px;">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="bi bi-search"></i>
            </span>
        </div>
        <input
            v-model="term"
            type="text"
            class="form-control"
            placeholder="Buscar por rol o guard..."
        >
        <div v-if="term" class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" title="Limpiar búsqueda" @click="term = ''">
                <i class="bi bi-x"></i>
            </button>
        </div>
    </div>
</template>
