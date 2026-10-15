<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { useDrugsStore } from '@/stores/drugsStore';

const store = useDrugsStore();
const { modalVisible, editingDrug, submitting, formErrors, categories, presentations } = storeToRefs(store);

const form = ref({
    id: null,
    id_categoria: '',
    id_presentacion: '',
    descripcion: '',
    detalle: '',
});

const isEditing = computed(() => Boolean(form.value.id));

watch([modalVisible, editingDrug], ([visible, item]) => {
    if (visible) {
        document.body.classList.add('modal-open');
        if (item) {
            form.value = {
                id: item.id || null,
                id_categoria: item.id_categoria ?? '',
                id_presentacion: item.id_presentacion ?? '',
                descripcion: item.descripcion || '',
                detalle: item.detalle || '',
            };
        } else {
            form.value = {
                id: null,
                id_categoria: '',
                id_presentacion: '',
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
    await store.saveDrug(form.value);
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
                            {{ isEditing ? 'Actualizar Fármaco' : 'Agregar Nuevo Fármaco' }}
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
                            <!-- Categoría -->
                            <div class="form-group">
                                <label for="drug_categoria" class="font-weight-bold text-dark">
                                    Categoría: <span class="text-danger">*</span>
                                </label>
                                <select
                                    id="drug_categoria"
                                    v-model="form.id_categoria"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.id_categoria }"
                                    required
                                    :disabled="submitting"
                                >
                                    <option value="">-- Seleccione Categoría --</option>
                                    <option
                                        v-for="c in categories"
                                        :key="c.id"
                                        :value="c.id"
                                    >
                                        {{ c.descripcion }} {{ c.detalle ? ' - ' + c.detalle : '' }}
                                    </option>
                                </select>
                                <div v-if="formErrors.id_categoria" class="invalid-feedback d-block">
                                    {{ formErrors.id_categoria[0] }}
                                </div>
                            </div>

                            <!-- Presentación -->
                            <div class="form-group">
                                <label for="drug_presentacion" class="font-weight-bold text-dark">
                                    Presentación: <span class="text-danger">*</span>
                                </label>
                                <select
                                    id="drug_presentacion"
                                    v-model="form.id_presentacion"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.id_presentacion }"
                                    required
                                    :disabled="submitting"
                                >
                                    <option value="">-- Seleccione Presentación --</option>
                                    <option
                                        v-for="p in presentations"
                                        :key="p.id"
                                        :value="p.id"
                                    >
                                        {{ p.descripcion }} {{ p.aka ? ' (' + p.aka + ')' : '' }}
                                    </option>
                                </select>
                                <div v-if="formErrors.id_presentacion" class="invalid-feedback d-block">
                                    {{ formErrors.id_presentacion[0] }}
                                </div>
                            </div>

                            <!-- Descripción / Nombre del fármaco -->
                            <div class="form-group">
                                <label for="drug_descripcion" class="font-weight-bold text-dark">
                                    Descripción / Nombre: <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="drug_descripcion"
                                    v-model.trim="form.descripcion"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.descripcion }"
                                    placeholder="Ej. SALBUTAMOL, PARACETAMOL, AMOXICILINA..."
                                    maxlength="50"
                                    required
                                    :disabled="submitting"
                                >
                                <div v-if="formErrors.descripcion" class="invalid-feedback d-block">
                                    {{ formErrors.descripcion[0] }}
                                </div>
                                <small class="form-text text-muted">Nombre del principio activo o medicamento (máx. 50 caracteres).</small>
                            </div>

                            <!-- Detalle -->
                            <div class="form-group mb-0">
                                <label for="drug_detalle" class="font-weight-bold text-dark">
                                    Detalle / Concentración: <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="drug_detalle"
                                    v-model.trim="form.detalle"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.detalle }"
                                    placeholder="Ej. 100 MCG / DOSIS, 500 MG, 250 MG / 5 ML..."
                                    maxlength="255"
                                    required
                                    :disabled="submitting"
                                >
                                <div v-if="formErrors.detalle" class="invalid-feedback d-block">
                                    {{ formErrors.detalle[0] }}
                                </div>
                                <small class="form-text text-muted">Concentración, dosificación o especificación técnica (máx. 255 caracteres).</small>
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
