<script setup>
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { usePostsStore } from '@/stores/postsStore';

const store = usePostsStore();
const { panelVisible, editingPost, submitting, formErrors, postTypes, permissions } = storeToRefs(store);

const isEditing = computed(() => !!(editingPost.value && editingPost.value.id));

const emptyForm = () => ({
    postId: null,
    type_id: '',
    titulo: '',
    titulo_corto: '',
    resumen: '',
    contenido: '',
    descrip_img: '',
    categories: '',
    meta_content: '',
    key_words: '',
    etiquetas: '',
    img: null,
    img_url: null,
});

const form              = ref(emptyForm());
const fileInput         = ref(null);
const localImgPreview   = ref(null);

watch(editingPost, (val) => {
    if (val) {
        form.value = {
            postId: val.id ?? null,
            type_id: val.type_id ?? '',
            titulo: val.titulo ?? '',
            titulo_corto: val.titulo_corto ?? '',
            resumen: val.resumen ?? '',
            contenido: val.contenido ?? '',
            descrip_img: val.descrip_img ?? '',
            categories: val.categories ?? '',
            meta_content: val.meta_content ?? '',
            key_words: val.key_words ?? '',
            etiquetas: val.etiquetas ?? '',
            img: null,
            img_url: val.img_url ?? null,
        };
        localImgPreview.value = null;
    } else {
        form.value = emptyForm();
        localImgPreview.value = null;
    }
}, { immediate: true });

watch(panelVisible, (visible) => {
    if (visible) {
        document.body.classList.add('modal-open');
    } else {
        document.body.classList.remove('modal-open');
        form.value = emptyForm();
        localImgPreview.value = null;
    }
});

const onKeydown = (e) => {
    if (e.key === 'Escape' && panelVisible.value && !submitting.value) {
        store.closePanel();
    }
};

onMounted(() => {
    window.addEventListener('keydown', onKeydown);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown);
    document.body.classList.remove('modal-open');
});

const onFileChange = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) {
        window.alertNotify?.('warning', 'La imagen no debe superar 2MB');
        return;
    }
    form.value.img = file;
    localImgPreview.value = URL.createObjectURL(file);
};

const clearImage = () => {
    form.value.img = null;
    localImgPreview.value = null;
    if (fileInput.value) fileInput.value.value = '';
};

const previewUrl = computed(() => localImgPreview.value || form.value.img_url || null);

const fieldError = (field) => formErrors.value?.[field]?.[0] ?? null;

const onSubmit = async () => {
    await store.savePost(form.value);
};
</script>

<template>
    <!-- Panel overlay backdrop -->
    <transition name="backdrop-fade">
        <div
            v-if="panelVisible"
            class="post-panel-backdrop"
            @click="store.closePanel()"
        ></div>
    </transition>

    <!-- Slide-in panel -->
    <transition name="panel-slide">
        <div v-if="panelVisible" class="post-slide-panel shadow-lg">
            <!-- Panel header -->
            <div class="panel-header d-flex align-items-center justify-content-between px-4 py-3 border-bottom bg-white">
                <h5 class="mb-0 font-weight-bold d-flex align-items-center">
                    <i class="bi bi-journal-richtext text-primary mr-2"></i>
                    {{ isEditing ? 'Editar publicación' : 'Nueva publicación' }}
                </h5>
                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    :disabled="submitting"
                    @click="store.closePanel()"
                >
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Panel scrollable body -->
            <div class="panel-body px-4 py-3">
                <form id="postSpaForm" enctype="multipart/form-data" @submit.prevent="onSubmit">

                    <!-- SECCIÓN 1: Identificación -->
                    <div class="mb-4">
                        <h6 class="text-primary font-weight-bold pb-2 border-bottom d-flex align-items-center">
                            <i class="bi bi-type-h1 mr-2"></i> Identificación
                        </h6>
                        <div class="form-row">
                            <div class="col-12 form-group">
                                <label for="pub-titulo" class="font-weight-semibold">Título <span class="text-danger">*</span></label>
                                <input
                                    id="pub-titulo"
                                    v-model="form.titulo"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': fieldError('titulo') }"
                                    placeholder="Título de la publicación"
                                    :disabled="submitting"
                                    maxlength="150"
                                >
                                <div v-if="fieldError('titulo')" class="invalid-feedback">{{ fieldError('titulo') }}</div>
                            </div>

                            <div class="col-md-8 form-group">
                                <label for="pub-titulo-corto" class="font-weight-semibold">Título corto</label>
                                <input
                                    id="pub-titulo-corto"
                                    v-model="form.titulo_corto"
                                    type="text"
                                    class="form-control"
                                    placeholder="Título resumido"
                                    :disabled="submitting"
                                >
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="pub-type" class="font-weight-semibold">Tipo <span class="text-danger">*</span></label>
                                <select
                                    id="pub-type"
                                    v-model="form.type_id"
                                    class="form-control"
                                    :class="{ 'is-invalid': fieldError('type_id') }"
                                    :disabled="submitting"
                                >
                                    <option value="">— Seleccione —</option>
                                    <option
                                        v-for="pt in postTypes"
                                        :key="pt.id"
                                        :value="pt.id"
                                    >
                                        {{ pt.description }}
                                    </option>
                                </select>
                                <div v-if="fieldError('type_id')" class="invalid-feedback">{{ fieldError('type_id') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: Contenido -->
                    <div class="mb-4">
                        <h6 class="text-primary font-weight-bold pb-2 border-bottom d-flex align-items-center">
                            <i class="bi bi-file-text mr-2"></i> Contenido
                        </h6>

                        <div class="form-group">
                            <label for="pub-resumen" class="font-weight-semibold">Resumen <span class="text-danger">*</span></label>
                            <textarea
                                id="pub-resumen"
                                v-model="form.resumen"
                                rows="2"
                                class="form-control"
                                :class="{ 'is-invalid': fieldError('resumen') }"
                                placeholder="Breve descripción de la publicación..."
                                :disabled="submitting"
                            ></textarea>
                            <div v-if="fieldError('resumen')" class="invalid-feedback">{{ fieldError('resumen') }}</div>
                        </div>

                        <div class="form-group">
                            <label for="pub-contenido" class="font-weight-semibold">
                                Contenido <span class="text-danger">*</span>
                                <small class="text-muted font-weight-normal ml-1">(HTML permitido)</small>
                            </label>
                            <textarea
                                id="pub-contenido"
                                v-model="form.contenido"
                                rows="8"
                                class="form-control font-monospace small"
                                :class="{ 'is-invalid': fieldError('contenido') }"
                                placeholder="Contenido completo de la publicación..."
                                :disabled="submitting"
                            ></textarea>
                            <div v-if="fieldError('contenido')" class="invalid-feedback">{{ fieldError('contenido') }}</div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: Imagen -->
                    <div class="mb-4">
                        <h6 class="text-primary font-weight-bold pb-2 border-bottom d-flex align-items-center">
                            <i class="bi bi-image mr-2"></i> Imagen destacada
                        </h6>
                        <div class="form-row">
                            <div class="col-md-5 form-group">
                                <label class="font-weight-semibold">
                                    Archivo de imagen <span v-if="!isEditing" class="text-danger">*</span>
                                </label>
                                <div class="border rounded p-3 bg-light text-center" style="min-height: 120px;">
                                    <div v-if="previewUrl" class="mb-2">
                                        <img
                                            :src="previewUrl"
                                            alt="Vista previa"
                                            class="img-thumbnail"
                                            style="max-height: 100px; object-fit: contain;"
                                        >
                                    </div>
                                    <div v-else class="text-muted small py-3">
                                        <i class="bi bi-image display-6 d-block text-secondary mb-1"></i>
                                        Sin imagen
                                    </div>
                                    <input
                                        ref="fileInput"
                                        type="file"
                                        class="d-none"
                                        accept="image/jpeg, image/png, image/jpg, image/gif, image/svg+xml"
                                        :disabled="submitting"
                                        @change="onFileChange"
                                    >
                                    <div class="mt-1 d-flex justify-content-center gap-2">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            :disabled="submitting"
                                            @click="fileInput.click()"
                                        >
                                            <i class="bi bi-folder2-open mr-1"></i>
                                            {{ previewUrl ? 'Cambiar' : 'Seleccionar' }}
                                        </button>
                                        <button
                                            v-if="localImgPreview"
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            @click="clearImage"
                                        >
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-1">JPEG, PNG, GIF. Máx. 2MB</small>
                                    <div v-if="fieldError('img')" class="text-danger small mt-1 font-weight-bold">
                                        {{ fieldError('img') }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-7">
                                <div class="form-group">
                                    <label for="pub-descrip-img" class="font-weight-semibold">Descripción imagen (SEO) <span class="text-danger">*</span></label>
                                    <textarea
                                        id="pub-descrip-img"
                                        v-model="form.descrip_img"
                                        rows="2"
                                        class="form-control"
                                        :class="{ 'is-invalid': fieldError('descrip_img') }"
                                        placeholder="Descripción de la imagen para motores de búsqueda"
                                        :disabled="submitting"
                                        maxlength="250"
                                    ></textarea>
                                    <div v-if="fieldError('descrip_img')" class="invalid-feedback">{{ fieldError('descrip_img') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: SEO & Categorías -->
                    <div class="mb-4">
                        <h6 class="text-primary font-weight-bold pb-2 border-bottom d-flex align-items-center">
                            <i class="bi bi-tags mr-2"></i> SEO & Categorización
                        </h6>
                        <div class="form-row">
                            <div class="col-md-6 form-group">
                                <label for="pub-categories" class="font-weight-semibold">Categorías <span class="text-danger">*</span></label>
                                <input
                                    id="pub-categories"
                                    v-model="form.categories"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': fieldError('categories') }"
                                    placeholder="Salud, Neumología, Tratamientos"
                                    :disabled="submitting"
                                    maxlength="250"
                                >
                                <div v-if="fieldError('categories')" class="invalid-feedback">{{ fieldError('categories') }}</div>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="pub-etiquetas" class="font-weight-semibold">Etiquetas <span class="text-danger">*</span></label>
                                <input
                                    id="pub-etiquetas"
                                    v-model="form.etiquetas"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': fieldError('etiquetas') }"
                                    placeholder="neumonia, covid, respiratorio"
                                    :disabled="submitting"
                                >
                                <div v-if="fieldError('etiquetas')" class="invalid-feedback">{{ fieldError('etiquetas') }}</div>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="pub-meta" class="font-weight-semibold">Meta-content (SEO) <span class="text-danger">*</span></label>
                                <textarea
                                    id="pub-meta"
                                    v-model="form.meta_content"
                                    rows="2"
                                    class="form-control"
                                    :class="{ 'is-invalid': fieldError('meta_content') }"
                                    placeholder="Meta descripción para SEO..."
                                    :disabled="submitting"
                                    maxlength="250"
                                ></textarea>
                                <div v-if="fieldError('meta_content')" class="invalid-feedback">{{ fieldError('meta_content') }}</div>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="pub-keywords" class="font-weight-semibold">Palabras clave (SEO) <span class="text-danger">*</span></label>
                                <textarea
                                    id="pub-keywords"
                                    v-model="form.key_words"
                                    rows="2"
                                    class="form-control"
                                    :class="{ 'is-invalid': fieldError('key_words') }"
                                    placeholder="neumología, pulmones, salud respiratoria"
                                    :disabled="submitting"
                                    maxlength="250"
                                ></textarea>
                                <div v-if="fieldError('key_words')" class="invalid-feedback">{{ fieldError('key_words') }}</div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <!-- Panel footer -->
            <div class="panel-footer px-4 py-3 border-top bg-white d-flex justify-content-end align-items-center">
                <button
                    type="button"
                    class="btn btn-secondary mr-2"
                    :disabled="submitting"
                    @click="store.closePanel()"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    form="postSpaForm"
                    class="btn btn-primary px-4"
                    :disabled="submitting"
                >
                    <span v-if="submitting" class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                    <i v-else class="bi bi-floppy mr-2"></i>
                    {{ isEditing ? 'Actualizar publicación' : 'Guardar publicación' }}
                </button>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.post-panel-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.35);
    z-index: 1040;
}
.post-slide-panel {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: min(680px, 96vw);
    background: #fff;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.panel-body {
    flex: 1;
    overflow-y: auto;
}
.panel-header,
.panel-footer {
    flex-shrink: 0;
}

/* Transitions */
.backdrop-fade-enter-active,
.backdrop-fade-leave-active {
    transition: opacity 0.22s ease;
}
.backdrop-fade-enter-from,
.backdrop-fade-leave-to {
    opacity: 0;
}

.panel-slide-enter-active,
.panel-slide-leave-active {
    transition: transform 0.26s cubic-bezier(0.4, 0, 0.2, 1);
}
.panel-slide-enter-from,
.panel-slide-leave-to {
    transform: translateX(100%);
}

.font-weight-semibold {
    font-weight: 600;
}
</style>
