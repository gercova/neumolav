<script setup>
import { ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    label:      { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

const editorRef = ref(null);
let editorInstance = null;
let suppressWatch = false;

onMounted(async () => {
    await nextTick();
    if (!editorRef.value || !window.ClassicEditor) {
        console.warn('CKEditor 5 (ClassicEditor) no está disponible.');
        return;
    }

    try {
        editorInstance = await window.ClassicEditor.create(editorRef.value);

        // Valor inicial
        editorInstance.setData(props.modelValue || '');

        // Emitir cambios hacia el padre
        editorInstance.model.document.on('change:data', () => {
            if (suppressWatch) return;
            emit('update:modelValue', editorInstance.getData());
        });
    } catch (error) {
        console.error('Error al inicializar CKEditor:', error);
    }
});

// Sincronizar cambios externos (ej. hydrate del store)
watch(() => props.modelValue, (newVal) => {
    if (!editorInstance) return;
    const current = editorInstance.getData();
    if (current === (newVal || '')) return;
    suppressWatch = true;
    editorInstance.setData(newVal || '');
    suppressWatch = false;
});

onBeforeUnmount(() => {
    if (editorInstance) {
        editorInstance.destroy().catch(() => {});
        editorInstance = null;
    }
});
</script>

<template>
    <div class="form-group mb-2">
        <label v-if="label" class="col-form-label font-weight-bold">{{ label }}</label>
        <div ref="editorRef"></div>
    </div>
</template>