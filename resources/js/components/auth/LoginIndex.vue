<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import LoginHeader from './LoginHeader.vue';
import LoginAlert from './LoginAlert.vue';
import LoginForm from './LoginForm.vue';
import LoginFooter from './LoginFooter.vue';

const props = defineProps({
    initialState: {
        type: Object,
        default: () => ({}),
    },
});

const store = useAuthStore();

onMounted(() => {
    // Inicializar estado desde props o window global
    const state = Object.keys(props.initialState).length > 0
        ? props.initialState
        : (window.__LOGIN_STATE__ || {});

    store.initState(state);

    // Bandera para scripts legacy
    window.__vueLoginMounted = true;
});
</script>

<template>
    <div class="login-box animate-fade-in">
        <div class="card card-outline card-primary shadow border-0">
            <!-- Encabezado con Logo y Subtítulo de la Empresa -->
            <LoginHeader
                :app-name="store.appName"
                :logo-url="store.logoUrl"
                :subtitle="store.subtitle"
            />

            <!-- Cuerpo del Formulario -->
            <div class="card-body pt-1 pb-3">
                <p class="login-box-msg text-center text-secondary mb-3 font-weight-500">
                    Bienvenido, ingrese sus credenciales
                </p>

                <!-- Alerta Reactiva para Mensajes y Errores -->
                <LoginAlert
                    v-if="store.alert.visible"
                    :type="store.alert.type"
                    :message="store.alert.message"
                    @close="store.clearAlert"
                />

                <!-- Formulario Reactivo con Validación -->
                <LoginForm />
            </div>

            <!-- Pie con Derechos de Autor -->
            <LoginFooter :app-name="store.appName" />
        </div>
    </div>
</template>

<style scoped>
.login-box {
    width: 380px;
    margin: 0 auto;
}
@media (max-width: 576px) {
    .login-box {
        width: 92%;
        margin-top: 1rem;
    }
}
.card {
    border-radius: 12px;
    overflow: hidden;
    background-color: #ffffff;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
}
.card-primary.card-outline {
    border-top: 3px solid #007bff;
}
.font-weight-500 {
    font-weight: 500;
}
.login-box-msg {
    font-size: 0.92rem;
}
.animate-fade-in {
    animation: fadeIn 0.4s ease-in-out;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
