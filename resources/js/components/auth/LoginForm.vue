<script setup>
import { useAuthStore } from '@/stores/authStore';

const store = useAuthStore();

const handleSubmit = async (event) => {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    await store.login();
};
</script>

<template>
    <form id="loginForm" method="post" @submit.prevent.stop="handleSubmit" novalidate>
        <!-- Campo Email -->
        <div class="form-group mb-3">
            <label for="email" class="form-label sr-only">Correo electrónico</label>
            <div class="input-group login-input-group" :class="{ 'is-invalid-group': store.fieldErrors.email }">
                <input
                    id="email"
                    v-model.trim="store.email"
                    type="email"
                    name="email"
                    class="form-control"
                    :class="{ 'is-invalid': store.fieldErrors.email }"
                    placeholder="E-mail"
                    autocomplete="username"
                    required
                    :disabled="store.loading"
                    @input="store.clearFieldError('email')"
                >
                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="fas fa-envelope text-muted"></i>
                    </span>
                </div>
            </div>
            <span
                v-if="store.fieldErrors.email"
                class="text-danger small d-block mt-1 font-weight-500"
            >
                {{ store.fieldErrors.email[0] }}
            </span>
        </div>

        <!-- Campo Contraseña con Toggle Show/Hide -->
        <div class="form-group mb-3">
            <label for="password" class="form-label sr-only">Contraseña</label>
            <div class="input-group login-input-group" :class="{ 'is-invalid-group': store.fieldErrors.password }">
                <input
                    id="password"
                    v-model="store.password"
                    :type="store.passwordVisible ? 'text' : 'password'"
                    name="password"
                    class="form-control"
                    :class="{ 'is-invalid': store.fieldErrors.password }"
                    placeholder="Clave y/o Contraseña"
                    autocomplete="current-password"
                    required
                    :disabled="store.loading"
                    @input="store.clearFieldError('password')"
                >
                <div class="input-group-append">
                    <button
                        type="button"
                        class="btn input-group-text border-0"
                        tabindex="-1"
                        :title="store.passwordVisible ? 'Ocultar contraseña' : 'Ver contraseña'"
                        @click="store.togglePasswordVisibility"
                    >
                        <i
                            :class="store.passwordVisible ? 'fas fa-eye-slash text-primary' : 'fas fa-eye text-muted'"
                        ></i>
                    </button>
                </div>
            </div>
            <span
                v-if="store.fieldErrors.password"
                class="text-danger small d-block mt-1 font-weight-500"
            >
                {{ store.fieldErrors.password[0] }}
            </span>
        </div>

        <!-- Fila Recordarme y Protegido -->
        <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
            <div class="custom-control custom-checkbox">
                <input
                    id="remember"
                    v-model="store.remember"
                    type="checkbox"
                    name="remember"
                    class="custom-control-input"
                    :disabled="store.loading"
                >
                <label class="custom-control-label text-muted small user-select-none font-weight-500" for="remember">
                    Recordarme
                </label>
            </div>
            <div>
                <span class="login-protected-badge">
                    <i class="fas fa-lock mr-1 text-primary"></i> Protegido
                </span>
            </div>
        </div>

        <!-- Botón Iniciar Sesión -->
        <div class="form-group mb-1">
            <button
                id="loginButton"
                type="submit"
                class="btn btn-primary btn-block btn-login-submit"
                :disabled="store.loading"
            >
                <template v-if="store.loading">
                    <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                    Iniciando sesión...
                </template>
                <template v-else>
                    <i class="fas fa-sign-in-alt mr-2"></i> Iniciar sesión
                </template>
            </button>
        </div>
    </form>
</template>

<style scoped>
.font-weight-500 {
    font-weight: 500;
}
.is-invalid-group {
    border-color: #ef4444 !important;
}
.input-group-append button:focus {
    outline: none;
    box-shadow: none;
}
</style>
