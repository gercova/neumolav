<script setup>
import { computed } from 'vue';

const props = defineProps({
    record: {
        type: Object,
        required: true,
    },
});

const canView = computed(() => Boolean(
    props.record?.Permissions?.view_risk ??
    props.record?.Permissions?.view_riesgo ??
    props.record?.Permissions?.riesgo_ver
));

const canAdd = computed(() => Boolean(
    props.record?.Permissions?.add_risk ??
    props.record?.Permissions?.add_riesgo ??
    props.record?.Permissions?.riesgo_crear
));
</script>

<template>
    <div v-if="canView || canAdd" class="btn-group">
        <!-- Ver informes de riesgo del paciente -->
        <a
            v-if="canView"
            :href="record.see_url"
            class="btn btn-info btn-xs"
            title="Ver informes de riesgo del paciente"
        >
            <i class="bi bi-eye"></i> Ver
        </a>
        <!-- Nuevo informe de riesgo para el paciente -->
        <a
            v-if="canAdd"
            :href="record.add_url"
            class="btn btn-success btn-xs"
            title="Registrar nuevo informe de riesgo"
        >
            <i class="bi bi-plus-circle"></i> Nuevo
        </a>
    </div>
    <span v-else class="text-muted small">Sin acciones</span>
</template>
