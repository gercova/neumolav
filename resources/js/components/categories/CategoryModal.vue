<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { useCategoriesStore } from '@/stores/categoriesStore';

const store = useCategoriesStore();
const { modalVisible, editingCategory, submitting, formErrors } = storeToRefs(store);

const form = ref({
    id: null,
    descripcion: '',
    detalle: '',
});

const isEditing = computed(() => Boolean(form.value.id));

// Sincronizar formulario al abrir modal o cambiar categoría seleccionada
watch([modalVisible, editingCategory], ([visible, cat]) => {
    if (visible) {
        document.body.classList.add('modal-open');
        if (cat) {
            form.value = {
                id: cat.id || null,
                descripcion: cat.descripcion || '',
                detalle: cat.detalle || '',
            };
        } else {
            form.value = {
                id: null,
                descripcion: '',
                detalle: '',
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
    await store.saveCategory(form.value);
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
                            {{ isEditing ? 'Actualizar Categoría' : 'Agregar Categoría' }}
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
                            <div class="form-group">
                                <label for="category_descripcion" class="font-weight-bold text-dark">
                                    Categoría: <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="category_descripcion"
                                    v-model.trim="form.descripcion"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.descripcion }"
                                    placeholder="Ej. Broncodilatadores, Corticoides, Antibióticos..."
                                    maxlength="50"
                                    required
                                    :disabled="submitting"
                                    autofocus
                                >
                                <div v-if="formErrors.descripcion" class="invalid-feedback d-block">
                                    {{ formErrors.descripcion[0] }}
                                </div>
                                <small class="form-text text-muted">Nombre de la categoría (máx. 50 caracteres).</small>
                            </div>

                            <div class="form-group mb-0">
                                <label for="category_detalle" class="font-weight-bold text-dark">
                                    Detalle: <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    id="category_detalle"
                                    v-model.trim="form.detalle"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.detalle }"
                                    rows="3"
                                    placeholder="Descripción o notas sobre el uso o alcance de esta categoría..."
                                    maxlength="255"
                                    required
                                    :disabled="submitting"
                                ></textarea>
                                <div v-if="formErrors.detalle" class="invalid-feedback d-block">
                                    {{ formErrors.detalle[0] }}
                                </div>
                                <small class="form-text text-muted">Detalles adicionales del grupo farmacológico (máx. 255 caracteres).</small>
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
