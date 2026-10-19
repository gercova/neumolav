<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { useUsersStore } from '@/stores/usersStore';

const store = useUsersStore();
const {
    passwordModalVisible,
    passwordTargetUser,
    passwordSubmitting,
    passwordFormErrors,
} = storeToRefs(store);

const password = ref('');
const passwordConfirmation = ref('');
const showPassword = ref(false);

watch([passwordModalVisible, passwordTargetUser], ([visible]) => {
    if (visible) {
        document.body.classList.add('modal-open');
        password.value = '';
        passwordConfirmation.value = '';
        showPassword.value = false;
    } else {
        document.body.classList.remove('modal-open');
    }
});

onBeforeUnmount(() => {
    document.body.classList.remove('modal-open');
});

const onBackdropClick = (e) => {
    if (e.target.classList.contains('modal') && !passwordSubmitting.value) {
        store.closePasswordModal();
    }
};

const onKeydown = (e) => {
    if (e.key === 'Escape' && passwordModalVisible.value && !passwordSubmitting.value) {
        store.closePasswordModal();
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
    await store.savePassword({
        password: password.value,
        password_confirmation: passwordConfirmation.value,
    });
};
</script>

<template>
    <Teleport to="body">
        <div
            v-if="passwordModalVisible"
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background: rgba(0, 0, 0, 0.55);"
            @click="onBackdropClick"
        >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title font-weight-bold mb-0">
                            <i class="bi bi-key mr-1"></i>
                            Cambiar Contraseña
                        </h5>
                        <button
                            type="button"
                            class="close text-white"
                            aria-label="Cerrar"
                            :disabled="passwordSubmitting"
                            @click="store.closePasswordModal()"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="handleSubmit">
                        <div class="modal-body">
                            <!-- Info del usuario destino -->
                            <div v-if="passwordTargetUser" class="alert alert-light border d-flex align-items-center mb-3">
                                <img
                                    :src="passwordTargetUser.avatar_url || '/images/default-avatar.png'"
                                    class="rounded-circle mr-3 border"
                                    style="width: 44px; height: 44px; object-fit: cover;"
                                    @error="(e) => (e.target.src = '/dist/img/user2-160x160.jpg')"
                                    alt="Avatar"
                                >
                                <div>
                                    <div class="font-weight-bold text-dark">{{ passwordTargetUser.name }}</div>
                                    <small class="text-muted">{{ passwordTargetUser.email }}</small>
                                </div>
                            </div>

                            <!-- Nueva Contraseña -->
                            <div class="form-group">
                                <label for="new_password" class="font-weight-semibold">
                                    Nueva Contraseña <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                    </div>
                                    <input
                                        id="new_password"
                                        v-model="password"
                                        :type="showPassword ? 'text' : 'password'"
                                        class="form-control"
                                        :class="{ 'is-invalid': passwordFormErrors.password }"
                                        placeholder="Mínimo 6 caracteres"
                                        required
                                        minlength="6"
                                        maxlength="20"
                                        :disabled="passwordSubmitting"
                                    >
                                    <div class="input-group-append">
                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary"
                                            @click="showPassword = !showPassword"
                                            tabindex="-1"
                                            title="Ver/Ocultar contraseña"
                                        >
                                            <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                                        </button>
                                    </div>
                                    <div v-if="passwordFormErrors.password" class="invalid-feedback d-block">
                                        {{ passwordFormErrors.password[0] }}
                                    </div>
                                </div>
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="form-group mb-0">
                                <label for="new_password_confirmation" class="font-weight-semibold">
                                    Confirmar Contraseña <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white">
                                            <i class="bi bi-shield-lock"></i>
                                        </span>
                                    </div>
                                    <input
                                        id="new_password_confirmation"
                                        v-model="passwordConfirmation"
                                        :type="showPassword ? 'text' : 'password'"
                                        class="form-control"
                                        :class="{ 'is-invalid': passwordFormErrors.password_confirmation }"
                                        placeholder="Repita la contraseña"
                                        required
                                        minlength="6"
                                        maxlength="20"
                                        :disabled="passwordSubmitting"
                                    >
                                    <div v-if="passwordFormErrors.password_confirmation" class="invalid-feedback d-block">
                                        {{ passwordFormErrors.password_confirmation[0] }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                :disabled="passwordSubmitting"
                                @click="store.closePasswordModal()"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="passwordSubmitting || !password || !passwordConfirmation"
                            >
                                <span v-if="passwordSubmitting" class="spinner-border spinner-border-sm mr-1"></span>
                                <i v-else class="bi bi-check-circle mr-1"></i>
                                Actualizar Contraseña
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
</style>
