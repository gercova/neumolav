import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL;

export const useAppointmentsSeeStore = defineStore('appointmentsSee', {
    state: () => ({
        items: [],
        loading: false,
    }),
    actions: {
        async fetchByHistory(historyId) {
            this.loading = true;
            try {
                // Usa el endpoint que soporta historia / appointment / dni
                const { data } = await axios.get(`${API()}/appointments/listAppointments/${historyId}`);
                this.items = (data.aaData || []).map((row, i) => ({
                    index: i + 1,
                    createdAt: row[1],
                    id: this._extractId(row[2]),
                }));
            } finally {
                this.loading = false;
            }
        },
        _extractId(html) {
            const m = String(html || '').match(/value="(\d+)"/);
            return m ? +m[1] : null;
        },
        async destroy(id) {
            await axios.delete(`${API()}/appointments/delete/${id}`);
        },
    },
});