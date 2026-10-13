import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL;

const defaultForm = () => ({
    appointmentId: null,
    id_historia: '',
    dni: '',
    sintomas: '',
    plan: '',
    recomendaciones: '',
});

export const useAppointmentFormStore = defineStore('appointmentForm', {
    state: () => ({
        form: defaultForm(),

        // NUEVOS (a guardar en este submit)
        diagnostics: [],
        drugs: [],

        // EXISTENTES (ya guardados; solo se borran por endpoint)
        existingDiagnostics: [],
        existingMedications: [],

        meta: {
            isEdit: false,
            canEdit: false,
            patient: { id: null, dni: '', nombres: '' },
            userName: '',
            createdAt: '',
        },

        errors: {},
        submitting: false,
    }),

    actions: {
        hydrate(payload) {
            if (payload.form) Object.assign(this.form, payload.form);
            if (payload.meta) Object.assign(this.meta, payload.meta);
            if (payload.diagnostics) this.existingDiagnostics = payload.diagnostics;
            if (payload.medications) this.existingMedications = payload.medications;
        },

        setField(k, v) {
            this.form[k] = v;
            if (this.errors[k]) delete this.errors[k];
        },

        // ─── NUEVOS ──────────────────────────────────────────
        addDiagnostic(item) {
            if (!item?.id || this.diagnostics.some(d => d.id === item.id)) return;
            this.diagnostics.push({ id: item.id, descripcion: item.label || item.descripcion });
        },
        removeDiagnostic(i) { this.diagnostics.splice(i, 1); },

        addDrug(item) {
            if (!item?.id) return;
            this.drugs.push({ id: item.id, descripcion: item.label || item.descripcion, rp: '' });
        },
        removeDrug(i) { this.drugs.splice(i, 1); },

        // ─── EXISTENTES ──────────────────────────────────────
        async deleteExistingDiagnostic(index) {
            const item = this.existingDiagnostics[index];
            if (!item) return;
            const { data } = await axios.delete(`${API()}/ap-dx/delete/${item.id}`);
            if (data.status) this.existingDiagnostics.splice(index, 1);
            return data;
        },

        async deleteExistingMedication(index) {
            const item = this.existingMedications[index];
            if (!item) return;
            const { data } = await axios.delete(`${API()}/ap-mx/delete/${item.id}`);
            if (data.status) this.existingMedications.splice(index, 1);
            return data;
        },

        // ─── BÚSQUEDAS ───────────────────────────────────────
        async searchDiagnostics(query) {
            const { data } = await axios.post(`${API()}/diagnostics/search`, { q: query });
            return (data || []).map(d => ({
                value: d.id,
                label: `${d.codigo || ''} ${d.descripcion}`.trim(),
            }));
        },
        async searchDrugs(query) {
            const { data } = await axios.post(`${API()}/drugs/search`, { q: query });
            return (data || []).map(d => ({ value: d.id, label: d.descripcion }));
        },

        // ─── SUBMIT ──────────────────────────────────────────
        async submit() {
            this.submitting = true;
            this.errors = {};
            try {
                const fd = new FormData();
                Object.entries(this.form).forEach(([k, v]) => {
                    if (v !== null && v !== undefined) fd.append(k, v);
                });
                this.diagnostics.forEach((d, i) => fd.append(`diagnostic_id[${i}]`, d.id));
                this.drugs.forEach((d, i) => {
                    fd.append(`drug_id[${i}]`, d.id);
                    fd.append(`description[${i}]`, d.rp || '');
                });

                const { data } = await axios.post(`${API()}/appointments/store`, fd);

                if (data.status) {
                    return {
                        ok: true,
                        route: data.route,
                        printA4: data.print_a4,
                        printA5: data.print_a5,
                        message: data.messages,
                    };
                }
                return { ok: false, message: data.messages };
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                    return { ok: false, validation: true };
                }
                return {
                    ok: false,
                    message: error.response?.data?.messages || 'Error del servidor.',
                };
            } finally {
                this.submitting = false;
            }
        },
    },
});