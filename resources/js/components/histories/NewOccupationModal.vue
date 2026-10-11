<script setup>
import { ref } from 'vue';
import { useHistoryFormStore } from '@/stores/historyFormStore';

const emit          = defineEmits(['close', 'created']);
const store         = useHistoryFormStore();

const descripcion   = ref('');
const loading       = ref(false);
const error         = ref('');

const submit        = async () => {
    if (!descripcion.value.trim()) { error.value = 'Descripción requerida.'; return; }
    loading.value = true;
    error.value = '';
    try {
        const data = await store.createOccupation(descripcion.value.trim());
        if (data.status) {
            emit('created', data.item || { ocupacion: descripcion.value.toUpperCase() });
            emit('close');
        } else {
            error.value = data.messages || 'No se pudo guardar.';
        }
    } catch (e) {
        error.value = e.response?.data?.errors?.descripcion?.[0]
            || e.response?.data?.messages
            || 'Error al conectar.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="modal fade show d-block" style="background: rgba(0,0,0,.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-briefcase text-primary mr-1"></i> Registrar Nueva Ocupación
                    </h5>
                    <button type="button" class="close" @click="emit('close')"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Descripción <span class="text-danger">*</span></label>
                        <input v-model="descripcion" type="text" class="form-control text-uppercase"
                               placeholder="Nombre de la ocupación" maxlength="100">
                        <div v-if="error" class="text-danger small mt-1">{{ error }}</div>
                        <small class="form-text text-muted">La nueva ocupación se seleccionará automáticamente.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light justify-content-between">
                    <button class="btn btn-outline-secondary" @click="emit('close')">Cancelar</button>
                    <button class="btn btn-primary" @click="submit" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm mr-1"></span>
                        <i v-else class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>