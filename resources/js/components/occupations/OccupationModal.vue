<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { useOccupationsStore } from '@/stores/occupationsStore';

const store = useOccupationsStore();
const { modalVisible, editingOccupation, submitting, formErrors } = storeToRefs(store);

const form = ref({
    id: null,
    descripcion: '',
    estado: true,
});

const isEditing = computed(() => Boolean(form.value.id));

watch([modalVisible, editingOccupation], ([visible, item]) => {
    if (visible) {
        document.body.classList.add('modal-open');
        if (item) {
            form.value = {
                id: item.id || null,
                descripcion: item.descripcion || '',
                estado: item.estado !== undefined ? Boolean(item.estado) : true,
            };
        } else {
            form.value = {
                id: null,
                descripcion: '',
                estado: true,
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
    await store.saveOccupation(form.value);
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
                            {{ isEditing ? 'Actualizar Ocupación' : 'Agregar Nueva Ocupación' }}
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

                    <form @submit.prevent="handleSubmit">
                        <div class="modal-body p-4">
                            <!-- Descripción -->
                            <div class="form-group">
                                <label for="occupation_descripcion" class="font-weight-bold text-dark">
                                    Descripción / Nombre: <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="occupation_descripcion"
                                    v-model.trim="form.descripcion"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.descripcion }"
                                    placeholder="Ej. MÉDICO CIRUJANO, CONDUCTOR, INGENIERO..."
                                    maxlength="100"
                                    required
                                    :disabled="submitting"
                                    autofocus
                                >
                                <div v-if="formErrors.descripcion" class="invalid-feedback d-block">
                                    {{ formErrors.descripcion[0] }}
                                </div>
                                <small class="form-text text-muted">Nombre o título de la ocupación profesional u oficio (máx. 100 caracteres).</small>
                            </div>

                            <!-- Estado -->
                            <div class="form-group mb-0">
                                <div class="custom-control custom-switch">
                                    <input
                                        id="occupation_estado"
                                        v-model="form.estado"
                                        type="checkbox"
                                        class="custom-control-input"
                                        :disabled="submitting"
                                    >
                                    <label class="custom-control-label font-weight-bold text-dark" for="occupation_estado">
                                        Estado:
                                        <span :class="form.estado ? 'text-success' : 'text-muted'">
                                            {{ form.estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </label>
                                </div>
                                <small class="form-text text-muted">Indica si esta ocupación estará disponible para selección en las historias clínicas.</small>
                            </div>
                        </div>

                        <div class="modal-footer bg-light justify-content-between">
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                :disabled="submitting"
                                @click="store.closeModal()"
                            >
                                <i class="bi bi-x-circle mr-1"></i> Cerrar
                            </button>

                            <button
                                type="submit"
                                class="btn"
                                :class="isEditing ? 'btn-warning font-weight-bold' : 'btn-primary font-weight-bold'"
                                :disabled="submitting"
                            >
                                <span v-if="submitting" class="spinner-border spinner-border-sm mr-1"></span>
                                <i v-else class="bi bi-check2-circle mr-1"></i>
                                {{ submitting ? 'Guardando...' : (isEditing ? 'Actualizar' : 'Guardar') }}
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
</style>
