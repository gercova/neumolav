import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL || '/admin';

export const useOccupationsStore = defineStore('occupations', {
    state: () => ({
        items: [],
        total: 0,
        page: 1,
        pageSize: 10,
        search: '',
        loading: false,
        submitting: false,
        permissions: {
            create: false,
            update: false,
            delete: false,
        },
        modalVisible: false,
        editingOccupation: null,
        formErrors: {},
    }),

    getters: {
        totalPages: (s) => Math.max(1, Math.ceil(s.total / s.pageSize)),

        rangeStart: (s) => (s.total === 0 ? 0 : (s.page - 1) * s.pageSize + 1),
        rangeEnd:   (s) => Math.min(s.page * s.pageSize, s.total),

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
            if (left > 2) range.push('…');
            for (let i = left; i <= right; i++) range.push(i);
            if (right < total - 1) range.push('…');
            if (total > 1) range.push(total);

            return range;
        },
    },

    actions: {
        async fetchPage(page = this.page) {
            this.loading = true;
            try {
                const { data } = await axios.get(`${API()}/occupations/list`, {
                    params: {
                        page,
                        pageSize: this.pageSize,
                        search: this.search || undefined,
                    },
                });

                if (data.status || data.Result === 'OK' || Array.isArray(data.data) || Array.isArray(data.aaData)) {
                    this.items = data.data ?? data.Records ?? [];
                    this.total = data.total ?? data.TotalRecordCount ?? this.items.length;
                    this.page  = page;

                    if (data.permissions) {
                        this.permissions = { ...this.permissions, ...data.permissions };
                    }
                }
            } catch (error) {
                console.error('Error fetching occupations:', error);
                window.alertNotify?.('error', 'Error al cargar las ocupaciones');
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

        openCreateModal() {
            this.editingOccupation = null;
            this.formErrors = {};
            this.modalVisible = true;
        },

        async openEditModal(occupation) {
            this.editingOccupation = { ...occupation };
            this.formErrors = {};
            this.modalVisible = true;

            try {
                const { data } = await axios.get(`${API()}/occupations/${occupation.id}`);
                if (data && data.id) {
                    this.editingOccupation = {
                        id: data.id,
                        descripcion: data.descripcion,
                        estado: data.estado !== undefined ? Boolean(data.estado) : true,
                    };
                }
            } catch (error) {
                console.warn('Usando datos locales de fila para edición:', error);
            }
        },

        closeModal() {
            this.modalVisible = false;
            this.editingOccupation = null;
            this.formErrors = {};
        },

        async saveOccupation(formData) {
            this.submitting = true;
            this.formErrors = {};
            try {
                const payload = {
                    id: formData.id || undefined,
                    descripcion: formData.descripcion?.trim(),
                    estado: formData.estado ? 1 : 0,
                };

                const response = await axios.post(`${API()}/occupations/store`, payload);
                const resData = response.data;

                if (resData.status) {
                    this.closeModal();
                    window.alertNotify?.(resData.type || 'success', resData.messages || 'Operación exitosa');
                    await this.fetchPage(this.page);
                    return { success: true, data: resData };
                } else {
                    window.alertNotify?.(resData.type || 'error', resData.messages || 'Error al procesar la ocupación');
                    return { success: false, data: resData };
                }
            } catch (error) {
                if (error.response?.data?.errors) {
                    this.formErrors = error.response.data.errors;
                    const firstKey = Object.keys(this.formErrors)[0];
                    if (firstKey && this.formErrors[firstKey][0]) {
                        window.alertNotify?.('warning', this.formErrors[firstKey][0]);
                    }
                } else {
                    window.alertNotify?.('error', 'Ocurrió un error al procesar la solicitud.');
                }
                return { success: false, error };
            } finally {
                this.submitting = false;
            }
        },

        async destroy(id) {
            await axios.delete(`${API()}/occupations/${id}`);
            const lastPage = this.page > 1 && this.items.length === 1;
            return this.fetchPage(lastPage ? this.page - 1 : this.page);
        },
    },
});
