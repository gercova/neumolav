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
        <div class="login-logo-container">
            <template v-if="logoUrl && !imageError">
                <img
                    :src="logoUrl"
                    :alt="'Logo ' + appName"
                    class="login-logo-img"
                    loading="eager"
                    @error="handleImageError"
                >
            </template>
            <template v-else>
                <div class="fallback-logo py-2 text-center">
                    <div class="fallback-icon-box mb-2">
                        <i class="fas fa-lungs fa-2x text-primary"></i>
                    </div>
                    <h3 class="font-weight-bold text-dark m-0">{{ appName }}</h3>
                </div>
            </template>
        </div>

        <div class="text-center px-4 pb-1">
            <p class="login-header-subtitle mb-0">
                {{ subtitle }}
            </p>
        </div>
    </div>
</template>

<style scoped>
.login-header-wrapper {
    background: transparent;
    padding-top: 0.5rem;
}
.fallback-icon-box {
    width: 54px;
    height: 54px;
    margin: 0 auto;
    border-radius: 50%;
    background: rgba(14, 165, 233, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
