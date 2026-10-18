<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'info',
    },
    message: {
        type: String,
        required: true,
    },
    dismissible: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);

const alertClass = computed(() => {
    switch (props.type) {
        case 'success':
            return 'alert-success';
        case 'danger':
        case 'error':
            return 'alert-danger';
        case 'warning':
            return 'alert-warning';
        default:
            return 'alert-info';
    }
});

const iconClass = computed(() => {
    switch (props.type) {
        case 'success':
            return 'fas fa-check-circle mr-2';
        case 'danger':
        case 'error':
            return 'fas fa-exclamation-circle mr-2';
        case 'warning':
            return 'fas fa-exclamation-triangle mr-2';
        default:
            return 'fas fa-info-circle mr-2';
    }
});
</script>

<template>
    <div id="message">
        <transition name="fade">
            <div
                v-if="message"
                class="alert alert-dismissible"
                :class="alertClass"
                role="alert"
            >
                <button
                    v-if="dismissible"
                    type="button"
                    class="close"
                    aria-label="Cerrar"
                    @click="emit('close')"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
                <i :class="iconClass"></i>
                <span class="alert-text">{{ message }}</span>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease, transform 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}
.alert {
    border-radius: 6px;
    font-size: 0.92rem;
}
</style>
