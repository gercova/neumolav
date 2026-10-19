<script setup>
import { onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useEnterpriseStore } from '@/stores/enterpriseStore';
import EnterpriseGeneralForm from './EnterpriseGeneralForm.vue';
import EnterpriseImageCard from './EnterpriseImageCard.vue';

const props = defineProps({
    initialState: {
        type: Object,
        default: () => ({}),
    },
});

const store = useEnterpriseStore();
const { activeTab, loading } = storeToRefs(store);

const tabs = [
    { key: 'general', label: 'Información General', icon: 'bi-building' },
    { key: 'representante', label: 'Foto Representante', icon: 'bi-person-badge' },
    { key: 'logo', label: 'Logo Principal', icon: 'bi-image' },
    { key: 'miniatura', label: 'Miniatura / Isotipo', icon: 'bi-aspect-ratio' },
    { key: 'receta', label: 'Fondo Recetas', icon: 'bi-file-medical' },
];

const imageCards = [ {
        key: 'representante',
        imageKey: 'foto_representante',
        fieldName: 'foto_representante',
        op: 2,
        title: 'Foto del Representante Legal',
        description: 'Foto oficial del representante legal de la empresa. Se muestra en documentos y certificados.',
        recommendation: 'Recomendado: 300×300 px. Formatos: JPG, PNG. Tamaño máximo: 2MB.',
        icon: 'bi-person-circle',
        badge: 'op=2',
    }, {
        key: 'logo',
        imageKey: 'logo',
        fieldName: 'logo',
        op: 3,
        title: 'Logo Institucional',
        description: 'Logo principal de la empresa utilizado en la cabecera del sistema y documentos oficiales.',
        recommendation: 'Recomendado: 600×200 px. Formatos: PNG con fondo transparente. Tamaño máximo: 2MB.',
        icon: 'bi-image-fill',
        badge: 'op=3',
    }, {
        key: 'miniatura',
        imageKey: 'logo_mini',
        fieldName: 'logo_mini',
        op: 4,
        title: 'Miniatura / Isotipo',
        description: 'Versión reducida del logo, usada como favicon y en espacios compactos del sistema.',
        recommendation: 'Recomendado: 64×64 px (cuadrado). Formatos: PNG, ICO. Tamaño máximo: 2MB.',
        icon: 'bi-grid-1x2-fill',
        badge: 'op=4',
    }, {
        key: 'receta',
        imageKey: 'logo_receta',
        fieldName: 'logo_receta',
        op: 5,
        title: 'Fondo para Recetas Médicas',
        description: 'Imagen de fondo aplicada en la generación de recetas y documentos médicos.',
        recommendation: 'Recomendado: A4 (794×1123 px). Formatos: JPG, PNG. Tamaño máximo: 2MB.',
        icon: 'bi-file-earmark-medical',
        badge: 'op=5',
    },
];

onMounted(() => {
    store.init(props.initialState);
});
</script>

<template>
    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Empresa</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/admin/home">Home</a></li>
                            <li class="breadcrumb-item">Negocio</li>
                            <li class="breadcrumb-item active">Empresa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">

                <!-- Global Loading Overlay -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="text-muted mt-2 small">Cargando información de la empresa...</p>
                </div>

                <template v-else>
                    <!-- Tabs Navigation -->
                    <div class="card shadow-sm mb-0">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li
                                    v-for="tab in tabs"
                                    :key="tab.key"
                                    class="nav-item"
                                >
                                    <a
                                        class="nav-link d-flex align-items-center"
                                        :class="{ active: activeTab === tab.key }"
                                        href="#"
                                        role="tab"
                                        @click.prevent="store.setActiveTab(tab.key)"
                                    >
                                        <i :class="['bi', tab.icon, 'mr-2']"></i>
                                        <span class="d-none d-md-inline">{{ tab.label }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab: General Information -->
                        <div v-show="activeTab === 'general'" class="card-body">
                            <EnterpriseGeneralForm />
                        </div>

                        <!-- Tabs: Image uploads -->
                        <template v-for="card in imageCards" :key="card.key">
                            <div v-show="activeTab === card.key" class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-lg-8 col-xl-7">
                                        <EnterpriseImageCard
                                            :title="card.title"
                                            :description="card.description"
                                            :recommendation="card.recommendation"
                                            :image-key="card.imageKey"
                                            :field-name="card.fieldName"
                                            :op="card.op"
                                            :icon="card.icon"
                                            :badge="card.badge"
                                        />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

            </div>
        </section>
    </div>
</template>

<style scoped>
.nav-tabs .nav-link {
    color: #6c757d;
    border-radius: 0;
    padding: 0.75rem 1.1rem;
    transition: color 0.15s, border-color 0.15s;
}
.nav-tabs .nav-link:hover {
    color: #007bff;
    border-color: transparent transparent #dee2e6;
}
.nav-tabs .nav-link.active {
    color: #007bff;
    font-weight: 600;
    border-top-color: #007bff;
}
</style>
