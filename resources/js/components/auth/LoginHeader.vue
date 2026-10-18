<script setup>
import { ref } from 'vue';

const props = defineProps({
    appName: {
        type: String,
        default: 'Neumotar',
    },
    logoUrl: {
        type: String,
        default: null,
    },
    subtitle: {
        type: String,
        default: 'Especialista en enfermedades respiratorias',
    },
});

const imageError = ref(false);

const handleImageError = () => {
    imageError.value = true;
};
</script>

<template>
    <div class="login-header-wrapper">
        <div class="text-center p-3 brand-logo-container">
            <template v-if="logoUrl && !imageError">
                <picture>
                    <source :srcset="logoUrl" type="image/png">
                    <img
                        :src="logoUrl"
                        :alt="'Logo ' + appName"
                        class="img-fluid login-logo-img"
                        loading="lazy"
                        @error="handleImageError"
                    >
                </picture>
            </template>
            <template v-else>
                <div class="fallback-logo py-2">
                    <i class="fas fa-lungs fa-3x text-primary mb-2"></i>
                    <h3 class="font-weight-bold text-dark m-0">{{ appName }}</h3>
                </div>
            </template>
        </div>

        <div class="card-header text-center border-top-0 pt-0 pb-2">
            <p class="login-box-msg text-muted mb-0 font-weight-500">
                {{ subtitle }}
            </p>
        </div>
    </div>
</template>

<style scoped>
.login-header-wrapper {
    background: transparent;
}
.brand-logo-container {
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.login-logo-img {
    max-height: 90px;
    max-width: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}
.login-logo-img:hover {
    transform: scale(1.02);
}
.login-box-msg {
    font-size: 0.95rem;
    color: #495057;
}
</style>
