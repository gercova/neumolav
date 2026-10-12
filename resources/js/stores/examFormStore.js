import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL;

const defaultForm = () => ({
    examId: null,
    id_historia: '',
    dni: '',
    id_tipo: '',
    // Examen físico
    ta: '', 
    fc: '', 
    rf: '', 
    so2: '', 
    peso: '', 
    talla: '', 
    imc: '',
    pym: '', 
    typ: '', 
    cv: '', 
    abdomen: '', 
    hemolinfopoyetico: '', 
    tcs: '', 
    neurologico: '',
    // Examen auxiliar
    hemograma: '', 
    bioquimico: '', 
    perfilhepatico: '', 
    perfilcoagulacion: '', 
    perfilreumatologico: '',
    // Microbiología
    orina: '', 
    sangre: '', 
    esputo: '', 
    heces: '', 
    lcr: '',
    // Líquido pleural
    citoquimico: '', 
    adalp: '', 
    paplp: '', 
    bclp: '', 
    cgchlp: '', 
    cbklp: '',
    // Aspirado bronquial
    bkdab: '', 
    bkcab: '', 
    cgchab: '', 
    papab: '', 
    bcab: '',
    // Biopsia
    pulmon: '', 
    pleurabpp: '',
    // Función pulmonar / medicina nuclear
    funcionpulmonar: '', 
    medicinanuclear: '', 
    otros: '',
    // Recomendaciones
    recomendaciones: '',
});

export const useExamFormStore = defineStore('examForm', {
    state: () => ({
        form: defaultForm(),
        // Listas dinámicas
        diagnostics: [],      // [{ id, descripcion }]
        drugs: [],            // [{ id, descripcion, rp }]
        images: [],           // [{ id?, file?, date, preview, isNew, removed? }]
        removedImageIds: [],

        catalog: {
            examTypes: [],    // [{ id, descripcion }]
        },

        meta: {
            isEdit: false,
            canEdit: false,   // @role('administrador|especialista')
            patient: { dni: '', nombres: '', id: null },
            userName: '',
            createdAt: '',
        },

        errors: {},
        submitting: false,
    }),

    getters: {
        imc: (s) => {
            const p = parseFloat(s.form.peso);
            const t = parseFloat(s.form.talla);
            if (!p || !t || t <= 0) return '';
            return (p / (t * t)).toFixed(2);
        },
    },

    actions: {
        hydrate(payload) {
            if (payload.form) Object.assign(this.form, payload.form);
            if (payload.catalog) Object.assign(this.catalog, payload.catalog);
            if (payload.meta) Object.assign(this.meta, payload.meta);
            if (payload.diagnostics) this.diagnostics = payload.diagnostics;
            if (payload.drugs) this.drugs = payload.drugs;
            if (payload.images) this.images = payload.images;
        },

        setField(k, v) {
            this.form[k] = v;
            if (this.errors[k]) delete this.errors[k];
        },

        // Diagnósticos
        addDiagnostic(item) {
            if (!item?.id) return;
            if (this.diagnostics.some(d => d.id === item.id)) return;
            this.diagnostics.push({ id: item.id, descripcion: item.label || item.descripcion });
        },
        removeDiagnostic(index) {
            this.diagnostics.splice(index, 1);
        },

        // Fármacos
        addDrug(item) {
            if (!item?.id) return;
            this.drugs.push({
                id: item.id,
                descripcion: item.label || item.descripcion,
                rp: '',
            });
        },
        removeDrug(index) {
            this.drugs.splice(index, 1);
        },

        // Imágenes
        addImage(file) {
            this.images.push({
                file,
                date: new Date().toISOString().slice(0, 10),
                preview: URL.createObjectURL(file),
                isNew: true,
            });
        },
        removeImage(index) {
            const img = this.images[index];
            if (img?.id) this.removedImageIds.push(img.id);
            if (img?.preview) URL.revokeObjectURL(img.preview);
            this.images.splice(index, 1);
        },

        async searchDiagnostics(query) {
            const { data } = await axios.post(`${API()}/diagnostics/search`, { q: query });
            return (data || []).map(d => ({ value: d.id, label: `${d.codigo || ''} ${d.descripcion}`.trim() }));
        },
        async searchDrugs(query) {
            const { data } = await axios.post(`${API()}/drugs/search`, { q: query });
            return (data || []).map(d => ({ value: d.id, label: d.descripcion }));
        },

        async submit() {
            this.submitting = true;
            this.errors = {};
            try {
                this.form.imc = this.imc;

                const fd = new FormData();
                // Campos escalares
                Object.entries(this.form).forEach(([k, v]) => {
                    if (v !== null && v !== undefined) fd.append(k, v);
                });
                // Diagnósticos
                this.diagnostics.forEach((d, i) => fd.append(`diagnostic_id[${i}]`, d.id));
                // Fármacos
                this.drugs.forEach((d, i) => {
                    fd.append(`drug_id[${i}]`, d.id);
                    fd.append(`description[${i}]`, d.rp || '');
                });
                // Imágenes nuevas
                const newImages = this.images.filter(i => i.isNew && i.file);
                newImages.forEach((img, i) => {
                    fd.append(`img[${i}]`, img.file);
                    fd.append(`dateImg[${i}]`, img.date);
                });
                // Imágenes eliminadas (por si el backend las procesa)
                this.removedImageIds.forEach((id, i) => fd.append(`removed_img[${i}]`, id));

                const { data } = await axios.post(`${API()}/exams/store`, fd, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });

                if (data.status) {
                    return {
                        ok: true,
                        route: data.route,
                        printA4: data.print_a4,
                        printA5: data.print_a5,
                        message: data.message,
                    };
                }
                return { ok: false, message: data.messages || 'Error al guardar' };
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