<script setup>
import { ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    label:      { type: String, default: '' },
    height:     { type: Number, default: 120 },
    placeholder:{ type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

const editor = ref(null);
let initialized = false;

const init = () => {
    if (!editor.value || !window.$?.fn?.summernote) return;

    window.$(editor.value).summernote({
        height: props.height,
        placeholder: props.placeholder,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para',  ['ul', 'ol', 'paragraph']],
            ['insert',['link', 'table']],
            ['view',  ['codeview']],
        ],
        callbacks: {
            onChange: (contents) => {
                // Evita emitir en la carga inicial
                if (!initialized) return;
                emit('update:modelValue', contents);
            },
        },
    });

    // Setea el valor inicial
    window.$(editor.value).summernote('code', props.modelValue || '');
    initialized = true;
};

onMounted(() => nextTick(init));

onBeforeUnmount(() => {
    if (editor.value && window.$?.fn?.summernote) {
        window.$(editor.value).summernote('destroy');
    }
});

// Sincroniza cambios externos (ej. hydrate del store)
watch(() => props.modelValue, (newVal) => {
    if (!initialized || !editor.value) return;
    const current = window.$(editor.value).summernote('code');
    if (current !== (newVal || '')) {
        window.$(editor.value).summernote('code', newVal || '');
    }
});
</script>

<template>
    <div class="form-group mb-2">
        <label v-if="label" class="font-weight-bold col-form-label">{{ label }}</label>
        <div ref="editor"></div>
    </div>
</template>