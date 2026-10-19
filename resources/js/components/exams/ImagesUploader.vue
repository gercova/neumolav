<script setup>
import { ref } from 'vue';
import { useExamFormStore } from '@/stores/examFormStore';

const store = useExamFormStore();
const fileInput = ref(null);

const trigger = () => fileInput.value?.click();

const onFiles = (e) => {
    Array.from(e.target.files || []).forEach(f => store.addImage(f));
    e.target.value = '';
};

const onRemoveImage = async (i) => {
    const img = store.images[i];
    const r = await window.Swal.fire({
        icon: 'warning',
        title: '¿Eliminar imagen?',
        text: img?.id ? 'Esta acción no se puede deshacer.' : '',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
    });
    if (!r.isConfirmed) return;
    try {
        if (img?.id) {
            const data = await store.deleteExistingImage(i);
            window.alertNotify?.('success', data?.messages || 'Imagen eliminada');
        } else {
            store.removeImage(i);
        }
    } catch {
        window.Swal.fire('Error', 'No se pudo eliminar la imagen.', 'error');
    }
};
</script>

<template>
    <div class="card card-info">
        <div class="card-header"><h5 class="card-title">Exámenes radiológicos</h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-4 mb-3">
                    <button type="button" class="btn btn-primary btn-block" @click="trigger">
                        <i class="bi bi-plus-circle"></i> Agregar imagen
                    </button>
                    <input ref="fileInput" type="file" multiple accept="image/*" class="d-none" @change="onFiles">
                </div>
                <div class="col-12">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Fecha del examen</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!store.images.length">
                                <td colspan="3" class="text-muted text-center py-3">Sin imágenes adjuntas</td>
                            </tr>
                            <tr v-for="(img, i) in store.images" :key="img.id || i">
                                <td>
                                    <img :src="img.preview" alt="preview" style="max-height:60px" class="border rounded">
                                </td>
                                <td>
                                    <input v-model="img.date" type="date" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-xs" @click.prevent.stop="onRemoveImage(i)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>