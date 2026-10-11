<script setup>
import { ref, watch } from 'vue';
import { useHistoryFormStore } from '@/stores/historyFormStore';

const props     = defineProps({
    modelValue:    { type: String, default: '' },
    tipoDocumento: { type: [String, Number], default: '' },
    label:         { type: String, default: 'Documento / DNI' },
    maxlength:     { type: Number, default: 8 },
});
const emit      = defineEmits(['update:modelValue', 'name-fetched']);

const store     = useHistoryFormStore();
const loading   = ref(false);
const feedback  = ref('');

watch(() => props.modelValue, (v) => {
    if (props.tipoDocumento == 1) {
        const clean = String(v || '').replace(/\D/g, '').slice(0, 8);
        if (clean !== v) emit('update:modelValue', clean);
    }
});

const canSearch = () => props.tipoDocumento == 1 && props.modelValue.length === 8;

const search = async () => {
    if (!canSearch()) return;
    loading.value = true;
    feedback.value = '';
    try {
        const data = await store.searchDni(props.modelValue);
        if (data?.first_name || data?.nombres) {
            const fullName = [
                data.first_name || data.nombres || '',
                data.first_last_name || data.apellido_paterno || '',
                data.second_last_name || data.apellido_materno || '',
            ].join(' ').trim().toUpperCase();
            emit('name-fetched', fullName);
            window.alertNotify?.('success', 'Datos obtenidos de RENIEC');
        } else {
            feedback.value = 'No encontrado en RENIEC. Ingrese manualmente.';
        }
    } catch {
        feedback.value = 'Servicio RENIEC no disponible.';
    } finally {
        loading.value = false;
    }
};

const onEnter = (e) => { if (e.key === 'Enter') { e.preventDefault(); search(); } };
</script>

<template>
    <div class="form-group">
        <label class="font-weight-bold">
            {{ label }} <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input
                :value="modelValue"
                @input="emit('update:modelValue', $event.target.value)"
                @keypress="onEnter"
                type="text"
                class="form-control"
                :placeholder="tipoDocumento == 1 ? '8 dígitos' : 'Número de documento'"
                :maxlength="maxlength"
                autocomplete="off"
            >
            <div class="input-group-append" v-if="tipoDocumento == 1">
                <button type="button" class="btn btn-outline-primary" @click="search" :disabled="!canSearch() || loading">
                    <span v-if="loading" class="spinner-border spinner-border-sm"></span>
                    <i v-else class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <small v-if="feedback" class="text-danger">{{ feedback }}</small>
    </div>
</template>