<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { useSpecialtiesStore } from '@/stores/specialtiesStore';

const store = useSpecialtiesStore();
const {
    modalVisible,
    editingSpecialty,
    submitting,
    formErrors,
    occupations,
} = storeToRefs(store);

const form = ref({
    id: null,
    id_ocupacion: '',
    descripcion: '',
    detalle: '',
});

const isEditing = computed(() => Boolean(form.value.id));

watch([modalVisible, editingSpecialty], ([visible, item]) => {
    if (visible) {
        document.body.classList.add('modal-open');
        if (item) {
            form.value = {
                id: item.id || null,
                id_ocupacion: item.id_ocupacion ?? '',
                descripcion: item.descripcion || '',
                detalle: item.detalle || '',
            };
        } else {
            form.value = {
                id: null,
                id_ocupacion: '',
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
    await store.saveSpecialty(form.value);
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
                            {{ isEditing ? 'Actualizar Especialidad' : 'Agregar Nueva Especialidad' }}
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
                            <!-- Cargo / Ocupación -->
                            <div class="form-group">
                                <label for="specialty_occupation" class="font-weight-semibold">
                                    Cargo / Ocupación <span class="text-danger">*</span>
                                </label>
                                <select
                                    id="specialty_occupation"
                                    v-model="form.id_ocupacion"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.id_ocupacion }"
                                    required
                                    :disabled="submitting"
                                >
                                    <option value="">-- Seleccione un cargo --</option>
                                    <option
                                        v-for="occ in occupations"
                                        :key="occ.id"
                                        :value="occ.id"
                                    >
                                        {{ occ.descripcion }}
                                    </option>
                                </select>
                                <div v-if="formErrors.id_ocupacion" class="invalid-feedback d-block">
                                    {{ formErrors.id_ocupacion[0] }}
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="form-group">
                                <label for="specialty_desc" class="font-weight-semibold">
                                    Descripción <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="specialty_desc"
                                    v-model="form.descripcion"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.descripcion }"
                                    placeholder="Ej: Neumología Pediátrica"
                                    required
                                    maxlength="255"
                                    :disabled="submitting"
                                >
                                <div v-if="formErrors.descripcion" class="invalid-feedback d-block">
                                    {{ formErrors.descripcion[0] }}
                                </div>
                            </div>

                            <!-- Detalle -->
                            <div class="form-group mb-0">
                                <label for="specialty_detail" class="font-weight-semibold">
                                    Detalle <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="specialty_detail"
                                    v-model="form.detalle"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.detalle }"
                                    placeholder="Ej: Atención de enfermedades respiratorias infantiles"
                                    required
                                    maxlength="255"
                                    :disabled="submitting"
                                >
                                <div v-if="formErrors.detalle" class="invalid-feedback d-block">
                                    {{ formErrors.detalle[0] }}
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                :disabled="submitting"
                                @click="store.closeModal()"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="submitting || !form.id_ocupacion || !form.descripcion || !form.detalle"
                            >
                                <span v-if="submitting" class="spinner-border spinner-border-sm mr-1"></span>
                                <i v-else class="bi" :class="isEditing ? 'bi-check-circle mr-1' : 'bi-plus-circle mr-1'"></i>
                                {{ isEditing ? 'Guardar Cambios' : 'Registrar Especialidad' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.font-weight-semibold {
    font-weight: 600;
}
.bg-warning-light {
    background-color: #ffeeba;
}
</style>
