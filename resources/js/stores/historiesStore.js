import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL;

export const useHistoriesStore = defineStore('histories', {
    state: () => ({
        items: [],
        total: 0,
        page: 1,
        pageSize: 10,
        search: '',
        loading: false,
        // Permisos globales (se calculan una vez y se aplican por fila)
        permissions: {
            update: false, delete: false,
            add_exm: false, view_exm: false,
            add_ctrl: false, view_ctrl: false,
            add_rpt: false, view_rpt: false,
            add_rsk: false, view_rsk: false,
        },
    }),

    getters: {
        totalPages: (s) => Math.max(1, Math.ceil(s.total / s.pageSize)),

        /**
         * Genera un rango de páginas con elipsis para evitar demasiados botones.
         * Ejemplo: [1, '…', 4, 5, 6, '…', 20]
         */
        visiblePages: (s) => {
            const total = Math.max(1, Math.ceil(s.total / s.pageSize));
            const cur   = s.page;
            const delta = 2; // páginas adyacentes a la actual

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
                // El endpoint es POST, usa protocolo JTable legacy
                const { data } = await axios.post(`${API()}/histories/list`, {
                    jtStartIndex: start,
                    jtPageSize: this.pageSize,
                    search: this.search || null,
                });

                if (data.Result === 'OK') {
                    this.items = data.Records ?? [];
                    this.total = data.TotalRecordCount ?? 0;
                    this.page  = page;

                    // Los permisos vienen incluidos en cada record; los tomamos del primero
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

        async destroy(id) {
            await axios.delete(`${API()}/histories/${id}`);
            // Si era el último de la página, retrocede
            const lastPage = this.page > 1 && this.items.length === 1;
            return this.fetchPage(lastPage ? this.page - 1 : this.page);
        },

        async addToQueue(historyId, tipoId) {
            const { data } = await axios.get(`${API()}/histories/quotes/${historyId}`, {
                params: { id_tipo_atencion: tipoId },
            });
            return data;
        },
    },
});