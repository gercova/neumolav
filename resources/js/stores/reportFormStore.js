import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL;

const defaultForm = () => ({
    reportId: null,
    id_historia: '',
    dni: '',
    antecedentes: '',
    historial_enfermedad: '',
    examen_fisico: '',
    examen_complementario: '',
    tratamiento: '',
    sugerencia: '',
});

export const useReportFormStore = defineStore('reportForm', {
    state: () => ({
        form: defaultForm(),

        // NUEVOS
        diagnostics: [],

        // EXISTENTES (solo se borran vía endpoint)
        existingDiagnostics: [],

        meta: {
            isEdit: false,
            canEdit: false,
            patient: { id: null, dni: '', nombres: '' },
            userName: '',
        },

        errors: {},
        submitting: false,
    }),

    actions: {
        hydrate(payload) {
            if (payload.form) Object.assign(this.form, payload.form);
            if (payload.meta) Object.assign(this.meta, payload.meta);
            if (payload.diagnostics) this.existingDiagnostics = payload.diagnostics;
        },

        setField(k, v) {
            this.form[k] = v;
            if (this.errors[k]) delete this.errors[k];
        },

        addDiagnostic(item) {
            if (!item?.id || this.diagnostics.some(d => d.id === item.id)) return;
            this.diagnostics.push({ id: item.id, descripcion: item.label || item.descripcion });
        },
        removeDiagnostic(i) { this.diagnostics.splice(i, 1); },

        async deleteExistingDiagnostic(index) {
            const item = this.existingDiagnostics[index];
            if (!item) return;
            const { data } = await axios.delete(`${API()}/rp-dx/delete/${item.id}`);
            if (data.status) this.existingDiagnostics.splice(index, 1);
            return data;
        },

        async searchDiagnostics(query) {
            const { data } = await axios.post(`${API()}/diagnostics/search`, { q: query });
            return (data || []).map(d => ({
                value: d.id,
                label: `${d.codigo || ''} ${d.descripcion}`.trim(),
            }));
        },

        async submit() {
            this.submitting = true;
            this.errors = {};
            try {
                const fd = new FormData();
                Object.entries(this.form).forEach(([k, v]) => {
                    if (v !== null && v !== undefined) fd.append(k, v);
                });
                this.diagnostics.forEach((d, i) => fd.append(`diagnostic_id[${i}]`, d.id));

                const { data } = await axios.post(`${API()}/reports/store`, fd);

                if (data.status) {
                    return {
                        ok: true,
                        route: data.route,
                        routePrint: data.route_print,
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