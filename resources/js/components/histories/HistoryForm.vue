<script setup>
import { onMounted, ref, computed, watch } from 'vue';
import { useHistoryFormStore } from '@/stores/historyFormStore';
import SearchSelect from './SearchSelect.vue';
import DniSearchField from './DniSearchField.vue';
import DatePickerField from './DatePickerField.vue';
import ChipTipoAtencion from './ChipTipoAtencion.vue';
import NewOccupationModal from './NewOccupationModal.vue';

const props = defineProps({ initialState: Object });
const store = useHistoryFormStore();
const form = computed(() => store.form);
const showOccupationModal = ref(false);

onMounted(() => {
    store.hydrate(props.initialState || {});
});

const set = (field, value) => store.setField(field, value);

const onDniFetched = (fullName) => set('nombres', fullName);

const onOccupationCreated = (item) => {
    const nombre = item?.ocupacion || '';
    set('id_ocupacion', nombre);
};

const submit = async () => {
    const result = await store.submit();
    if (result.ok) {
        await window.Swal.fire({
            icon: 'success',
            title: '¡Operación exitosa!',
            text: result.messages,
            confirmButtonColor: '#007bff',
        });
        window.location.href = result.route;
    } else if (result.validation) {
        window.Swal.fire({
            icon: 'warning',
            title: 'Campos requeridos',
            text: 'Corrija los campos marcados en rojo.',
        });
        // Scroll al primer error
        const firstKey = Object.keys(store.errors)[0];
        if (firstKey) {
            document.querySelector(`[name="${firstKey}"]`)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    } else {
        window.Swal.fire({ icon: 'error', title: 'Error', text: result.messages });
    }
};
</script>

<template>
    <form @submit.prevent="submit" novalidate>
        <div class="card-body">
            <!-- ═══════════════════════════════════════════
                 FILIACIÓN PRINCIPAL
            ═══════════════════════════════════════════ -->
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Tipo Documento <span class="text-danger">*</span></label>
                        <select v-model="form.id_td" class="form-control" :class="{ 'is-invalid': store.errors.id_td }" name="id_td">
                            <option value="">-- Seleccione --</option>
                            <option v-for="t in store.catalog.documentTypes" :key="t.id" :value="t.id">{{ t.descripcion }}</option>
                        </select>
                        <div class="invalid-feedback">{{ store.errors.id_td?.[0] }}</div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <DniSearchField
                        :model-value="form.dni"
                        :tipo-documento="form.id_td"
                        :maxlength="form.id_td == 1 ? 8 : 15"
                        @update:model-value="v => set('dni', v)"
                        @name-fetched="onDniFetched"
                    />
                </div>

                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label class="font-weight-bold">Nombres y Apellidos <span class="text-danger">*</span></label>
                        <input v-model="form.nombres" type="text" class="form-control text-uppercase"
                               :class="{ 'is-invalid': store.errors.nombres }" name="nombres"
                               placeholder="NOMBRES COMPLETOS">
                        <div class="invalid-feedback">{{ store.errors.nombres?.[0] }}</div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <DatePickerField
                        :model-value="form.fecha_nacimiento"
                        @update:model-value="v => set('fecha_nacimiento', v)"
                    />
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Edad</label>
                        <div class="input-group">
                            <input :value="store.ipa && form.fecha_nacimiento ? '' : ''" type="text"
                                   class="form-control bg-light text-center font-weight-bold" readonly>
                            <div class="input-group-append"><span class="input-group-text">años</span></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Sexo <span class="text-danger">*</span></label>
                        <select v-model="form.id_sexo" class="form-control" :class="{ 'is-invalid': store.errors.id_sexo }" name="id_sexo">
                            <option value="">-- Seleccione --</option>
                            <option v-for="s in store.catalog.sexes" :key="s.id" :value="s.id">{{ s.descripcion }}</option>
                        </select>
                        <div class="invalid-feedback">{{ store.errors.id_sexo?.[0] }}</div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Celular / Teléfono <span class="text-danger">*</span></label>
                        <input v-model="form.telefono" type="tel" class="form-control" maxlength="11"
                               :class="{ 'is-invalid': store.errors.telefono }" name="telefono"
                               placeholder="Ej: 987654321">
                        <div class="invalid-feedback">{{ store.errors.telefono?.[0] }}</div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Grupo sanguíneo <span class="text-danger">*</span></label>
                        <select v-model="form.id_gs" class="form-control" :class="{ 'is-invalid': store.errors.id_gs }" name="id_gs">
                            <option value="">-- Seleccione --</option>
                            <option v-for="b in store.catalog.bloodGroups" :key="b.id" :value="b.id">{{ b.descripcion }}</option>
                        </select>
                        <div class="invalid-feedback">{{ store.errors.id_gs?.[0] }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <ChipTipoAtencion :model-value="form.id_tipo_atencion"
                                      @update:model-value="v => set('id_tipo_atencion', v)" />
                </div>
            </div>
            <hr>

            <!-- ═══════════════════════════════════════════
                 UBICACIÓN Y PROCEDENCIA
            ═══════════════════════════════════════════ -->
            <div class="row align-items-center">
                <div class="col-md-2 col-sm-12">
                    <div class="form-group">
                        <label class="d-none d-md-block">&nbsp;</label>
                        <button v-if="!form.isForeign" type="button" class="btn btn-outline-warning btn-block"
                                @click="set('isForeign', true)">
                            <i class="fa fa-globe"></i> Extranjero
                        </button>
                        <button v-else type="button" class="btn btn-outline-success btn-block"
                                @click="set('isForeign', false); set('ubigeo_extranjero', '')">
                            <i class="fa fa-map-marker-alt"></i> Nacional (Perú)
                        </button>
                    </div>
                </div>
                <div class="col-md-5 col-sm-12">
                    <div v-if="!form.isForeign" class="form-group">
                        <label class="font-weight-bold">Lugar de Nacimiento (Perú)</label>
                        <SearchSelect
                            :model-value="form.ubigeo_nacimiento"
                            :initial-label="form.ubigeo_nacimiento"
                            :search-fn="store.searchUbigeo"
                            placeholder="-- Buscar ubigeo (Distrito, Provincia o Dpto) --"
                            @update:model-value="v => set('ubigeo_nacimiento', v)"
                        />
                    </div>
                    <div v-else class="form-group">
                        <label class="font-weight-bold">Lugar de Nacimiento (Extranjero)</label>
                        <input v-model="form.ubigeo_extranjero" type="text" class="form-control"
                               placeholder="PAÍS, REGIÓN, CIUDAD">
                    </div>
                </div>
                <div class="col-md-5 col-sm-12">
                    <div class="form-group">
                        <label class="font-weight-bold">Lugar de Residencia <span class="text-danger">*</span></label>
                        <SearchSelect
                            :model-value="form.ubigeo_residencia"
                            :initial-label="form.ubigeo_residencia"
                            :search-fn="store.searchUbigeo"
                            placeholder="-- Buscar ubigeo de residencia --"
                            @update:model-value="v => set('ubigeo_residencia', v)"
                        />
                        <div v-if="store.errors.ubigeo_residencia" class="text-danger small">
                            {{ store.errors.ubigeo_residencia[0] }}
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <!-- ═══════════════════════════════════════════
                 INSTRUCCIÓN / OCUPACIÓN / ESTADO CIVIL
            ═══════════════════════════════════════════ -->
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Grado instrucción <span class="text-danger">*</span></label>
                        <select v-model="form.id_gi" class="form-control" :class="{ 'is-invalid': store.errors.id_gi }" name="id_gi">
                            <option value="">-- Seleccione --</option>
                            <option v-for="g in store.catalog.degrees" :key="g.id" :value="g.id">{{ g.descripcion }}</option>
                        </select>
                        <div class="invalid-feedback">{{ store.errors.id_gi?.[0] }}</div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label class="font-weight-bold">Ocupación <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="flex-grow-1">
                                <SearchSelect
                                    :model-value="form.id_ocupacion"
                                    :initial-label="form.id_ocupacion"
                                    :search-fn="store.searchOccupation"
                                    placeholder="-- Buscar ocupación --"
                                    @update:model-value="v => set('id_ocupacion', v)"
                                />
                            </div>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-primary" @click="showOccupationModal = true">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div v-if="store.errors.id_ocupacion" class="text-danger small">
                            {{ store.errors.id_ocupacion[0] }}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Estado civil <span class="text-danger">*</span></label>
                        <select v-model="form.id_estado" class="form-control" :class="{ 'is-invalid': store.errors.id_estado }" name="id_estado">
                            <option value="">-- Seleccione --</option>
                            <option v-for="m in store.catalog.maritalStatuses" :key="m.id" :value="m.id">{{ m.descripcion }}</option>
                        </select>
                        <div class="invalid-feedback">{{ store.errors.id_estado?.[0] }}</div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════
                 SECCIÓN CLÍNICA (solo admin/especialista)
            ═══════════════════════════════════════════ -->
            <template v-if="store.canSeeClinical">
                <hr>
                <div class="row">
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.cirugias"          label="Cirugías" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.transfusiones"    label="Transfusiones sanguíneas" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.traumatismos"     label="Traumatismos" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.hospitalizaciones" label="Hospitalizaciones previas" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.drogas"           label="Consumo de drogas sociales" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.antecedentes"     label="Antecedentes familiares" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.estadobasal"      label="Estado basal" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.medicacion"       label="RAMS / Medicación" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.animales"         label="Animales en casa" /></div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Otros antecedentes</label>
                            <textarea v-model="form.otros" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <hr>

                <div class="row">
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.asmabronquial" label="Asma bronquial" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.epoc"          label="EPOC" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.epid"          label="EPID" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.tuberculosis"  label="Tuberculosis" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.cancerpulmon"  label="Cáncer al pulmón" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.efusionpleural" label="Efusión pleural" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.neumonias"     label="Neumonías" /></div>
                    <div class="col-md-4 col-sm-6"><FieldInput v-model="form.tabaquismo"    label="Tabaquismo" /></div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Tipo de consumo de tabaco</label>
                            <select v-model="form.id_ct" class="form-control" name="id_ct">
                                <option value="">-- Seleccione --</option>
                                <option v-for="t in store.catalog.smokingTypes" :key="t.id" :value="t.id">{{ t.consumo }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Cigarros x día</label>
                            <input v-model.number="form.cig" type="number" min="0" class="form-control" name="cig">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Años fumando</label>
                            <input v-model.number="form.aniosfum" type="number" min="0" class="form-control" name="aniosfum">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Resultado (IPA)</label>
                            <input :value="store.ipa" type="number" class="form-control bg-light font-weight-bold" readonly name="result">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6"><FieldInput v-model="form.contactotbc"       label="Contacto TBC / COVID" /></div>
                    <div class="col-md-3 col-sm-6"><FieldInput v-model="form.exposicionbiomasa" label="Exposición a biomasa" /></div>
                </div>
                <hr>

                <div class="row">
                    <div class="col-md-6 col-sm-12"><FieldInput v-model="form.motivoconsulta"     label="Motivo consulta" /></div>
                    <div class="col-md-6 col-sm-12"><FieldInput v-model="form.sintomascardinales" label="Síntomas cardinales" /></div>
                    <div class="col-md-4 col-sm-4"><FieldInput v-model="form.te" label="TE (Tiempo de Enfermedad)" /></div>
                    <div class="col-md-4 col-sm-4"><FieldInput v-model="form.fi" label="FI (Forma de Inicio)" /></div>
                    <div class="col-md-4 col-sm-4"><FieldInput v-model="form.c"  label="C (Curso)" /></div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Relato cronológico</label>
                            <textarea v-model="form.relatocronologico" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="card-footer bg-white border-top">
            <div class="d-flex justify-content-between align-items-center">
                <a href="/admin/histories/home" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Cancelar / Volver
                </a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="store.submitting">
                    <span v-if="store.submitting" class="spinner-border spinner-border-sm mr-1"></span>
                    <i v-else class="fas fa-save mr-1"></i> Guardar Historia Clínica
                </button>
            </div>
        </div>

        <NewOccupationModal
            v-if="showOccupationModal"
            @close="showOccupationModal = false"
            @created="onOccupationCreated"
        />
    </form>
</template>

<script>
// Pequeño componente inline para no crear 15 archivos
export default {
    components: {
        FieldInput: {
            props: ['modelValue', 'label'],
            emits: ['update:modelValue'],
            template: `
                <div class="form-group">
                    <label class="font-weight-bold">{{ label }}</label>
                    <input :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
                           type="text" class="form-control">
                </div>
            `,
        },
    },
};
</script>