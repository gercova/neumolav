<script setup>
import { onMounted, computed } from 'vue';
import { useAppointmentFormStore } from '@/stores/appointmentFormStore';
import AppointmentDiagnosticsPicker from './AppointmentDiagnosticsPicker.vue';
import AppointmentDrugsPicker from './AppointmentDrugsPicker.vue';
import PreviousRecordsPanel from './PreviousRecordsPanel.vue';

const props = defineProps({ initialState: Object });
const store = useAppointmentFormStore();
const form  = computed(() => store.form);

onMounted(() => store.hydrate(props.initialState || {}));
const set   = (k, v) => store.setField(k, v);

const submit = async () => {
    const result = await store.submit();
    if (result.ok) {
        const html = `
            <div class="text-center">
                <p class="mb-3">Selecciona el formato de impresión:</p>
                <div class="d-flex justify-content-center gap-3">
                    <a class="btn btn-outline-info d-flex flex-column align-items-center p-3 mr-2"
                       href="${result.printA4}" target="_blank">
                        <i class="fas fa-file-pdf fa-2x mb-2"></i>
                        <span>Formato A4</span>
                        <small class="badge badge-light mt-1">Carta</small>
                    </a>
                    <a class="btn btn-outline-success d-flex flex-column align-items-center p-3"
                       href="${result.printA5}" target="_blank">
                        <i class="fas fa-file-pdf fa-2x mb-2"></i>
                        <span>Formato A5</span>
                        <small class="badge badge-light mt-1">Medio</small>
                    </a>
                </div>
            </div>
        `;
        await window.Swal.fire({
            icon: 'success',
            title: result.message || 'Guardado correctamente',
            html,
            confirmButtonText: 'Aceptar',
        });
        window.location.href = result.route;
    } else if (result.validation) {
        window.Swal.fire({
            icon: 'warning',
            title: 'Campos requeridos',
            text: 'Corrija los campos marcados en rojo.',
        });
        const firstKey = Object.keys(store.errors)[0];
        if (firstKey) {
            document.querySelector(`[name="${firstKey}"]`)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
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
                <div class="col-4" v-if="store.meta.isEdit">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Fecha:</label>
                        <div class="col-sm-8">
                            <input :value="store.meta.createdAt" type="text" class="form-control form-control-sm" readonly>
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
                        <!-- Datos de control -->
                        <div class="card card-info">
                            <div class="card-header"><h5 class="card-title">Datos de control</h5></div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label font-weight-bold">Síntomas:</label>
                                    <div class="col-sm-10">
                                        <textarea v-model="form.sintomas" class="form-control form-control-sm"
                                            :class="{ 'is-invalid': store.errors.sintomas }"
                                            name="sintomas" rows="2"></textarea>
                                        <div class="invalid-feedback">{{ store.errors.sintomas?.[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label font-weight-bold">Plan:</label>
                                    <div class="col-sm-10">
                                        <textarea v-model="form.plan" class="form-control form-control-sm"
                                            :class="{ 'is-invalid': store.errors.plan }"
                                            name="plan" rows="2"></textarea>
                                        <div class="invalid-feedback">{{ store.errors.plan?.[0] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Diagnósticos -->
                        <AppointmentDiagnosticsPicker />
                        <!-- Tratamiento -->
                        <AppointmentDrugsPicker />
                    </template>
                    <template v-else>
                        <p class="alert alert-danger mb-0">
                            No tienes permisos para {{ store.meta.isEdit ? 'editar' : 'crear' }} controles.
                            Consulta con el administrador.
                        </p>
                    </template>
                </div>
                <!-- ══════════════ RECETAS ANTERIORES ══════════════ -->
                <div class="col-5">
                    <PreviousRecordsPanel :dni="store.meta.patient.dni" />
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