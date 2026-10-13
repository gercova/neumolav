<script setup>
import { computed } from 'vue';

const props = defineProps({
    record: {
        type: Object,
        required: true,
    },
});

const canView = computed(() => Boolean(
    props.record?.Permissions?.view_report ??
    props.record?.Permissions?.view_informe ??
    props.record?.Permissions?.informe_ver
));

const canAdd = computed(() => Boolean(
    props.record?.Permissions?.add_report ??
    props.record?.Permissions?.add_informe ??
    props.record?.Permissions?.informe_crear
));
</script>

<template>
    <div v-if="canView || canAdd" class="btn-group">
        <!-- Ver informes del paciente -->
        <a
            v-if="canView"
            :href="record.see_url"
            class="btn btn-info btn-xs"
            title="Ver informes del paciente"
        >
            <i class="bi bi-eye"></i> Ver
        </a>
        <!-- Nuevo informe para el paciente -->
        <a
            v-if="canAdd"
            :href="record.add_url"
            class="btn btn-success btn-xs"
            title="Registrar nuevo informe"
        >
            <i class="bi bi-plus-circle"></i> Nuevo
        </a>
    </div>
    <span v-else class="text-muted small">Sin acciones</span>
</template>
