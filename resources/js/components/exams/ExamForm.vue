<script setup>
import { onMounted, computed } from 'vue';
import { useExamFormStore } from '@/stores/examFormStore';
import DiagnosticsPicker from './DiagnosticsPicker.vue';
import DrugsPicker from './DrugsPicker.vue';
import ImagesUploader from './ImagesUploader.vue';

const props = defineProps({ initialState: Object });
const store = useExamFormStore();
const form = computed(() => store.form);

onMounted(() => store.hydrate(props.initialState || {}));

const set = (k, v) => store.setField(k, v);

const submit = async () => {
    const result = await store.submit();
    if (result.ok) {
        const html = `
            <div class="text-center">
                <p>${result.message}</p>
                <a href="${result.printA4}" target="_blank" class="btn btn-outline-danger btn-sm mr-2">
                    <i class="bi bi-file-pdf"></i> A4
                </a>
                <a href="${result.printA5}" target="_blank" class="btn btn-outline-info btn-sm">
                    <i class="bi bi-file-pdf"></i> A5
                </a>
            </div>
        `;
        await window.Swal.fire({
            icon: 'success',
            title: '¡Operación exitosa!',
            html,
            showConfirmButton: true,
            confirmButtonText: 'Continuar',
        });
        window.location.href = result.route;
    } else if (result.validation) {
        window.Swal.fire({
            icon: 'warning',
            title: 'Campos requeridos',
            text: 'Corrija los campos marcados en rojo.',
        });
    } else {
        window.Swal.fire({ icon: 'error', title: 'Error', text: result.message });
    }
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="card-body">
            <!-- Encabezado: atendido por + fecha -->
            <div class="row">
                <div class="col-4">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Atendido por:</label>
                        <div class="col-sm-8">
                            <input :value="store.meta.userName" type="text" class="form-control form-control-sm" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Fecha:</label>
                        <div class="col-sm-8">
                            <input :value="store.meta.createdAt" type="text" class="form-control form-control-sm" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <!-- Paciente + Tipo consulta -->
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="font-weight-bold">DNI :: NOMBRES</label>
                        <input :value="`${store.meta.patient.dni} :: ${store.meta.patient.nombres}`"
                               type="text" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label class="font-weight-bold">Tipo consulta</label>
                        <select v-model="form.id_tipo" class="form-control" :class="{ 'is-invalid': store.errors.id_tipo }" required>
                            <option value="">-- Seleccione --</option>
                            <option v-for="t in store.catalog.examTypes" :key="t.id" :value="t.id">{{ t.descripcion }}</option>
                        </select>
                        <div class="invalid-feedback">{{ store.errors.id_tipo?.[0] }}</div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════
                 SECCIÓN CLÍNICA (solo admin/especialista)
            ═══════════════════════════════════════════ -->
            <template v-if="store.meta.canEdit">
                <hr>

                <!-- EXAMEN FÍSICO -->
                <div class="card card-info">
                    <div class="card-header"><h5 class="card-title">Examen físico</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2"><Field v-model="form.ta"  label="TA" /></div>
                            <div class="col-2"><Field v-model="form.fc"  label="FC" /></div>
                            <div class="col-2"><Field v-model="form.rf"  label="RF" /></div>
                            <div class="col-2"><Field v-model="form.so2" label="SO2" /></div>
                            <div class="col-1"><Field v-model="form.peso"  label="Peso (kg)" /></div>
                            <div class="col-1"><Field v-model="form.talla" label="Talla (m)" /></div>
                            <div class="col-1">
                                <div class="form-group">
                                    <label class="font-weight-bold">IMC</label>
                                    <input :value="store.imc" type="text" class="form-control form-control-sm bg-light" readonly>
                                </div>
                            </div>
                            <div class="col-4"><Field v-model="form.pym" label="PYM" /></div>
                            <div class="col-4"><Field v-model="form.typ" label="TYP" /></div>
                            <div class="col-4"><Field v-model="form.cv"  label="CV" /></div>
                            <div class="col-4"><Field v-model="form.abdomen"         label="Abdomen" /></div>
                            <div class="col-4"><Field v-model="form.hemolinfopoyetico" label="Hemolinfopoyético" /></div>
                            <div class="col-4"><Field v-model="form.tcs"             label="TCS" /></div>
                            <div class="col-4"><Field v-model="form.neurologico"     label="Neurológico" /></div>
                        </div>
                    </div>
                </div>

                <!-- EXAMEN AUXILIAR -->
                <div class="card card-info">
                    <div class="card-header"><h5 class="card-title">Examen auxiliar</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4"><Field v-model="form.hemograma"         label="Hemograma" /></div>
                            <div class="col-4"><Field v-model="form.bioquimico"        label="Bioquímico" /></div>
                            <div class="col-4"><Field v-model="form.perfilhepatico"    label="Perfil hepático" /></div>
                            <div class="col-4"><Field v-model="form.perfilcoagulacion" label="Perfil coagulación" /></div>
                            <div class="col-4"><Field v-model="form.perfilreumatologico" label="Perfil reumatológico" /></div>
                        </div>
                    </div>
                </div>

                <!-- MICROBIOLOGÍA -->
                <div class="card card-info">
                    <div class="card-header"><h5 class="card-title">Microbiología</h5></div>
                    <div class="card-body">
                        <RowTextarea v-model="form.orina"  label="Orina" />
                        <RowTextarea v-model="form.sangre" label="Sangre" />
                        <RowTextarea v-model="form.esputo" label="Esputo" />
                        <RowTextarea v-model="form.heces"  label="Heces" />
                        <RowTextarea v-model="form.lcr"    label="LCR" />
                    </div>
                </div>

                <!-- LÍQUIDO PLEURAL -->
                <div class="card card-info">
                    <div class="card-header"><h5 class="card-title">Líquido pleural</h5></div>
                    <div class="card-body">
                        <RowTextarea v-model="form.citoquimico" label="Citoquímico" />
                        <RowTextarea v-model="form.adalp"       label="ADA" />
                        <RowTextarea v-model="form.paplp"       label="PAP" />
                        <RowTextarea v-model="form.bclp"        label="Block Cell" />
                        <RowTextarea v-model="form.cgchlp"      label="Cultivo GCH" />
                        <RowTextarea v-model="form.cbklp"       label="Cultivo BK" />
                    </div>
                </div>

                <!-- ASPIRADO BRONQUIAL -->
                <div class="card card-info">
                    <div class="card-header"><h5 class="card-title">Aspirado bronquial</h5></div>
                    <div class="card-body">
                        <RowTextarea v-model="form.bkdab" label="BK Directo" />
                        <RowTextarea v-model="form.bkcab" label="BK Cultivo" />
                        <RowTextarea v-model="form.cgchab" label="Cultivo GCH" />
                        <RowTextarea v-model="form.papab" label="PAP" />
                        <RowTextarea v-model="form.bcab"  label="Block Cell" />
                    </div>
                </div>

                <!-- BIOPSIA -->
                <div class="card card-info">
                    <div class="card-header"><h5 class="card-title">Biopsia pleuro pulmonar</h5></div>
                    <div class="card-body">
                        <RowTextarea v-model="form.pulmon"    label="Pulmón" />
                        <RowTextarea v-model="form.pleurabpp" label="Pleura" />
                    </div>
                </div>

                <!-- FUNCIÓN PULMONAR / NUCLEAR -->
                <div class="card card-info">
                    <div class="card-body">
                        <RowTextarea v-model="form.funcionpulmonar" label="Función pulmonar" />
                        <RowTextarea v-model="form.medicinanuclear" label="Medicina nuclear" />
                        <RowTextarea v-model="form.otros"           label="Otros" />
                    </div>
                </div>

                <!-- DIAGNÓSTICOS -->
                <DiagnosticsPicker />

                <!-- TRATAMIENTO -->
                <DrugsPicker />

                <!-- IMÁGENES -->
                <ImagesUploader />
            </template>

            <template v-else>
                <p class="alert alert-danger mb-0">
                    No tienes permisos para {{ store.meta.isEdit ? 'editar' : 'crear' }} exámenes. Consulta con el administrador.
                </p>
            </template>
        </div>

        <!-- Footer -->
        <div class="card-footer" v-if="store.meta.canEdit">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4" :disabled="store.submitting">
                    <span v-if="store.submitting" class="spinner-border spinner-border-sm mr-1"></span>
                    <i v-else class="fas fa-save mr-1"></i> Guardar
                </button>
            </div>
        </div>
    </form>
</template>

<script>
// Componentes inline para no generar 15 archivos pequeños
export default {
    components: {
        Field: {
            props: ['modelValue', 'label'],
            emits: ['update:modelValue'],
            template: `
                <div class="form-group">
                    <label class="font-weight-bold">{{ label }}</label>
                    <input :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
                           type="text" class="form-control form-control-sm">
                </div>
            `,
        },
        RowTextarea: {
            props: ['modelValue', 'label'],
            emits: ['update:modelValue'],
            template: `
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label font-weight-bold">{{ label }}:</label>
                    <div class="col-sm-10">
                        <textarea :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
                                  class="form-control form-control-sm" rows="1"></textarea>
                    </div>
                </div>
            `,
        },
    },
};
</script>