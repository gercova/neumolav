<script setup>
import { ref, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useEnterpriseStore } from '@/stores/enterpriseStore';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    recommendation: {
        type: String,
        default: 'Formatos soportados: JPG, PNG, WEBP. Tamaño máximo: 2MB.',
    },
    imageKey: {
        type: String,
        required: true,
    },
    fieldName: {
        type: String,
        required: true,
    },
    op: {
        type: Number,
        required: true,
    },
    badge: {
        type: String,
        default: '',
    },
    icon: {
        type: String,
        default: 'bi-image',
    },
});

const store = useEnterpriseStore();
const { images, uploading, permissions } = storeToRefs(store);

const fileInput     = ref(null);
const selectedFile  = ref(null);
const localPreview  = ref(null);
const isDragging    = ref(false);

const isUploadingThis   = computed(() => !!uploading.value[props.imageKey]);
const currentImageUrl   = computed(() => images.value[props.imageKey] || null);
const displayUrl        = computed(() => localPreview.value || currentImageUrl.value);

const formatFileSize = (bytes) => {
    if (!bytes) return '0 B';
    const k     = 1024;
    const sizes = ['B', 'KB', 'MB'];
    const i     = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(2))} ${sizes[i]}`;
};

const triggerFileInput = () => {
    if (fileInput.value) {
        fileInput.value.click();
    }
};

const handleFile = (file) => {
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        window.alertNotify?.('warning', 'Solo se permiten archivos de imagen (JPG, PNG, WEBP)');
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        window.alertNotify?.('warning', 'La imagen seleccionada supera el límite permitido de 2MB');
        return;
    }

    selectedFile.value = file;
    localPreview.value = URL.createObjectURL(file);
};

const onFileChange = (e) => {
    const file = e.target.files?.[0];
    handleFile(file);
};

const onDragOver = () => {
    isDragging.value = true;
};

const onDragLeave = () => {
    isDragging.value = false;
};

const onDrop = (e) => {
    isDragging.value = false;
    const file = e.dataTransfer.files?.[0];
    handleFile(file);
};

const cancelSelection = () => {
    selectedFile.value = null;
    if (localPreview.value) {
        URL.revokeObjectURL(localPreview.value);
        localPreview.value = null;
    }
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const uploadSelected = async () => {
    if (!selectedFile.value) return;

    const res = await store.uploadImage({
        op: props.op,
        fieldName: props.fieldName,
        file: selectedFile.value,
        imageKey: props.imageKey,
    });

    if (res.success) {
        cancelSelection();
    }
};
</script>

<template>
    <div class="card h-100 shadow-sm border">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 font-weight-bold text-dark d-flex align-items-center">
                <i :class="['bi', icon, 'text-primary mr-2']"></i>
                {{ title }}
            </h6>
            <span v-if="badge" class="badge badge-light border text-secondary px-2 py-1">
                {{ badge }}
            </span>
        </div>

        <div class="card-body d-flex flex-column">
            <p v-if="description" class="text-muted small mb-3">
                {{ description }}
            </p>

            <!-- Visualizador y Dropzone -->
            <div
                class="image-upload-box mb-3 text-center position-relative d-flex flex-column align-items-center justify-content-center p-3"
                :class="{ 'dragging': isDragging, 'has-image': !!displayUrl }"
                @dragover.prevent="onDragOver"
                @dragleave.prevent="onDragLeave"
                @drop.prevent="onDrop"
            >
                <!-- Hidden file input -->
                <input
                    ref="fileInput"
                    type="file"
                    class="d-none"
                    accept="image/png, image/jpeg, image/jpg, image/webp"
                    :disabled="isUploadingThis || !permissions.update"
                    @change="onFileChange"
                >

                <!-- Vista previa de imagen -->
                <div v-if="displayUrl" class="image-preview-wrapper mb-2">
                    <img
                        :src="displayUrl"
                        :alt="title"
                        class="img-fluid rounded preview-image"
                    >
                    <div v-if="localPreview" class="badge badge-warning position-absolute top-2 right-2 shadow-sm">
                        Pendiente de guardar
                    </div>
                </div>

                <!-- Placeholder sin imagen -->
                <div v-else class="text-muted py-4">
                    <i :class="['bi', icon, 'display-4 d-block text-secondary opacity-50 mb-2']"></i>
                    <span class="small font-weight-semibold">Sin imagen cargada</span>
                </div>

                <!-- Subir / Cambiar Botón de dropzone -->
                <div v-if="permissions.update" class="mt-2">
                    <button
                        type="button"
                        class="btn btn-outline-secondary btn-sm"
                        :disabled="isUploadingThis"
                        @click="triggerFileInput"
                    >
                        <i class="bi bi-cloud-arrow-up mr-1"></i>
                        {{ displayUrl ? 'Seleccionar otra imagen' : 'Seleccionar archivo' }}
                    </button>
                    <p class="text-muted small mt-1 mb-0 drag-hint">o arrastra y suelta aquí</p>
                </div>
            </div>

            <!-- Información del archivo seleccionado -->
            <div v-if="selectedFile" class="alert alert-info py-2 px-3 small d-flex align-items-center justify-content-between mb-3">
                <div class="text-truncate mr-2">
                    <i class="bi bi-file-earmark-image mr-1"></i>
                    <strong>{{ selectedFile.name }}</strong> ({{ formatFileSize(selectedFile.size) }})
                </div>
                <button
                    type="button"
                    class="btn btn-link text-danger p-0 ml-2"
                    title="Descartar"
                    @click="cancelSelection"
                >
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </div>

            <!-- Recomendación y límites -->
            <div class="mt-auto pt-2 border-top">
                <small class="text-muted d-block mb-3">
                    <i class="bi bi-info-circle mr-1"></i>
                    {{ recommendation }}
                </small>

                <!-- Botón de subida si hay archivo seleccionado -->
                <div v-if="permissions.update && selectedFile" class="d-flex justify-content-end">
                    <button
                        type="button"
                        class="btn btn-secondary btn-sm mr-2"
                        :disabled="isUploadingThis"
                        @click="cancelSelection"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary btn-sm px-3"
                        :disabled="isUploadingThis"
                        @click="uploadSelected"
                    >
                        <span v-if="isUploadingThis" class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
                        <i v-else class="bi bi-upload mr-1"></i>
                        Subir Imagen
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.image-upload-box {
    border: 2px dashed #d6d8db;
    border-radius: 8px;
    background-color: #f8f9fa;
    transition: all 0.2s ease-in-out;
    min-height: 220px;
}
.image-upload-box.dragging {
    border-color: #007bff;
    background-color: #e8f4fd;
}
.image-preview-wrapper {
    max-height: 180px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}
.preview-image {
    max-height: 160px;
    object-fit: contain;
    border: 1px solid #e9ecef;
    background-color: #fff;
    padding: 4px;
}
.drag-hint {
    font-size: 0.75rem;
}
.top-2 {
    top: 8px;
}
.right-2 {
    right: 8px;
}
</style>
