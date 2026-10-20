<script setup>
import { ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useUsersStore } from '@/stores/usersStore';

const store     = useUsersStore();
const { roles, roleFilter } = storeToRefs(store);

const term = ref(store.search);
let timer  = null;

watch(term, (v) => {
    clearTimeout(timer);
    timer = setTimeout(() => store.setSearch(v.trim()), 350);
});

watch(() => store.search, (v) => {
    if (v !== term.value) {
        term.value = v;
    }
});

const onRoleChange = (e) => {
    store.setRoleFilter(e.target.value);
};

const clearFilters = () => {
    term.value = '';
    store.roleFilter = '';
    store.fetchPage(1);
};
</script>
<template>
    <div class="d-flex align-items-center gap-2">
        <!-- Selector de Rol -->
        <div class="filter-select">
            <select
                class="form-control form-control-sm"
                :value="roleFilter"
                @change="onRoleChange"
            >
                <option value="">Todos los roles</option>
                <option
                    v-for="role in roles"
                    :key="role.id"
                    :value="role.name"
                >
                    {{ role.name }}
                </option>
            </select>
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
                placeholder="Buscar nombre, email, usuario..."
            >
            <div v-if="term || roleFilter" class="input-group-append">
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
    min-width: 150px;
}
.search-input-group {
    min-width: 260px;
    max-width: 380px;
}
</style>
