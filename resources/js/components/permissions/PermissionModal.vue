<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { usePermissionsStore } from '@/stores/permissionsStore';

const store = usePermissionsStore();
const { modalVisible, editingPermission, submitting, formErrors } = storeToRefs(store);

const form = ref({
    id: null,
    name: '',
    description: '',
    guard_name: 'web',
});

const isEditing = computed(() => Boolean(form.value.id));

watch([modalVisible, editingPermission], ([visible, item]) => {
    if (visible) {
        document.body.classList.add('modal-open');
        if (item) {
            form.value = {
                id: item.id || null,
                name: item.name || '',
                description: item.description || '',
                guard_name: item.guard_name || 'web',
            };
        } else {
            form.value = {
                id: null,
                name: '',
                description: '',
                guard_name: 'web',
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
    await store.savePermission(form.value);
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
                            {{ isEditing ? 'Actualizar Permiso' : 'Agregar Nuevo Permiso' }}
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
                            <!-- Nombre -->
                            <div class="form-group">
                                <label for="permission_name" class="font-weight-bold text-dark">
                                    Nombre del Permiso: <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="permission_name"
                                    v-model.trim="form.name"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.name }"
                                    placeholder="Ej. seguridad_ver, citas_crear, farmaco_acceder..."
                                    maxlength="191"
                                    required
                                    :disabled="submitting"
                                    autofocus
                                >
                                <div v-if="formErrors.name" class="invalid-feedback d-block">
                                    {{ formErrors.name[0] }}
                                </div>
                                <small class="form-text text-muted">Identificador único utilizado por el sistema de control de accesos (Spatie Permission).</small>
                            </div>

                            <!-- Descripción -->
                            <div class="form-group">
                                <label for="permission_description" class="font-weight-bold text-dark">
                                    Descripción:
                                </label>
                                <input
                                    id="permission_description"
                                    v-model.trim="form.description"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.description }"
                                    placeholder="Ej. Permite ver el listado del módulo de citas..."
                                    maxlength="255"
                                    :disabled="submitting"
                                >
                                <div v-if="formErrors.description" class="invalid-feedback d-block">
                                    {{ formErrors.description[0] }}
                                </div>
                                <small class="form-text text-muted">Explicación breve del propósito o alcance de este permiso (opcional).</small>
                            </div>

                            <!-- Guard Name -->
                            <div class="form-group mb-0">
                                <label for="permission_guard_name" class="font-weight-bold text-dark">
                                    Nombre de Guardia (Guard Name): <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="permission_guard_name"
                                    v-model.trim="form.guard_name"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': formErrors.guard_name }"
                                    placeholder="web"
                                    maxlength="191"
                                    required
                                    :disabled="submitting"
                                >
                                <div v-if="formErrors.guard_name" class="invalid-feedback d-block">
                                    {{ formErrors.guard_name[0] }}
                                </div>
                                <small class="form-text text-muted">Por defecto 'web' para autenticación estándar de sesión.</small>
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
