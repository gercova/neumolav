import { defineStore } from 'pinia';
import axios from 'axios';

const API = window.API_BASE_URL; // "https://tu-app/admin"

export const useCitasStore = defineStore('citas', {
    state: () => ({
        currentDate: null,
        filterStatus: '',
        citas: [],
        stats: { total: 0, en_espera: 0, atendidos: 0, pendientes: 0 },
        loading: false,
    }),

    actions: {
        async fetchCitas() {
            this.loading = true;
            try {
                const { data } = await axios.get(`${API}/citas/list`, {
                    params: { date: this.currentDate, status: this.filterStatus || undefined },
                });
                this.citas = data?.status ? data.data : [];
            } finally {
                this.loading = false;
            }
        },

        async fetchStats() {
            const { data } = await axios.get(`${API}/citas/stats`, {
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
        },

        setDate(date) {
            this.currentDate = date;
            return Promise.all([this.fetchCitas(), this.fetchStats()]);
        },

        setStatus(statusId) {
            this.filterStatus = statusId;
            return this.fetchCitas();
        },

        async refresh() {
            return Promise.all([this.fetchCitas(), this.fetchStats()]);
        },
    },
});