<script setup>
import { ref, watch } from 'vue';
import { useReportsStore } from '@/stores/reportsStore';

const store = useReportsStore();
const term = ref(store.search);
let timer = null;

watch(term, (v) => {
    clearTimeout(timer);
    timer = setTimeout(() => store.setSearch(v.trim()), 350);
});
</script>

<template>
    <div class="input-group" style="max-width: 360px;">
        <div class="input-group-prepend">
            <span class="input-group-text bg-white">
                <i class="bi bi-search"></i>
            </span>
        </div>
        <input
            v-model="term"
            type="text"
            class="form-control"
            placeholder="Buscar por DNI, nombres o apellidos..."
        >
        <div v-if="term" class="input-group-append">
            <button class="btn btn-outline-secondary" @click="term = ''">
                <i class="bi bi-x"></i>
            </button>
        </div>
    </div>
</template>
