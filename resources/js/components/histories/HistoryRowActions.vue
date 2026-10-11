<script setup>
import { ref, computed } from 'vue';
import { useHistoriesStore } from '@/stores/historiesStore';
import AddToQueueModal from './AddToQueueModal.vue';

const props = defineProps({
    record: Object,
    editUrl: String,
});

const store = useHistoriesStore();
const showQueueModal = ref(false);
const perms = computed(() => props.record.Permissions || {});

const confirmDelete = async () => {
    const r = await window.Swal.fire({
        title: '¿Eliminar historia clínica?',
        text: 'Se eliminarán todos los datos del paciente asociados a esta historia.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, borrar',
        cancelButtonText: 'Cancelar',
    });
    if (!r.isConfirmed) return;
    try {
        await store.destroy(props.record.id);
        window.alertNotify?.('success', 'Historia eliminada');
    } catch (e) {
        window.Swal?.fire('Error', 'No se pudo eliminar la historia.', 'error');
    }
};
</script>

<template>
    <div class="btn-group">
        <button
            type="button"
            class="btn btn-info btn-xs"
            @click="showQueueModal = true"
            title="Añadir a la cola de citas de hoy"
        >
            <i class="bi bi-file-earmark-plus"></i> Añadir
        </button>

        <a
            v-if="perms.update"
            :href="`${editUrl}/${record.id}`"
            class="btn btn-warning btn-xs"
            title="Editar historia"
        >
            <i class="bi bi-pencil-square"></i> Editar
        </a>

        <button
            v-if="perms.delete"
            type="button"
            class="btn btn-danger btn-xs"
            @click="confirmDelete"
            title="Eliminar historia"
        >
            <i class="bi bi-trash"></i> Eliminar
        </button>
    </div>

    <AddToQueueModal
        v-if="showQueueModal"
        :history="record"
        @close="showQueueModal = false"
    />
</template>