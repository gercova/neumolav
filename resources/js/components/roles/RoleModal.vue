<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { useRolesStore } from '@/stores/rolesStore';

const store = useRolesStore();
const { roleModalVisible, editingRole, submitting, roleFormErrors } = storeToRefs(store);

const form = ref({
    id: null,
    name: '',
    guard_name: 'web',
});

const isEditing = computed(() => Boolean(form.value.id));

watch([roleModalVisible, editingRole], ([visible, item]) => {
    if (visible) {
        document.body.classList.add('modal-open');
        if (item) {
            form.value = {
                id: item.id || null,
                name: item.name || '',
                guard_name: item.guard_name || 'web',
            };
        } else {
            form.value = {
                id: null,
                name: '',
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
        store.closeRoleModal();
    }
};

const onKeydown = (e) => {
    if (e.key === 'Escape' && roleModalVisible.value && !submitting.value) {
        store.closeRoleModal();
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
    await store.saveRole(form.value);
};
</script>

<template>
    <Teleport to="body">
        <div
            v-if="roleModalVisible"
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
                            {{ isEditing ? 'Editar Rol' : 'Agregar Rol' }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            :class="{ 'text-white': !isEditing }"
                            aria-label="Cerrar"
                            :disabled="submitting"
                            @click="store.closeRoleModal()"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="handleSubmit">
                        <div class="modal-body p-4">
                            <!-- Nombre del rol -->
                            <div class="form-group">
                                <label for="role_name" class="font-weight-bold text-dark">
                                    Nombre del rol: <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="role_name"
                                    v-model.trim="form.name"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': roleFormErrors.name }"
                                    placeholder="ej. editor, supervisor, enfermero..."
                                    maxlength="125"
                                    required
                                    :disabled="submitting"
                                    autofocus
                                >
                                <div v-if="roleFormErrors.name" class="invalid-feedback d-block">
                                    {{ roleFormErrors.name[0] }}
                                </div>
                            </div>

                            <!-- Guard Name -->
                            <div class="form-group mb-0">
                                <label for="role_guard_name" class="font-weight-bold text-dark">
                                    Guard Name: <span class="text-danger">*</span>
                                </label>
                                <input
                                    id="role_guard_name"
                                    v-model.trim="form.guard_name"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': roleFormErrors.guard_name }"
                                    placeholder="web"
                                    maxlength="125"
                                    required
                                    :disabled="submitting"
                                >
                                <div v-if="roleFormErrors.guard_name" class="invalid-feedback d-block">
                                    {{ roleFormErrors.guard_name[0] }}
                                </div>
                                <small class="form-text text-muted">Usualmente <code>web</code> o <code>api</code>.</small>
                            </div>
                        </div>

                        <div class="modal-footer bg-light justify-content-between">
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                :disabled="submitting"
                                @click="store.closeRoleModal()"
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
                                <i v-else class="bi bi-save mr-1"></i>
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
