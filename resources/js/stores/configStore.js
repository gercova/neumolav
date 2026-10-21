import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL || '/admin';

export const useConfigStore = defineStore('config', {
    state: () => ({
        versions: [],
        total: 0,
        page: 1,
        pageSize: 10,
        search: '',
        typeFilter: '',
        loading: false,
        detecting: false,
        submitting: false,
        deleting: false,

        currentVersion: '3.3.0',
        latestRelease: null,
        metrics: {
            total: 0,
            major: 0,
            minor: 0,
            patch: 0,
        },
        permissions: {
            access: false,
            view: false,
            create: false,
            delete: false,
        },
        routes: {},

        // Change detection & release creation modal
        pendingDetection: null,
        newVersionModalVisible: false,
        formErrors: {},

        // Detail modal
        selectedVersion: null,
        detailLoading: false,
        detailModalVisible: false,
    }),

    getters: {
        totalPages: (s) => Math.max(1, Math.ceil(s.total / s.pageSize)),
        rangeStart: (s) => (s.total === 0 ? 0 : (s.page - 1) * s.pageSize + 1),
        rangeEnd: (s) => Math.min(s.page * s.pageSize, s.total),

        visiblePages: (s) => {
            const total = Math.max(1, Math.ceil(s.total / s.pageSize));
            const cur = s.page;
            const delta = 2;

            const range = [];
            const left = Math.max(2, cur - delta);
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
        async fetchVersions(page = this.page) {
            this.loading = true;
            try {
                const url = this.routes.list || `${API()}/config/list`;
                const { data } = await axios.get(url, {
                    params: {
                        page,
                        pageSize: this.pageSize,
                        search: this.search || undefined,
                        type: this.typeFilter || undefined,
                    },
                });

                if (data.status) {
                    this.versions = data.data || [];
                    this.total = data.total || 0;
                    this.page = page;

                    if (data.metrics) {
                        this.metrics = { ...this.metrics, ...data.metrics };
                    }
                    if (data.permissions) {
                        this.permissions = { ...this.permissions, ...data.permissions };
                    }
                }
            } catch (error) {
                console.error('Error cargando versiones:', error);
                window.alertNotify?.('error', 'Error al cargar el historial de versiones.');
            } finally {
                this.loading = false;
            }
        },

        setSearch(term) {
            this.search = term;
            return this.fetchVersions(1);
        },

        setTypeFilter(type) {
            this.typeFilter = type;
            return this.fetchVersions(1);
        },

        setPageSize(size) {
            this.pageSize = Number(size);
            return this.fetchVersions(1);
        },

        goToPage(p) {
            if (p < 1 || p > this.totalPages) return;
            return this.fetchVersions(p);
        },

        nextPage() { return this.goToPage(this.page + 1); },
        prevPage() { return this.goToPage(this.page - 1); },
        firstPage() { return this.goToPage(1); },
        lastPage() { return this.goToPage(this.totalPages); },

        async detectChanges() {
            this.detecting = true;
            try {
                const url = this.routes.detect || `${API()}/config/detect`;
                const { data } = await axios.get(url);
                if (data.status && data.detection) {
                    this.pendingDetection = data.detection;
                    if (data.detection.current_version) {
                        this.currentVersion = data.detection.current_version;
                    }
                    return data.detection;
                }
            } catch (error) {
                console.error('Error detectando cambios:', error);
                window.alertNotify?.('error', 'No se pudieron escanear los cambios del código.');
            } finally {
                this.detecting = false;
            }
            return null;
        },

        async openNewVersionModal() {
            this.formErrors = {};
            this.newVersionModalVisible = true;
            await this.detectChanges();
        },

        closeNewVersionModal() {
            this.newVersionModalVisible = false;
            this.formErrors = {};
        },

        async createVersion(payload) {
            this.submitting = true;
            this.formErrors = {};

            try {
                const url = this.routes.generate || `${API()}/config/generate`;
                const { data } = await axios.post(url, payload);

                if (data.status) {
                    this.closeNewVersionModal();
                    this.currentVersion = data.version.version;
                    window.alertNotify?.('success', data.message || 'Nueva versión generada y registrada exitosamente.');
                    await this.fetchVersions(1);
                    return { success: true, data };
                } else {
                    window.alertNotify?.('error', data.message || 'Error al generar la versión.');
                    return { success: false, data };
                }
            } catch (error) {
                if (error.response?.data?.errors) {
                    this.formErrors = error.response.data.errors;
                    const firstKey = Object.keys(this.formErrors)[0];
                    if (firstKey && this.formErrors[firstKey][0]) {
                        window.alertNotify?.('warning', this.formErrors[firstKey][0]);
                    }
                } else {
                    const msg = error.response?.data?.message || 'Ocurrió un error inesperado al procesar la versión.';
                    window.alertNotify?.('error', msg);
                }
                return { success: false, error };
            } finally {
                this.submitting = false;
            }
        },

        async openDetailModal(id) {
            this.detailLoading = true;
            this.selectedVersion = null;
            this.detailModalVisible = true;

            try {
                const { data } = await axios.get(`${API()}/config/${id}`);
                if (data.status && data.version) {
                    this.selectedVersion = data.version;
                }
            } catch (error) {
                console.error('Error obteniendo detalle de versión:', error);
                window.alertNotify?.('error', 'Error al obtener los detalles de la versión.');
                this.closeDetailModal();
            } finally {
                this.detailLoading = false;
            }
        },

        closeDetailModal() {
            this.detailModalVisible = false;
            this.selectedVersion = null;
        },

        async deleteVersion(id) {
            this.deleting = true;
            try {
                const { data } = await axios.delete(`${API()}/config/${id}`);
                if (data.status) {
                    window.alertNotify?.('success', data.message || 'Versión eliminada.');
                    const lastPage = this.page > 1 && this.versions.length === 1;
                    await this.fetchVersions(lastPage ? this.page - 1 : this.page);
                    return { success: true };
                } else {
                    window.alertNotify?.(data.type || 'error', data.message || 'No se pudo eliminar la versión.');
                    return { success: false };
                }
            } catch (error) {
                const msg = error.response?.data?.message || 'Error al eliminar la versión.';
                window.alertNotify?.('error', msg);
                return { success: false };
            } finally {
                this.deleting = false;
            }
        },
    },
});
