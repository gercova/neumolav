<script setup>
const props = defineProps({ modelValue: { type: Number, default: 1 } });
const emit  = defineEmits(['update:modelValue']);

const tipos = [
    { id: 1, label: 'Nuevo',       icon: 'bi-person-plus-fill',  cls: 'btn-outline-success' },
    { id: 2, label: 'Control',     icon: 'bi-arrow-repeat',      cls: 'btn-outline-primary' },
    { id: 3, label: 'Continuador', icon: 'bi-person-check-fill', cls: 'btn-outline-warning text-dark' },
];
</script>

<template>
    <div class="form-group">
        <label class="d-block font-weight-bold">Tipo de Atención <span class="text-danger">*</span></label>
        <div class="btn-group btn-group-toggle w-100 chip-tipo-group">
            <label
                v-for="t in tipos"
                :key="t.id"
                class="btn chip-tipo-btn flex-fill"
                :class="[t.cls, modelValue === t.id ? 'active' : '']"
                @click.prevent="emit('update:modelValue', t.id)"
            >
                <i class="bi" :class="t.icon"></i> <b>{{ t.label }}</b>
            </label>
        </div>
    </div>
</template>

<style scoped>
.chip-tipo-group { display: flex; gap: 8px; }
.chip-tipo-group .btn {
    border-radius: 20px !important;
    padding: 6px 14px;
    font-size: 0.88rem;
    transition: all 0.15s ease;
}
.chip-tipo-group .btn:hover { transform: translateY(-1px); }
.chip-tipo-group .btn.active { box-shadow: 0 2px 5px rgba(0,0,0,.15); font-weight: 700; }
</style>