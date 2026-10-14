import { defineStore } from 'pinia';
import axios from 'axios';

const getApi = () => (window.API_BASE_URL || '/admin').replace(/\/+$/, '');

export const useCitasStore = defineStore('citas', {
    state: () => ({
        currentDate: null,
        filterStatus: '',
        citas: [],
        stats: { total: 0, en_espera: 0, atendidos: 0, pendientes: 0 },
        loading: false,
    }),

    actions: {
        async fetchCitas(silent = false) {
            if (!silent) {
                this.loading = true;
            }
            try {
                const { data } = await axios.get(`${getApi()}/citas/list`, {
                    params: { date: this.currentDate, status: this.filterStatus || undefined },
                });
                this.citas = data?.status ? data.data : [];
            } catch (error) {
                console.error('Error al cargar lista de citas:', error);
            } finally {
                if (!silent) {
                    this.loading = false;
                }
            }
        },

        async fetchStats() {
            try {
                const { data } = await axios.get(`${getApi()}/citas/stats`, {
                    params: { date: this.currentDate },
                });
                if (data?.status !== false) {
                    this.stats = {
                        total:      data.total      ?? 0,
                        en_espera:  data.en_espera  ?? 0,
                        atendidos:  data.atendidos  ?? 0,
                        pendientes: data.pendientes ?? 0,
                    };
                }
            } catch (error) {
                console.error('Error al cargar estadísticas de citas:', error);
            }
        },

        setDate(date) {
            this.currentDate = date;
            return Promise.all([this.fetchCitas(false), this.fetchStats()]);
        },

        setStatus(statusId) {
            this.filterStatus = statusId;
            return this.fetchCitas(false);
        },

        async refresh(silent = false) {
            return Promise.all([this.fetchCitas(silent), this.fetchStats()]);
        },
    },
});