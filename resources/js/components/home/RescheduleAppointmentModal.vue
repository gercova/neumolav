<script setup>
import { watch, onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { useCitasModalsStore } from '@/stores/citasModalsStore';
import { useCitasStore } from '@/stores/citasStore';

const modals = useCitasModalsStore();
const citas  = useCitasStore();
const { rescheduleVisible, reschedule, submitting } = storeToRefs(modals);

watch(rescheduleVisible, (v) => {
    document.body.classList.toggle('modal-open', v);
});
onBeforeUnmount(() => document.body.classList.remove('modal-open'));

const onBackdrop = (e) => {
    if (e.target.classList.contains('modal')) modals.closeReschedule();
};

const todayIso = new Date().toISOString().slice(0, 10);

const submit = async () => {
    const result = await modals.submitReschedule();
    if (result?.status) {
        modals.closeReschedule();
        window.alertNotify?.('success', result.messages);
        await citas.refresh();
    } else {
        window.Swal.fire('Aviso', result?.messages || 'No se pudo reagendar.', 'warning');
    }
};
</script>

<template>
    <Teleport to="body">
        <div v-if="rescheduleVisible" class="modal fade show d-block" tabindex="-1"
             style="background: rgba(0,0,0,.5);"
             @click="onBackdrop">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title">
                            <i class="bi bi-arrow-repeat text-warning"></i> Reagendar Cita Médica
                        </h5>
                        <button type="button" class="close" @click="modals.closeReschedule()">
                            <span>&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="modal-body p-4">
                            <div class="alert alert-light border mb-3">
                                <div class="small text-muted">Paciente:</div>
                                <div class="font-weight-bold text-dark h6 mb-1">{{ reschedule.patientName }}</div>
                                <div class="small text-muted">
                                    Fecha actual de la cita: <b>{{ reschedule.currentDateFormatted }}</b>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Nueva Fecha de Cita: <span class="text-danger">*</span></label>
                                <input v-model="reschedule.newDate" type="date" class="form-control"
                                       required :min="todayIso">
                                <small class="form-text text-muted">
                                    Al reagendar, se le asignará el turno FIFO correspondiente en la nueva fecha.
                                </small>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">
                                    Nueva Hora: <small class="text-muted">(Opcional)</small>
                                </label>
                                <input v-model="reschedule.newTime" type="time" class="form-control">
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold">
                                    Motivo del cambio: <small class="text-muted">(Opcional)</small>
                                </label>
                                <textarea v-model="reschedule.note" class="form-control" rows="2"
                                          placeholder="Ej. Solicitud del paciente, cruce de horarios, etc."></textarea>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" @click="modals.closeReschedule()">
                                Cerrar
                            </button>
                            <button type="submit" class="btn btn-warning" :disabled="submitting">
                                <span v-if="submitting" class="spinner-border spinner-border-sm mr-1"></span>
                                <i v-else class="bi bi-arrow-repeat"></i>
                                Confirmar Reagendamiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Teleport>
</template>