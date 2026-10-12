import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL;

export const useExamsSeeStore = defineStore('examsSee', {
    state: () => ({
        items: [],
        loading: false,
        permissions: { view: false, update: false, delete: false },
    }),
    actions: {
        async fetchByHistory(historyId) {
            this.loading = true;
            try {
                const { data } = await axios.get(`${API()}/exams/list/${historyId}`);
                this.items = (data.aaData || []).map((row, i) => ({
                    index: i + 1,
                    createdAt: row[1],
                    dni: row[2],
                    tipo: row[3],
                    actionsRaw: row[4],
                    // Extraemos los IDs de los botones HTML del backend
                    id: this._extractId(row[4]),
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
            await axios.delete(`${API()}/exams/delete/${id}`);
        },
    },
});