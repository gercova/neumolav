import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => (window.API_BASE_URL || '/admin').replace(/\/+$/, '');

const defaultQuick = () => ({
    id_td: 1,
    dni: '',
    nombres: '',
    fecha_nacimiento: '',
    id_sexo: '',
    telefono: '',
    fecha_cita: '',
    id_tipo_atencion: 1,
});

export const useCitasModalsStore = defineStore('citasModals', {
    state: () => ({
        // ── SCHEDULE ──────────────────────────────
        scheduleVisible: false,
        scheduleTab: 'search',       // 'search' | 'quick'
        scheduleDefaultDate: '',
        submitting: false,
        errors: {},

        // Búsqueda de paciente existente
        searchQuery: '',
        searchLoading: false,
        searchResults: [],
        selectedPatient: null,
        existingTipoAtencion: 2,
        existingDate: '',

        // Registro rápido
        quick: defaultQuick(),
        reniecLoading: false,
        reniecFeedback: '',
        quickFeedback: '',

        // ── RESCHEDULE ────────────────────────────
        rescheduleVisible: false,
        reschedule: {
            id: null,
            patientName: '',
            currentDateFormatted: '',
            newDate: '',
            newTime: '',
            note: '',
        },
    }),

    actions: {
        /* ═══════════════ SCHEDULE ═══════════════ */
        openSchedule(defaultDate = '') {
            this.scheduleVisible        = true;
            this.scheduleTab            = 'search';
            this.scheduleDefaultDate    = defaultDate;
            this.existingDate           = defaultDate;
            this.searchQuery            = '';
            this.searchResults          = [];
            this.selectedPatient        = null;
            this.existingTipoAtencion   = 2;
            this.quick                  = { ...defaultQuick(), fecha_cita: defaultDate };
            this.errors                 = {};
            this.reniecFeedback         = '';
            this.quickFeedback          = '';
        },

        closeSchedule() {
            this.scheduleVisible = false;
        },

        setScheduleTab(tab) {
            this.scheduleTab = tab;
        },

        async searchPatients(query) {
            if (!query || query.length < 2) {
                this.searchResults = [];
                return;
            }
            this.searchLoading = true;
            try {
                const { data } = await axios.get(`${API()}/citas/search-patients`, {
                    params: { q: query },
                });
                this.searchResults = data || [];
            } finally {
                this.searchLoading = false;
            }
        },

        selectPatient(p) {
            this.selectedPatient    = p;
            this.searchResults      = [];
            this.searchQuery        = '';
        },

        clearSelectedPatient() {
            this.selectedPatient        = null;
            this.existingTipoAtencion   = 2;
        },

        async submitExisting() {
            if (!this.selectedPatient) {
                return { status: false, messages: 'Seleccione un paciente.' };
            }
            this.submitting = true;
            this.errors = {};
            try {
                const { data } = await axios.post(`${API()}/citas/store`, {
                    id_historia:      this.selectedPatient.id,
                    fecha_cita:       this.existingDate,
                    id_tipo_atencion: this.existingTipoAtencion,
                });
                return data;
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                    return { status: false, messages: error.response.data.messages };
                }
                return { status: false, messages: 'Error del servidor.' };
            } finally {
                this.submitting = false;
            }
        },

        async searchReniec() {
            const dni = (this.quick.dni || '').trim();
            if (dni.length !== 8) {
                this.reniecFeedback = 'Ingrese un DNI válido de 8 dígitos.';
                return;
            }
            this.reniecLoading = true;
            this.reniecFeedback = '';
            try {
                const fd = new FormData();
                fd.append('dni', dni);
                const { data } = await axios.post(`${API()}/histories/dni`, fd);
                let payload = data;
                if (typeof payload === 'string') {
                    try { payload = JSON.parse(payload); } catch (_) {}
                }
                if (payload?.first_name || payload?.nombres) {
                    const full = [
                        payload.first_name  || payload.nombres            || '',
                        payload.first_last_name  || payload.apellido_paterno || '',
                        payload.second_last_name || payload.apellido_materno || '',
                    ].join(' ').trim().toUpperCase();
                    this.quick.nombres = full;
                    window.alertNotify?.('success', 'Datos obtenidos de RENIEC');
                } else {
                    this.reniecFeedback = 'No encontrado en RENIEC. Ingrese manualmente.';
                }
            } catch {
                this.reniecFeedback = 'Servicio RENIEC no disponible.';
            } finally {
                this.reniecLoading = false;
            }
        },

        async submitQuick() {
            this.submitting     = true;
            this.errors         = {};
            this.quickFeedback  = '';
            try {
                const { data }  = await axios.post(`${API()}/citas/quick-patient`, this.quick);
                return data;
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                    this.quickFeedback = error.response.data.messages || 'Verifique los campos.';
                    return { status: false, messages: this.quickFeedback };
                }
                return { status: false, messages: 'Error del servidor.' };
            } finally {
                this.submitting = false;
            }
        },

        /* ═══════════════ RESCHEDULE ═══════════════ */
        openReschedule(cita) {
            this.rescheduleVisible = true;
            this.reschedule = {
                id:                    cita.id,
                patientName:           cita.nombres || '',
                currentDateFormatted:  cita.fecha_formato || '',
                newDate:               cita.fecha_cita || '',
                newTime:               cita.hora_raw || '',
                note:                  '',
            };
        },

        closeReschedule() {
            this.rescheduleVisible = false;
        },

        async submitReschedule() {
            if (!this.reschedule.newDate) {
                return { status: false, messages: 'Seleccione una fecha.' };
            }
            this.submitting = true;
            try {
                const { data } = await axios.post(
                    `${API()}/citas/${this.reschedule.id}/reschedule`,
                    {
                        fecha_cita: this.reschedule.newDate,
                        hora_cita:  this.reschedule.newTime || null,
                        motivo:     this.reschedule.note || null,
                    }
                );
                return data;
            } catch (error) {
                return {
                    status: false,
                    messages: error.response?.data?.messages || 'No se pudo reagendar.',
                };
            } finally {
                this.submitting = false;
            }
        },
    },
});