import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL;

export const useReportsSeeStore = defineStore('reportsSee', {
    state: () => ({
        items: [],
        loading: false,
    }),
    actions: {
        async fetchByHistory(historyId) {
            this.loading = true;
            try {
                const { data } = await axios.get(`${API()}/reports/list/${historyId}`);
                this.items = (data.aaData || []).map((row, i) => ({
                    index: i + 1,
                    createdAt: row[1],
                    id: this._extractId(row[3]),
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
            await axios.delete(`${API()}/reports/delete/${id}`);
        },
    },
});