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
        <div class="card login-card-modern shadow-lg border-0">
            <!-- Encabezado con Logo y Subtítulo de la Empresa -->
            <LoginHeader
                :app-name="store.appName"
                :logo-url="store.logoUrl"
                :subtitle="store.subtitle"
            />

            <!-- Línea divisoria elegante -->
            <div class="login-divider"></div>

            <!-- Cuerpo del Formulario -->
            <div class="card-body pt-0 pb-3 px-4">
                <p class="login-msg-title">
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
.animate-fade-in {
    animation: fadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(12px) scale(0.99);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
</style>
