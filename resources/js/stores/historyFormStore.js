import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL;

// Campos base del formulario (los que no tienen valor por defecto)
const defaultForm = () => ({
    // Filiación
    id: null,
    id_td: '',
    dni: '',
    nombres: '',
    fecha_nacimiento: '',     // ISO yyyy-mm-dd
    id_sexo: '',
    telefono: '',
    id_gs: '',
    id_tipo_atencion: 1,
    // Ubicación
    isForeign: false,
    ubigeo_nacimiento: '',
    ubigeo_extranjero: '',
    ubigeo_residencia: '',
    // Socio
    id_gi: '',
    id_ocupacion: '',
    id_estado: '',
    // Antecedentes clínicos
    cirugias: '', 
    transfusiones: '', 
    traumatismos: '',
    hospitalizaciones: '', 
    drogas: '', 
    antecedentes: '',
    estadobasal: '', 
    medicacion: '', 
    animales: '', 
    otros: '',
    // Enfermedades respiratorias
    asmabronquial: '', 
    epoc: '', 
    epid: '', 
    tuberculosis: '',
    cancerpulmon: '', 
    efusionpleural: '', 
    neumonias: '',
    // Tabaquismo
    tabaquismo: '', 
    id_ct: '', 
    cig: 0, 
    aniosfum: 0, 
    result: 0,
    contactotbc: '', 
    exposicionbiomasa: '',
    // Relato
    motivoconsulta: '', 
    sintomascardinales: '',
    te: '', 
    fi: '', 
    c: '', 
    relatocronologico: '',
});

export const useHistoryFormStore = defineStore('historyForm', {
    state: () => ({
        form: defaultForm(),
        errors: {},               // { field: [messages] }
        submitting: false,
        isEdit: false,

        // Catálogos (vienen desde Blade)
        catalog: {
            documentTypes:      [],
            sexes:              [],
            bloodGroups:        [],
            degrees:            [],
            maritalStatuses:    [],
            smokingTypes:       [],
            tipoAtencion:       [],
        },

        // Permisos de rol
        canSeeClinical: false,    // @role('administrador|especialista')
    }),

    getters: {
        // IPA calculado reactivamente
        ipa: (s) => {
            const cig = parseFloat(s.form.cig) || 0;
            const af = parseFloat(s.form.aniosfum) || 0;
            if (cig <= 0 || af <= 0) return 0;
            return +((cig * af) / 20).toFixed(2);
        },
        isNew: (s) => !s.form.id,
    },

    actions: {
        hydrate(payload) {
            // payload: { form?, catalog, isEdit, canSeeClinical }
            if (payload.form) {
                Object.assign(this.form, payload.form);
                this.isEdit = !!payload.form.id;
                this.form.isForeign = !!payload.form.ubigeo_extranjero;
            }
            if (payload.catalog) Object.assign(this.catalog, payload.catalog);
            if (typeof payload.canSeeClinical === 'boolean') {
                this.canSeeClinical = payload.canSeeClinical;
            }
        },

        setField(name, value) {
            this.form[name] = value;
            // Limpia error del campo al escribir
            if (this.errors[name]) delete this.errors[name];
        },

        clearErrors() { this.errors = {}; },

        async submit() {
            this.submitting = true;
            this.clearErrors();
            try {
                // Aseguramos que result del IPA esté actualizado
                this.form.result = this.ipa;

                const { data } = await axios.post(`${API()}/histories/store`, this.form);

                if (data.status) {
                    return { ok: true, route: data.route, messages: data.messages };
                }
                return { ok: false, messages: data.messages };
            } catch (error) {
                if (error.response?.status === 422 && error.response.data.errors) {
                    this.errors = error.response.data.errors;
                    return { ok: false, validation: true };
                }
                return {
                    ok: false,
                    messages: error.response?.data?.messages || 'Error del servidor.',
                };
            } finally {
                this.submitting = false;
            }
        },

        // Búsquedas AJAX (reemplazan select2)
        async searchUbigeo(query) {
            const { data } = await axios.post(`${API()}/histories/location`, { q: query });
            return data.map(item => ({ value: item.ubigeo, label: item.ubigeo }));
        },

        async searchOccupation(query) {
            const { data } = await axios.post(`${API()}/histories/occupation`, { q: query });
            return data.map(item => ({ value: item.ocupacion, label: item.ocupacion }));
        },

        async searchDni(dni) {
            const form = new FormData();
            form.append('dni', dni);
            const { data } = await axios.post(`${API()}/histories/dni`, form);
            let payload = data;
            if (typeof payload === 'string') {
                try { payload = JSON.parse(payload); } catch { /* noop */ }
            }
            return payload;
        },

        async createOccupation(descripcion) {
            const { data } = await axios.post(`${API()}/occupations/store`, {
                descripcion: descripcion.toUpperCase(),
                id: null,
            });
            return data;
        },
    },
});