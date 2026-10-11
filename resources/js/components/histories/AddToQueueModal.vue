<script setup>
import { ref } from 'vue';
import { useHistoriesStore } from '@/stores/historiesStore';

const props         = defineProps({ history: Object });
const emit          = defineEmits(['close']);

const store         = useHistoriesStore();
const tipoId        = ref(2);          // Control por defecto
const submitting    = ref(false);

const tipos         = [
    { id: 1, label: 'Nuevo',       icon: 'bi-person-plus-fill',  cls: 'btn-outline-success' },
    { id: 2, label: 'Control',     icon: 'bi-stethoscope',       cls: 'btn-outline-primary' },
    { id: 3, label: 'Continuador', icon: 'bi-person-check-fill', cls: 'btn-outline-warning text-dark' },
];

const confirm = async () => {
    submitting.value = true;
    try {
        const data = await store.addToQueue(props.history.id, tipoId.value);
        if (data.status) {
            window.Swal.fire('Operación exitosa', data.messages, 'success');
            emit('close');
        } else {
            window.Swal.fire('Error', data.messages || 'No se pudo añadir.', 'error');
        }
    } catch (e) {
        window.Swal.fire('Error', 'Error de conexión.', 'error');
    } finally {
        submitting.value = false;
    }
};
</script>

<template>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-plus text-primary mr-1"></i>
                        Añadir a la cola de hoy
                    </h5>
                    <button type="button" class="close" @click="emit('close')">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-1">Paciente:</p>
                    <p class="font-weight-bold text-dark mb-3">{{ history.nombres }}</p>

                    <label class="d-block font-weight-bold mb-2">Tipo de atención:</label>
                    <div class="btn-group w-100">
                        <button
                            v-for="t in tipos"
                            :key="t.id"
                            type="button"
                            class="btn flex-fill"
                            :class="[t.cls, tipoId === t.id ? 'active' : '']"
                            @click="tipoId = t.id"
                        >
                            <i class="bi" :class="t.icon"></i> {{ t.label }}
                        </button>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button class="btn btn-outline-secondary" @click="emit('close')" :disabled="submitting">
                        Cancelar
                    </button>
                    <button class="btn btn-primary" @click="confirm" :disabled="submitting">
                        <span v-if="submitting" class="spinner-border spinner-border-sm mr-1"></span>
                        <i v-else class="bi bi-check2 mr-1"></i>
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>