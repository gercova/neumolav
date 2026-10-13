import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL;

export const useAppointmentsStore = defineStore('appointments', {
    state: () => ({
        items: [],
        total: 0,
        page: 1,
        pageSize: 10,
        search: '',
        loading: false,
        permissions: {
            view_app: false,
            add_app: false,
        },
    }),

    getters: {
        totalPages: (s) => Math.max(1, Math.ceil(s.total / s.pageSize)),

        /**
         * Genera un rango de páginas con elipsis.
         * Ejemplo: [1, '…', 4, 5, 6, '…', 20]
         */
        visiblePages: (s) => {
            const total = Math.max(1, Math.ceil(s.total / s.pageSize));
            const cur   = s.page;
            const delta = 2;

            const range = [];
            const left  = Math.max(2, cur - delta);
            const right = Math.min(total - 1, cur + delta);

            range.push(1);
            if (left > 2)          range.push('…');
            for (let i = left; i <= right; i++) range.push(i);
            if (right < total - 1) range.push('…');
            if (total > 1)         range.push(total);

            return range;
        },
    },

    actions: {
        async fetchPage(page = this.page) {
            this.loading = true;
            try {
                const start = (page - 1) * this.pageSize;
                const { data } = await axios.post(`${API()}/appointments/list-histories`, {
                    jtStartIndex: start,
                    jtPageSize: this.pageSize,
                    search: this.search || null,
                });

                if (data.Result === 'OK') {
                    this.items = data.Records ?? [];
                    this.total = data.TotalRecordCount ?? 0;
                    this.page  = page;

                    if (this.items[0]?.Permissions) {
                        this.permissions = this.items[0].Permissions;
                    }
                }
            } finally {
                this.loading = false;
            }
        },

        setSearch(term) {
            this.search = term;
            return this.fetchPage(1);
        },

        setPageSize(size) {
            this.pageSize = Number(size);
            return this.fetchPage(1);
        },

        goToPage(p) {
            if (p < 1 || p > this.totalPages) return;
            return this.fetchPage(p);
        },

        nextPage()  { return this.goToPage(this.page + 1); },
        prevPage()  { return this.goToPage(this.page - 1); },
        firstPage() { return this.goToPage(1); },
        lastPage()  { return this.goToPage(this.totalPages); },
    },
});
