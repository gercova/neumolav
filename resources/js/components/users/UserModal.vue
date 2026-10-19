<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { useUsersStore } from '@/stores/usersStore';

const store = useUsersStore();
const {
    userModalVisible,
    editingUser,
    submitting,
    userFormErrors,
    roles,
    specialties,
} = storeToRefs(store);

const form = ref({
    userId: null,
    name: '',
    role_id: '',
    specialty: '',
    biografia: '',
    cmp: '',
    rne: '',
    password: '',
    username: '',
    email: '',
    avatar: null,
    firma_digital: null,
});

const avatarPreview = ref(null);
const signaturePreview = ref(null);

const isEditing = computed(() => Boolean(form.value.userId));

watch([userModalVisible, editingUser], ([visible, item]) => {
    if (visible) {
        document.body.classList.add('modal-open');
        avatarPreview.value = null;
        signaturePreview.value = null;

        if (item) {
            form.value = {
                userId: item.id || null,
                name: item.name || '',
                role_id: item.role_id ?? '',
                specialty: item.specialty ?? '',
                biografia: item.biografia || '',
                cmp: item.cmp || '',
                rne: item.rne || '',
                password: '',
                username: item.username || '',
                email: item.email || '',
                avatar: null,
                firma_digital: null,
            };
            if (item.avatar_url) {
                avatarPreview.value = item.avatar_url;
            }
        } else {
            form.value = {
                userId: null,
                name: '',
                role_id: '',
                specialty: '',
                biografia: '',
                cmp: '',
                rne: '',
                password: '',
                username: '',
                email: '',
                avatar: null,
                firma_digital: null,
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
        store.closeUserModal();
    }
};

const onKeydown = (e) => {
    if (e.key === 'Escape' && userModalVisible.value && !submitting.value) {
        store.closeUserModal();
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

const onAvatarChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.value.avatar = file;
        const reader = new FileReader();
        reader.onload = (event) => {
            avatarPreview.value = event.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        form.value.avatar = null;
    }
};

const onSignatureChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.value.firma_digital = file;
        const reader = new FileReader();
        reader.onload = (event) => {
            signaturePreview.value = event.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        form.value.firma_digital = null;
    }
};

const handleSubmit = async () => {
    const payload = new FormData();
    if (form.value.userId) {
        payload.append('userId', form.value.userId);
    }
    payload.append('name', form.value.name?.trim() || '');
    payload.append('role_id', form.value.role_id || '');
    payload.append('specialty', form.value.specialty || '');

    if (form.value.biografia) {
        payload.append('biografia', form.value.biografia.trim());
    }
    if (form.value.cmp) {
        payload.append('cmp', form.value.cmp.trim());
    }
    if (form.value.rne) {
        payload.append('rne', form.value.rne.trim());
    }
    if (form.value.password) {
        payload.append('password', form.value.password);
    }
    if (form.value.avatar) {
        payload.append('avatar', form.value.avatar);
    }
    if (form.value.firma_digital) {
        payload.append('firma_digital', form.value.firma_digital);
    }

    await store.saveUser(payload);
};
</script>

<template>
    <Teleport to="body">
        <div
            v-if="userModalVisible"
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background: rgba(0, 0, 0, 0.55);"
            @click="onBackdropClick"
        >
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header" :class="isEditing ? 'bg-warning-light text-dark' : 'bg-primary text-white'">
                        <h5 class="modal-title font-weight-bold mb-0">
                            <i class="bi" :class="isEditing ? 'bi-pencil-square mr-1' : 'bi-person-plus mr-1'"></i>
                            {{ isEditing ? `Actualizar Usuario: ${editingUser?.name}` : 'Agregar Nuevo Usuario' }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            :class="{ 'text-white': !isEditing }"
                            aria-label="Cerrar"
                            :disabled="submitting"
                            @click="store.closeUserModal()"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="handleSubmit">
                        <div class="modal-body p-4">
                            <!-- Fila: Nombre, Rol, Especialidad -->
                            <div class="form-row">
                                <div class="col-md-6 form-group">
                                    <label for="user_name" class="font-weight-semibold">
                                        Nombres completos <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        id="user_name"
                                        v-model="form.name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': userFormErrors.name }"
                                        placeholder="Ej: Dr. Juan Pérez"
                                        required
                                        maxlength="255"
                                        :disabled="submitting"
                                    >
                                    <div v-if="userFormErrors.name" class="invalid-feedback d-block">
                                        {{ userFormErrors.name[0] }}
                                    </div>
                                </div>

                                <div class="col-md-3 form-group">
                                    <label for="user_role" class="font-weight-semibold">
                                        Rol <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        id="user_role"
                                        v-model="form.role_id"
                                        class="form-control"
                                        :class="{ 'is-invalid': userFormErrors.role_id }"
                                        required
                                        :disabled="submitting"
                                    >
                                        <option value="">-- Seleccione --</option>
                                        <option v-for="r in roles" :key="r.id" :value="r.id">
                                            {{ r.name }}
                                        </option>
                                    </select>
                                    <div v-if="userFormErrors.role_id" class="invalid-feedback d-block">
                                        {{ userFormErrors.role_id[0] }}
                                    </div>
                                </div>

                                <div class="col-md-3 form-group">
                                    <label for="user_specialty" class="font-weight-semibold">
                                        Especialidad <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        id="user_specialty"
                                        v-model="form.specialty"
                                        class="form-control"
                                        :class="{ 'is-invalid': userFormErrors.specialty }"
                                        required
                                        :disabled="submitting"
                                    >
                                        <option value="">-- Seleccione --</option>
                                        <option v-for="e in specialties" :key="e.id" :value="e.id">
                                            {{ e.descripcion }}
                                        </option>
                                    </select>
                                    <div v-if="userFormErrors.specialty" class="invalid-feedback d-block">
                                        {{ userFormErrors.specialty[0] }}
                                    </div>
                                </div>
                            </div>

                            <!-- Fila Datos Médicos (CMP / RNE) -->
                            <div class="form-row">
                                <div class="col-md-6 form-group">
                                    <label for="user_cmp" class="font-weight-semibold">
                                        Número de Colegiatura Médica (CMP)
                                    </label>
                                    <input
                                        id="user_cmp"
                                        v-model="form.cmp"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': userFormErrors.cmp }"
                                        placeholder="Ej: 60432"
                                        maxlength="20"
                                        :disabled="submitting"
                                    >
                                    <div v-if="userFormErrors.cmp" class="invalid-feedback d-block">
                                        {{ userFormErrors.cmp[0] }}
                                    </div>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="user_rne" class="font-weight-semibold">
                                        Registro Nacional de Especialista (RNE)
                                    </label>
                                    <input
                                        id="user_rne"
                                        v-model="form.rne"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': userFormErrors.rne }"
                                        placeholder="Ej: 39261"
                                        maxlength="20"
                                        :disabled="submitting"
                                    >
                                    <div v-if="userFormErrors.rne" class="invalid-feedback d-block">
                                        {{ userFormErrors.rne[0] }}
                                    </div>
                                </div>
                            </div>

                            <!-- Fila: Username / Email / Contraseña -->
                            <div v-if="isEditing" class="form-row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-semibold">Alias (Username)</label>
                                    <input
                                        type="text"
                                        class="form-control bg-light"
                                        :value="form.username"
                                        readonly
                                    >
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-semibold">Email</label>
                                    <input
                                        type="text"
                                        class="form-control bg-light"
                                        :value="form.email"
                                        readonly
                                    >
                                </div>
                            </div>

                            <div v-else class="alert alert-info py-2 small d-flex align-items-center mb-3">
                                <i class="bi bi-info-circle-fill mr-2" style="font-size: 1.2rem;"></i>
                                <div>
                                    El alias (username) y correo institucional se generarán automáticamente a partir del nombre completo.
                                </div>
                            </div>

                            <!-- Biografía -->
                            <div class="form-group">
                                <label for="user_bio" class="font-weight-semibold">
                                    Biografía y formación médica (opcional)
                                </label>
                                <textarea
                                    id="user_bio"
                                    v-model="form.biografia"
                                    class="form-control"
                                    :class="{ 'is-invalid': userFormErrors.biografia }"
                                    rows="2"
                                    placeholder="Breve reseña de especializaciones, cursos o certificaciones..."
                                    maxlength="255"
                                    :disabled="submitting"
                                ></textarea>
                                <div v-if="userFormErrors.biografia" class="invalid-feedback d-block">
                                    {{ userFormErrors.biografia[0] }}
                                </div>
                            </div>

                            <!-- Avatares y Firma Digital -->
                            <div class="form-row mt-3">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-semibold d-block">Foto / Avatar</label>
                                    <div class="custom-file mb-2">
                                        <input
                                            type="file"
                                            class="custom-file-input"
                                            id="user_avatar_file"
                                            accept="image/jpeg,image/png,image/jpg,image/gif"
                                            @change="onAvatarChange"
                                            :disabled="submitting"
                                        >
                                        <label class="custom-file-label" for="user_avatar_file">
                                            {{ form.avatar ? form.avatar.name : 'Elegir imagen...' }}
                                        </label>
                                    </div>
                                    <div v-if="avatarPreview" class="text-center mt-2">
                                        <img
                                            :src="avatarPreview"
                                            alt="Preview Avatar"
                                            class="img-thumbnail rounded-circle"
                                            style="width: 80px; height: 80px; object-fit: cover;"
                                        >
                                    </div>
                                    <div v-if="userFormErrors.avatar" class="invalid-feedback d-block">
                                        {{ userFormErrors.avatar[0] }}
                                    </div>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label class="font-weight-semibold d-block">Firma Digital (Opcional)</label>
                                    <div class="custom-file mb-2">
                                        <input
                                            type="file"
                                            class="custom-file-input"
                                            id="user_signature_file"
                                            accept="image/jpeg,image/png,image/jpg"
                                            @change="onSignatureChange"
                                            :disabled="submitting"
                                        >
                                        <label class="custom-file-label" for="user_signature_file">
                                            {{ form.firma_digital ? form.firma_digital.name : 'Elegir archivo PNG/JPG...' }}
                                        </label>
                                    </div>
                                    <div v-if="signaturePreview" class="text-center mt-2">
                                        <img
                                            :src="signaturePreview"
                                            alt="Preview Firma"
                                            class="img-thumbnail bg-white"
                                            style="max-height: 80px; max-width: 150px; object-fit: contain;"
                                        >
                                    </div>
                                    <div v-if="userFormErrors.firma_digital" class="invalid-feedback d-block">
                                        {{ userFormErrors.firma_digital[0] }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                :disabled="submitting"
                                @click="store.closeUserModal()"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="submitting || !form.name || !form.role_id || !form.specialty"
                            >
                                <span v-if="submitting" class="spinner-border spinner-border-sm mr-1"></span>
                                <i v-else class="bi" :class="isEditing ? 'bi-check-circle mr-1' : 'bi-plus-circle mr-1'"></i>
                                {{ isEditing ? 'Guardar Cambios' : 'Registrar Usuario' }}
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
.custom-file-label::after {
    content: "Examinar";
}
</style>
