<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';

const props     = defineProps({
    modelValue: { type: String, default: '' },   // ISO yyyy-mm-dd
});
const emit      = defineEmits(['update:modelValue']);

const inputRef  = ref(null);
let picker      = null;

const isoToDisplay = (iso) => {
    if (!iso) return '';
    const [y, m, d] = iso.split('-');
    return `${d}-${m}-${y}`;
};
const displayToIso = (display) => {
    if (!display || display.length < 10) return '';
    const [d, m, y] = display.split('-');
    if (!d || !m || !y || y.length !== 4) return '';
    return `${y}-${m}-${d}`;
};

onMounted(() => {
    if (!window.VanillaDatePicker || !inputRef.value) return;
    inputRef.value.value = isoToDisplay(props.modelValue);

    picker = new window.VanillaDatePicker(inputRef.value, {
        maxDate: new Date(),
        minDate: '1900-01-01',
        triggerButton: '#btnDatepickerToggle',
        autoClose: true,
        onSelect: (date, displayStr) => {
            emit('update:modelValue', displayToIso(displayStr));
        },
    });
});

onBeforeUnmount(() => picker?.destroy?.());

watch(() => props.modelValue, (v) => {
    if (inputRef.value) inputRef.value.value = isoToDisplay(v);
});
</script>

<template>
    <div class="form-group">
        <label class="font-weight-bold">Fecha Nacimiento <span class="text-danger">*</span></label>
        <div class="input-group">
            <input
                ref="inputRef"
                type="text"
                class="form-control"
                placeholder="DD-MM-AAAA"
                maxlength="10"
                autocomplete="off"
                inputmode="numeric"
            >
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-secondary" id="btnDatepickerToggle">
                    <i class="far fa-calendar-alt"></i>
                </button>
            </div>
        </div>
    </div>
</template>