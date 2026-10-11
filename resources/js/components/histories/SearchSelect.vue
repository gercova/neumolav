<script setup>
import { ref, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';

const props = defineProps({
    modelValue:  { type: [String, Number], default: '' },
    placeholder: { type: String, default: '-- Buscar --' },
    searchFn:    { type: Function, required: true },  // async (query) => [{ value, label }]
    minChars:    { type: Number, default: 2 },
    initialLabel:{ type: String, default: '' },       // para edit: mostrar valor precargado
    disabled:    { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue', 'selected']);

const query     = ref('');
const results   = ref([]);
const loading   = ref(false);
const open      = ref(false);
const displayLabel = ref(props.initialLabel);
const rootRef   = ref(null);
let timer = null;

watch(() => props.initialLabel, (v) => { if (v) displayLabel.value = v; });
watch(() => props.modelValue, (v) => { if (!v) displayLabel.value = ''; });

const runSearch = async () => {
    if (query.value.length < props.minChars) { results.value = []; return; }
    loading.value = true;
    try {
        results.value = await props.searchFn(query.value);
        open.value = true;
    } finally {
        loading.value = false;
    }
};

watch(query, () => {
    clearTimeout(timer);
    timer = setTimeout(runSearch, 300);
});

const select = (item) => {
    emit('update:modelValue', item.value);
    emit('selected', item);
    displayLabel.value  = item.label;
    query.value         = '';
    results.value       = [];
    open.value          = false;
};

const clear = () => {
    emit('update:modelValue', '');
    displayLabel.value = '';
    query.value = '';
};

const onClickOutside = (e) => {
    if (rootRef.value && !rootRef.value.contains(e.target)) open.value = false;
};

onMounted(() => document.addEventListener('click', onClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside));
</script>

<template>
    <div ref="rootRef" class="position-relative">
        <!-- Display del valor seleccionado -->
        <div v-if="modelValue && !query" class="form-control d-flex justify-content-between align-items-center"
             :class="{ 'bg-light': disabled }" @click="!disabled && (open = true)">
            <span class="text-truncate">{{ displayLabel || modelValue }}</span>
            <button v-if="!disabled" type="button" class="btn btn-sm btn-link p-0 text-muted" @click.stop="clear">
                <i class="bi bi-x-circle"></i>
            </button>
        </div>

        <!-- Input de búsqueda -->
        <input
            v-else
            v-model="query"
            type="text"
            class="form-control"
            :placeholder="placeholder"
            :disabled="disabled"
            @focus="open = true"
        >

        <span v-if="loading" class="position-absolute" style="right: 10px; top: 8px;">
            <span class="spinner-border spinner-border-sm text-primary"></span>
        </span>

        <!-- Dropdown -->
        <div v-if="open && results.length" class="dropdown-menu show w-100 shadow"
             style="max-height: 260px; overflow-y: auto;">
            <button
                v-for="(item, i) in results"
                :key="i"
                type="button"
                class="dropdown-item small"
                @click="select(item)"
            >
                {{ item.label }}
            </button>
        </div>

        <div v-else-if="open && query.length >= minChars && !loading"
             class="dropdown-menu show w-100 text-muted small p-2">
            Sin resultados
        </div>
    </div>
</template>