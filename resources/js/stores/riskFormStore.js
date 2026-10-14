import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL;

const defaultForm = () => ({
    riskId: null,
    id_historia: '',
    dni: '',
    motivo: '',
    antecedente: '',
    sintomas: '',
    examen_fisico: '',
    examen_complementario: '',
    riesgo_neumologico: '',
    sugerencia: '',
});

export const useRiskFormStore = defineStore('riskForm', {
    state: () => ({
        form: defaultForm(),

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
        },

        setField(k, v) {
            this.form[k] = v;
            if (this.errors[k]) delete this.errors[k];
        },

        async submit() {
            this.submitting = true;
            this.errors = {};
            try {
                const fd = new FormData();
                Object.entries(this.form).forEach(([k, v]) => {
                    if (v !== null && v !== undefined) fd.append(k, v);
                });

                const { data } = await axios.post(`${API()}/risks/store`, fd);

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