import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL;

export const useStatisticsStore = defineStore('statistics', {
    state: () => ({
        counts: { hc: 0, ex: 0, ap: 0, cd: 0 },
        years: [],          // provisto desde Blade (server-side)
        selectedYear: null,
        annualData: [],     // series de Highcharts
        diagnostics: [],    // [{ diagnostico, cantidad }]
        drugs: [],
        bySex: [],
        bySmoking: [],
        byBloodGroup: [],
        byDegree: [],
        byMaritalStatus: [],
        loading: {
            counts: false, annual: false,
            diagnostics: false, drugs: false,
            demographics: false,
        },
    }),

    actions: {
        // ── Contadores globales ───────────────────────────────
        async fetchCounts() {
            this.loading.counts = true;
            try {
                const { data } = await axios.get(`${API()}/dashboard/hcCount`);
                this.counts = {
                    hc: data.hc ?? 0,
                    ex: data.ex ?? 0,
                    ap: data.ap ?? 0,
                    cd: data.cd ?? 0,
                };
            } finally { this.loading.counts = false; }
        },

        // ── Serie anual (depende del año seleccionado) ────────
        async fetchAnnual(year = this.selectedYear) {
            this.loading.annual = true;
            try {
                const { data } = await axios.get(`${API()}/dashboard/histories/${year}`);
                this.annualData = data;
                this.selectedYear = year;
            } finally { this.loading.annual = false; }
        },

        // ── Top diagnósticos / fármacos ───────────────────────
        async fetchDiagnostics() {
            this.loading.diagnostics = true;
            try {
                const { data } = await axios.get(`${API()}/dashboard/diagnosticsByExam`);
                this.diagnostics = data;
            } finally { this.loading.diagnostics = false; }
        },
        async fetchDrugs() {
            this.loading.drugs = true;
            try {
                const { data } = await axios.get(`${API()}/dashboard/drugsByExam`);
                this.drugs = data;
            } finally { this.loading.drugs = false; }
        },

        // ── Demográficos (5 endpoints en paralelo) ────────────
        async fetchDemographics() {
            this.loading.demographics = true;
            try {
                const [sex, smoking, blood, degree, marital] = await Promise.all([
                    axios.get(`${API()}/dashboard/historiesBySex`),
                    axios.get(`${API()}/dashboard/historiesBySmoking`),
                    axios.get(`${API()}/dashboard/historiesByBloodingGroup`),
                    axios.get(`${API()}/dashboard/historiesByDegreeIntruction`),
                    axios.get(`${API()}/dashboard/historiesByMaritalStatus`),
                ]);
                this.bySex              = sex.data;
                this.bySmoking          = smoking.data;
                this.byBloodGroup       = blood.data;
                this.byDegree           = degree.data;
                this.byMaritalStatus    = marital.data;
            } finally { this.loading.demographics = false; }
        },

        async init(initialYear) {
            this.selectedYear = initialYear;
            await Promise.all([
                this.fetchCounts(),
                this.fetchAnnual(initialYear),
                this.fetchDiagnostics(),
                this.fetchDrugs(),
                this.fetchDemographics(),
            ]);
        },

        // Helper: convierte [{ key, cantidad }] al formato [ [name, y], ... ]
        toPieData(rows, keyField, valueField = 'cantidad') {
            return rows.map(r => [r[keyField], Number(r[valueField])]);
        },
    },
});