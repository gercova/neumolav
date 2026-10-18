<script setup>
const props = defineProps({
    appName: {
        type: String,
        default: 'Neumotar',
    },
});

const currentYear = new Date().getFullYear();
</script>

<template>
    <div class="card-footer bg-transparent text-center border-top-0 pt-0 pb-3">
        <p class="text-muted small mb-0">
            &copy; {{ currentYear }} <strong>{{ appName }}</strong>. Todos los derechos reservados.
        </p>
    </div>
</template>

<style scoped>
.card-footer {
    font-size: 0.85rem;
}
</style>
