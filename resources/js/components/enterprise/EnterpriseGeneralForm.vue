<script setup>
import { ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useEnterpriseStore } from '@/stores/enterpriseStore';

const store = useEnterpriseStore();
const { enterprise, savingGeneral, permissions } = storeToRefs(store);

const form = ref({
    razon_social: '',
    nombre_comercial: '',
    ruc: '',
    email: '',
    pais: '',
    codigo_pais: '',
    telefono: '',
    pagina_web: '',
    representante_legal: '',
    slogan: '',
    frase: '',
    descripcion: '',
    mision: '',
    vision: '',
    ubigeo: '',
    direccion: '',
    rubro: '',
    fecha_creacion: '',
    autocomplete: 'on',
    iframe_location: '',
});

watch(
    enterprise,
    (val) => {
        if (val) {
            form.value = {
                razon_social: val.razon_social || '',
                nombre_comercial: val.nombre_comercial || '',
                ruc: val.ruc || '',
                email: val.email || '',
                pais: val.pais || '',
                codigo_pais: val.codigo_pais || '',
                telefono: val.telefono || '',
                pagina_web: val.pagina_web || '',
                representante_legal: val.representante_legal || '',
                slogan: val.slogan || '',
                frase: val.frase || '',
                descripcion: val.descripcion || '',
                mision: val.mision || '',
                vision: val.vision || '',
                ubigeo: val.ubigeo || '',
                direccion: val.direccion || '',
                rubro: val.rubro || '',
                fecha_creacion: val.fecha_creacion ? val.fecha_creacion.substring(0, 10) : '',
                autocomplete: val.autocomplete || 'on',
                iframe_location: val.iframe_location || '',
            };
        }
    },
    { immediate: true, deep: true }
);

const onSubmit = async () => {
    await store.saveGeneral(form.value);
};
</script>

<template>
    <form @submit.prevent="onSubmit">
        <!-- SECCIÓN 1: DATOS FISCALES & LEGALES -->
        <div class="mb-4">
            <h6 class="text-primary font-weight-bold pb-2 border-bottom d-flex align-items-center">
                <i class="bi bi-shield-check mr-2"></i> Identificación Fiscal & Representación
            </h6>
            <div class="form-row">
                <div class="col-md-4 form-group">
                    <label for="razon_social" class="font-weight-semibold">Razón Social</label>
                    <input
                        id="razon_social"
                        v-model="form.razon_social"
                        type="text"
                        class="form-control"
                        placeholder="Ej: Neumología Integral S.A.C."
                        :disabled="savingGeneral || !permissions.update"
                    >
                </div>

                <div class="col-md-4 form-group">
                    <label for="nombre_comercial" class="font-weight-semibold">Nombre Comercial</label>
                    <input
                        id="nombre_comercial"
                        v-model="form.nombre_comercial"
                        type="text"
                        class="form-control"
                        placeholder="Ej: NeumoLav"
                        :disabled="savingGeneral || !permissions.update"
                    >
                </div>

                <div class="col-md-4 form-group">
                    <label for="ruc" class="font-weight-semibold">RUC</label>
                    <input
                        id="ruc"
                        v-model="form.ruc"
                        type="text"
                        class="form-control"
                        placeholder="Ej: 20601234567"
                        maxlength="11"
                        :disabled="savingGeneral || !permissions.update"
                    >
                </div>

                <div class="col-md-6 form-group">
                    <label for="representante_legal" class="font-weight-semibold">Representante Legal</label>
                    <input
                        id="representante_legal"
                        v-model="form.representante_legal"
                        type="text"
                        class="form-control"
                        placeholder="Ej: Dr. Carlos Pérez Ramos"
                        :disabled="savingGeneral || !permissions.update"
                    >
                </div>

                <div class="col-md-3 form-group">
                    <label for="fecha_creacion" class="font-weight-semibold">Fecha de Fundación</label>
                    <input
                        id="fecha_creacion"
                        v-model="form.fecha_creacion"
                        type="date"
                        class="form-control"
                        :disabled="savingGeneral || !permissions.update"
                    >
                </div>

                <div class="col-md-3 form-group">
                    <label for="rubro" class="font-weight-semibold">Rubro / Actividad</label>
                    <input
                        id="rubro"
                        v-model="form.rubro"
                        type="text"
                        class="form-control"
                        placeholder="Ej: Servicios Médicos y Neumología"
                        :disabled="savingGeneral || !permissions.update"
                    >
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: CONTACTO & LOCALIZACIÓN -->
        <div class="mb-4">
            <h6 class="text-primary font-weight-bold pb-2 border-bottom d-flex align-items-center">
                <i class="bi bi-geo-alt mr-2"></i> Contacto & Ubicación
            </h6>
            <div class="form-row">
                <div class="col-md-4 form-group">
                    <label for="email" class="font-weight-semibold">E-mail Institucional</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        </div>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="form-control"
                            placeholder="contacto@neumolav.com"
                            :disabled="savingGeneral || !permissions.update"
                        >
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label for="telefono" class="font-weight-semibold">Teléfono / Celular</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                        </div>
                        <input
                            id="telefono"
                            v-model="form.telefono"
                            type="text"
                            class="form-control"
                            placeholder="Ej: +51 987654321"
                            :disabled="savingGeneral || !permissions.update"
                        >
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label for="pagina_web" class="font-weight-semibold">Página Web</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light"><i class="bi bi-globe"></i></span>
                        </div>
                        <input
                            id="pagina_web"
                            v-model="form.pagina_web"
                            type="text"
                            class="form-control"
                            placeholder="neumolav.com"
                            :disabled="savingGeneral || !permissions.update"
                        >
                    </div>
                </div>

                <div class="col-md-3 form-group">
                    <label for="pais" class="font-weight-semibold">País</label>
                    <input
                        id="pais"
                        v-model="form.pais"
                        type="text"
                        class="form-control"
                        placeholder="Perú"
                        :disabled="savingGeneral || !permissions.update"
                    >
                </div>

                <div class="col-md-2 form-group">
                    <label for="codigo_pais" class="font-weight-semibold">Código País</label>
                    <input
                        id="codigo_pais"
                        v-model="form.codigo_pais"
                        type="text"
                        class="form-control"
                        placeholder="+51"
                        :disabled="savingGeneral || !permissions.update"
                    >
                </div>

                <div class="col-md-2 form-group">
                    <label for="ubigeo" class="font-weight-semibold">Ubigeo</label>
                    <input
                        id="ubigeo"
                        v-model="form.ubigeo"
                        type="text"
                        class="form-control"
                        placeholder="150101"
                        :disabled="savingGeneral || !permissions.update"
                    >
                </div>

                <div class="col-md-5 form-group">
                    <label for="direccion" class="font-weight-semibold">Dirección</label>
                    <input
                        id="direccion"
                        v-model="form.direccion"
                        type="text"
                        class="form-control"
                        placeholder="Av. Principal 123, Consultorio 401"
                        :disabled="savingGeneral || !permissions.update"
                    >
                </div>
            </div>
        </div>

        <!-- SECCIÓN 3: FILOSOFÍA & IDENTIDAD INSTITUCIONAL -->
        <div class="mb-4">
            <h6 class="text-primary font-weight-bold pb-2 border-bottom d-flex align-items-center">
                <i class="bi bi-quote mr-2"></i> Identidad Corporativa & Filosofía
            </h6>
            <div class="form-row">
                <div class="col-md-6 form-group">
                    <label for="slogan" class="font-weight-semibold">Slogan</label>
                    <input
                        id="slogan"
                        v-model="form.slogan"
                        type="text"
                        class="form-control"
                        placeholder="Cuidando tu salud respiratoria con excelencia"
                        :disabled="savingGeneral || !permissions.update"
                    >
                </div>

                <div class="col-md-6 form-group">
                    <label for="frase" class="font-weight-semibold">Frase de la Empresa</label>
                    <input
                        id="frase"
                        v-model="form.frase"
                        type="text"
                        class="form-control"
                        placeholder="Compromiso y calidez en cada diagnóstico"
                        :disabled="savingGeneral || !permissions.update"
                    >
                </div>

                <div class="col-12 form-group">
                    <label for="descripcion" class="font-weight-semibold">Descripción General</label>
                    <textarea
                        id="descripcion"
                        v-model="form.descripcion"
                        rows="2"
                        class="form-control"
                        placeholder="Centro médico especializado en diagnóstico y tratamiento de patologías respiratorias..."
                        :disabled="savingGeneral || !permissions.update"
                    ></textarea>
                </div>

                <div class="col-md-6 form-group">
                    <label for="mision" class="font-weight-semibold">Misión</label>
                    <textarea
                        id="mision"
                        v-model="form.mision"
                        rows="2"
                        class="form-control"
                        placeholder="Brindar atención neumológica integral con tecnología avanzada..."
                        :disabled="savingGeneral || !permissions.update"
                    ></textarea>
                </div>

                <div class="col-md-6 form-group">
                    <label for="vision" class="font-weight-semibold">Visión</label>
                    <textarea
                        id="vision"
                        v-model="form.vision"
                        rows="2"
                        class="form-control"
                        placeholder="Ser líderes y referentes en salud respiratoria y bienestar..."
                        :disabled="savingGeneral || !permissions.update"
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 4: MAPA & CONFIGURACIÓN ADICIONAL -->
        <div class="mb-4">
            <h6 class="text-primary font-weight-bold pb-2 border-bottom d-flex align-items-center">
                <i class="bi bi-map mr-2"></i> Mapa de Ubicación & Preferencias
            </h6>
            <div class="form-row">
                <div class="col-md-4 form-group">
                    <label for="autocomplete" class="font-weight-semibold">Autocompletar en Formularios</label>
                    <select
                        id="autocomplete"
                        v-model="form.autocomplete"
                        class="form-control"
                        :disabled="savingGeneral || !permissions.update"
                    >
                        <option value="on">Habilitado (on)</option>
                        <option value="off">Deshabilitado (off)</option>
                    </select>
                    <small class="form-text text-muted">Controla si los navegadores sugieren autocompletado en los formularios.</small>
                </div>

                <div class="col-md-8 form-group">
                    <label for="iframe_location" class="font-weight-semibold">Código Iframe (Google Maps)</label>
                    <textarea
                        id="iframe_location"
                        v-model="form.iframe_location"
                        rows="3"
                        class="form-control font-monospace small"
                        placeholder='<iframe src="https://www.google.com/maps/embed?..." width="600" height="450" ...></iframe>'
                        :disabled="savingGeneral || !permissions.update"
                    ></textarea>
                </div>

                <!-- Preview del Mapa -->
                <div v-if="form.iframe_location" class="col-12 mt-2">
                    <div class="card bg-light border p-2">
                        <small class="text-muted font-weight-bold mb-2 d-block">
                            <i class="bi bi-eye mr-1"></i> Vista Previa del Mapa
                        </small>
                        <div class="map-preview-container" v-html="form.iframe_location"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTÓN DE ENVÍO -->
        <div class="d-flex justify-content-end border-top pt-3">
            <button
                v-if="permissions.update"
                type="submit"
                class="btn btn-primary px-4 py-2"
                :disabled="savingGeneral"
            >
                <span v-if="savingGeneral" class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                <i v-else class="bi bi-floppy mr-2"></i>
                Guardar Información
            </button>
        </div>
    </form>
</template>

<style scoped>
.font-weight-semibold {
    font-weight: 600;
}
.map-preview-container :deep(iframe) {
    width: 100% !important;
    height: 250px !important;
    border: 0;
    border-radius: 6px;
}
</style>
