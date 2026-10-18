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
            <div class="input-group">
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
                    <div class="input-group-text bg-white">
                        <span class="fas fa-envelope text-muted"></span>
                    </div>
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
            <div class="input-group">
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
                        class="btn btn-outline-secondary input-group-text bg-white border-left-0"
                        tabindex="-1"
                        :title="store.passwordVisible ? 'Ocultar contraseña' : 'Ver contraseña'"
                        @click="store.togglePasswordVisibility"
                    >
                        <span
                            :class="store.passwordVisible ? 'fas fa-eye-slash text-primary' : 'fas fa-eye text-muted'"
                        ></span>
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

        <!-- Fila Recordarme y Botón -->
        <div class="row align-items-center mb-3">
            <div class="col-6">
                <div class="custom-control custom-checkbox">
                    <input
                        id="remember"
                        v-model="store.remember"
                        type="checkbox"
                        name="remember"
                        class="custom-control-input"
                        :disabled="store.loading"
                    >
                    <label class="custom-control-label text-muted small" for="remember">
                        Recordarme
                    </label>
                </div>
            </div>
            <div class="col-6 text-right">
                <span class="badge badge-light text-muted p-1 small">
                    <i class="fas fa-lock mr-1"></i> Protegido
                </span>
            </div>
        </div>

        <!-- Botón Iniciar Sesión -->
        <div class="row">
            <div class="col-12">
                <button
                    id="loginButton"
                    type="submit"
                    class="btn btn-primary btn-block shadow-sm font-weight-600"
                    :disabled="store.loading"
                >
                    <template v-if="store.loading">
                        <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                        Cargando...
                    </template>
                    <template v-else>
                        <i class="fas fa-sign-in-alt mr-1"></i> Iniciar sesión
                    </template>
                </button>
            </div>
        </div>
    </form>
</template>

<style scoped>
.font-weight-500 {
    font-weight: 500;
}
.font-weight-600 {
    font-weight: 600;
}
.input-group-text {
    border-color: #ced4da;
}
.input-group .form-control:focus {
    box-shadow: none;
    border-color: #80bdff;
}
.input-group-append button:focus {
    box-shadow: none;
}
#loginButton {
    padding: 0.6rem 1rem;
    font-size: 0.95rem;
    letter-spacing: 0.3px;
    transition: background-color 0.2s ease, transform 0.1s ease;
}
#loginButton:hover:not(:disabled) {
    transform: translateY(-1px);
}
#loginButton:active:not(:disabled) {
    transform: translateY(0);
}
</style>
