<script setup>
import { computed } from 'vue';

const props = defineProps({
    record: {
        type: Object,
        required: true,
    },
});

const canView = computed(() => Boolean(props.record?.Permissions?.view_app ?? props.record?.Permissions?.view_control));
const canAdd  = computed(() => Boolean(props.record?.Permissions?.add_app ?? props.record?.Permissions?.add_control));
</script>

<template>
    <div v-if="canView || canAdd" class="btn-group">
        <!-- Ver controles del paciente -->
        <a
            v-if="canView"
            :href="record.see_url"
            class="btn btn-info btn-xs"
            title="Ver controles del paciente"
        >
            <i class="bi bi-eye"></i> Ver
        </a>
        <!-- Nuevo control para el paciente -->
        <a
            v-if="canAdd"
            :href="record.add_url"
            class="btn btn-success btn-xs"
            title="Registrar nuevo control"
        >
            <i class="bi bi-plus-circle"></i> Nuevo
        </a>
    </div>
    <span v-else class="text-muted small">Sin acciones</span>
</template>
