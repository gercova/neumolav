import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL;

export const useQuickViewStore = defineStore('quickView', {
    state: () => ({
        visible: false,
        loading: false,
        error: null,
        historyId: null,
        history: null,
        appointments: [],
        exams: [],
        routes: {},
        activeTab: 'controles',
    }),

    getters: {
        appointmentsCount: (s) => s.appointments.length,
        examsCount:        (s) => s.exams.length,
        tipoAtencion:      (s) => s.history?.tipo_atencion_desc || '',
        tipoAtencionColor: (s) => s.history?.tipo_atencion_color || 'badge-secondary',
        isArchived:        (s) => !!s.history?.is_deleted,
    },

    actions: {
        async open(historyId) {
            if (!historyId) {
                window.Swal?.fire({
                    icon: 'warning',
                    title: 'Historia clínica no encontrada',
                    text: 'El paciente no tiene una historia clínica vinculada.',
                });
                return;
            }

            this.visible        = true;
            this.loading        = true;
            this.error          = null;
            this.historyId      = historyId;
            this.activeTab      = 'controles';
            this.history        = null;
            this.appointments   = [];
            this.exams          = [];
            this.routes         = {};

            try {
                const { data } = await axios.get(`${API()}/histories/quick-view/${historyId}`);
                if (!data?.status) throw new Error(data?.message || 'Error al obtener datos.');

                this.history        = data.history;
                this.appointments   = data.appointments || [];
                this.exams          = data.exams || [];
                this.routes         = data.routes || {};
            } catch (e) {
                this.error          = e.message || 'Error al conectar con el servidor.';
            } finally {
                this.loading        = false;
            }
        },

        close() {
            this.visible = false;
            setTimeout(() => {
                this.history        = null;
                this.appointments   = [];
                this.exams          = [];
                this.routes         = {};
                this.historyId      = null;
                this.error          = null;
            }, 300);
        },

        setTab(tab) { this.activeTab = tab; },

        // Helper: renderiza antecedentes que vienen como texto libre
        isPositive(val) {
            if (!val) return false;
            const s = String(val).trim().toLowerCase();
            return !['', 'no', '0', 'negativo', 'no refiere', 'ninguno', 'ninguna'].includes(s);
        },
    },
});