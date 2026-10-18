<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { useDiagnosticsStore } from '@/stores/diagnosticsStore';

const store = useDiagnosticsStore();
const { modalVisible, editingDiagnostic, submitting, formErrors } = storeToRefs(store);

const form = ref({
    id: null,
    descripcion: '',
});

const isEditing = computed(() => Boolean(form.value.id));

watch([modalVisible, editingDiagnostic], ([visible, item]) => {
    if (visible) {
        document.body.classList.add('modal-open');
        if (item) {
            form.value = {
                id: item.id || null,
                descripcion: item.descripcion || '',
            };
        } else {
            form.value = {
                id: null,
                descripcion: '',
            };
        }
    } else {
        document.body.classList.remove('modal-open');
    }
});

onBeforeUnmount(() => {
    document.body.classList.remove('modal-open');
});

const onBackdropClick = (e) => {
    if (e.target.classList.contains('modal') && !submitting.value) {
        store.closeModal();
    }
};

const onKeydown = (e) => {
    if (e.key === 'Escape' && modalVisible.value && !submitting.value) {
        store.closeModal();
    }
};

if (typeof window !== 'undefined') {
    window.addEventListener('keydown', onKeydown);
}
onBeforeUnmount(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('keydown', onKeydown);
    }
});

const handleSubmit = async () => {
    await store.saveDiagnostic(form.value);
};
</script>

<template>
    <Teleport to="body">
        <div
            v-if="modalVisible"
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background: rgba(0, 0, 0, 0.55);"
            @click="onBackdropClick"
        >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header" :class="isEditing ? 'bg-warning-light text-dark' : 'bg-primary text-white'">
                        <h5 class="modal-title font-weight-bold mb-0">
                            <i class="bi" :class="isEditing ? 'bi-pencil-square mr-1' : 'bi-plus-circle mr-1'"></i>
                            {{ isEditing ? 'Actualizar Diagnóstico' : 'Agregar Nuevo Diagnóstico' }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            :class="{ 'text-white': !isEditing }"
                            aria-label="Cerrar"
                            :disabled="submitting"
                            @click="store.closeModal()"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="diagnosticForm" @submit.prevent="handleSubmit">
                        <div class="modal-body">
                            <!-- Campo Descripción -->
                            <div class="form-group mb-0">
                                <label for="descripcion" class="font-weight-bold small text-muted text-uppercase mb-1">
                                    Descripción <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="descripcion"
                                    v-model="form.descripcion"
                                    type="text"
                                    name="descripcion"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.descripcion }"
                                    placeholder="Ej. Bronquitis aguda, Asma..."
                                    maxlength="50"
                                    required
                                    autofocus
                                    :disabled="submitting"
                                >
                                <div v-if="formErrors.descripcion" class="invalid-feedback d-block">
                                    {{ formErrors.descripcion[0] }}
                                </div>
                                <small class="form-text text-muted">
                                    Máximo 50 caracteres.
                                </small>
                            </div>
                        </div>

                        <div class="modal-footer justify-content-between bg-light">
                            <button
                                type="button"
                                class="btn btn-secondary btn-sm"
                                :disabled="submitting"
                                @click="store.closeModal()"
                            >
                                <i class="bi bi-x-circle mr-1"></i> Cancelar
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary btn-sm"
                                :disabled="submitting"
                            >
                                <template v-if="submitting">
                                    <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                                    Guardando...
                                </template>
                                <template v-else>
                                    <i class="bi bi-check-circle mr-1"></i> Guardar
                                </template>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.bg-warning-light {
    background-color: #fff3cd;
    border-bottom: 1px solid #ffeeba;
}
.modal-header .close {
    font-size: 1.5rem;
    padding: 0.75rem 1rem;
    margin: -1rem -1rem -1rem auto;
}
</style>
