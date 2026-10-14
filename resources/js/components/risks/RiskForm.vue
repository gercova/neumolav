<script setup>
import { onMounted, computed } from 'vue';
import { useRiskFormStore } from '@/stores/riskFormStore';
import RiskRichTextarea from './RiskRichTextarea.vue';
import PreviousRisksPanel from './PreviousRisksPanel.vue';

const props = defineProps({ initialState: Object });
const store = useRiskFormStore();
const form = computed(() => store.form);

onMounted(() => store.hydrate(props.initialState || {}));

const set = (k, v) => store.setField(k, v);

const submit = async () => {
    const result = await store.submit();
    if (result.ok) {
        await window.Swal.fire({
            icon: 'success',
            title: result.message || 'Guardado correctamente',
            html: `
                <div class="text-center">
                    <p class="mb-3">¿Deseas imprimir el informe de riesgo?</p>
                    <a href="${result.routePrint}" target="_blank" class="btn btn-outline-danger">
                        <i class="fas fa-file-pdf"></i> Imprimir PDF
                    </a>
                </div>
            `,
            confirmButtonText: 'Aceptar',
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
            <!-- Atendido por -->
            <div class="row">
                <div class="col-4">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Atendido por:</label>
                        <div class="col-sm-8">
                            <input :value="store.meta.userName" type="text" class="form-control form-control-sm" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <!-- Paciente -->
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label class="font-weight-bold">DNI :: NOMBRES</label>
                        <input :value="`${store.meta.patient.dni} :: ${store.meta.patient.nombres}`"
                               type="text" class="form-control" readonly>
                    </div>
                </div>
            </div>
            <hr>

            <div class="row">
                <!-- ══════════════ SECCIÓN CLÍNICA ══════════════ -->
                <div class="col-7">
                    <template v-if="store.meta.canEdit">
                        <div class="card card-info">
                            <div class="card-header"><h5 class="card-title">Datos del informe</h5></div>
                            <div class="card-body">
                                <RiskRichTextarea
                                    :model-value="form.motivo"
                                    @update:model-value="v => set('motivo', v)"
                                    label="Motivo:"
                                />
                                <RiskRichTextarea
                                    :model-value="form.antecedente"
                                    @update:model-value="v => set('antecedente', v)"
                                    label="Antecedente:"
                                />
                                <RiskRichTextarea
                                    :model-value="form.sintomas"
                                    @update:model-value="v => set('sintomas', v)"
                                    label="Síntomas:"
                                />
                                <RiskRichTextarea
                                    :model-value="form.examen_fisico"
                                    @update:model-value="v => set('examen_fisico', v)"
                                    label="Examen físico:"
                                />
                                <RiskRichTextarea
                                    :model-value="form.examen_complementario"
                                    @update:model-value="v => set('examen_complementario', v)"
                                    label="Examen complementario:"
                                />
                                <RiskRichTextarea
                                    :model-value="form.riesgo_neumologico"
                                    @update:model-value="v => set('riesgo_neumologico', v)"
                                    label="Riesgo neumológico:"
                                />
                                <RiskRichTextarea
                                    :model-value="form.sugerencia"
                                    @update:model-value="v => set('sugerencia', v)"
                                    label="Sugerencia:"
                                />
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <p class="alert alert-danger mb-0">
                            No tienes permisos para {{ store.meta.isEdit ? 'editar' : 'crear' }} informes de riesgo.
                            Consulta con el administrador.
                        </p>
                    </template>
                </div>

                <!-- ══════════════ INFORMES ANTERIORES ══════════════ -->
                <div class="col-5">
                    <PreviousRisksPanel :history-id="store.meta.patient.id" />
                </div>
            </div>
        </div>

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