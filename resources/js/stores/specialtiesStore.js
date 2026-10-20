import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL || '/admin';

export const useSpecialtiesStore = defineStore('specialties', {
    state: () => ({
        // Listado y Paginación
        items: [],
        total: 0,
        page: 1,
        pageSize: 10,
        search: '',
        occupationFilter: '',
        specialtyFilter: '',
        specialtyLabel: '',
        loading: false,
        submitting: false,
        permissions: {
            create: false,
            update: false,
            delete: false,
        },

        // Catálogo de cargos / ocupaciones
        occupations: [],

        // Modal (Crear / Editar)
        modalVisible: false,
        editingSpecialty: null,
        formErrors: {},
    }),

    getters: {
        totalPages: (s) => Math.max(1, Math.ceil(s.total / s.pageSize)),

        rangeStart: (s) => (s.total === 0 ? 0 : (s.page - 1) * s.pageSize + 1),
        rangeEnd:   (s) => Math.min(s.page * s.pageSize, s.total),

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
        // Listado y Paginación
        async fetchPage(page = this.page) {
            this.loading = true;
            try {
                const { data } = await axios.get(`${API()}/specialties/list`, {
                    params: {
                        page,
                        pageSize: this.pageSize,
                        search: this.search || undefined,
                        occupation: this.occupationFilter || undefined,
                        specialty_id: this.specialtyFilter || undefined,
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
                console.error('Error fetching specialties:', error);
                window.alertNotify?.('error', 'Error al cargar las especialidades');
            } finally {
                this.loading = false;
            }
        },

        async fetchOptions() {
            try {
                const { data } = await axios.get(`${API()}/specialties/options`);
                if (data && data.occupations) {
                    this.occupations = data.occupations;
                }
            } catch (error) {
                console.warn('Error fetching specialty options:', error);
            }
        },

        setSearch(term) {
            this.search = term;
            return this.fetchPage(1);
        },

        setOccupationFilter(occId) {
            this.occupationFilter = occId;
            return this.fetchPage(1);
        },

        setSpecialtyFilter(id, label = '') {
            this.specialtyFilter = id ? String(id) : '';
            this.specialtyLabel = label || '';
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

        // Modal Especialidad (Crear / Editar)
        openCreateModal() {
            this.editingSpecialty = null;
            this.formErrors = {};
            this.modalVisible = true;
        },

        async openEditModal(specialty) {
            this.editingSpecialty = { ...specialty };
            this.formErrors = {};
            this.modalVisible = true;

            try {
                const { data } = await axios.get(`${API()}/specialties/${specialty.id}`);
                if (data && data.id) {
                    this.editingSpecialty = {
                        id: data.id,
                        id_ocupacion: data.id_ocupacion,
                        descripcion: data.descripcion,
                        detalle: data.detalle,
                        occupation_name: data.occupation_name || data.occupation?.descripcion || '',
                    };
                }
            } catch (error) {
                console.warn('Usando datos de fila para edición de especialidad:', error);
            }
        },

        closeModal() {
            this.modalVisible = false;
            this.editingSpecialty = null;
            this.formErrors = {};
        },

        async saveSpecialty(formData) {
            this.submitting = true;
            this.formErrors = {};
            try {
                const payload = {
                    id: formData.id || undefined,
                    id_ocupacion: formData.id_ocupacion,
                    descripcion: formData.descripcion?.trim(),
                    detalle: formData.detalle?.trim(),
                };

                const response = await axios.post(`${API()}/specialties/store`, payload);
                const resData = response.data;

                if (resData.status) {
                    this.closeModal();
                    window.alertNotify?.(resData.type || 'success', resData.messages || resData.message || 'Operación exitosa');
                    await this.fetchPage(this.page);
                    return { success: true, data: resData };
                } else {
                    window.alertNotify?.(resData.type || 'error', resData.messages || resData.message || 'Error al procesar la especialidad');
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
                    const msg = error.response?.data?.messages || error.response?.data?.message || 'Ocurrió un error al procesar la solicitud.';
                    window.alertNotify?.('error', msg);
                }
                return { success: false, error };
            } finally {
                this.submitting = false;
            }
        },

        async destroy(id) {
            try {
                const response = await axios.delete(`${API()}/specialties/${id}`);
                const resData = response.data;
                if (resData.status) {
                    window.alertNotify?.('success', resData.messages || resData.message || 'La especialidad fue eliminada');
                    const lastPage = this.page > 1 && this.items.length === 1;
                    await this.fetchPage(lastPage ? this.page - 1 : this.page);
                    return { success: true };
                } else {
                    window.alertNotify?.('error', resData.messages || resData.message || 'Error al eliminar especialidad');
                    return { success: false };
                }
            } catch (error) {
                const msg = error.response?.data?.messages || error.response?.data?.message || 'Error al eliminar la especialidad';
                window.alertNotify?.('error', msg);
                throw error;
            }
        },
    },
});
