import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL || '/admin';

export const usePostsStore = defineStore('posts', {
    state: () => ({
        items: [],
        total: 0,
        page: 1,
        pageSize: 10,
        search: '',
        filterType: '',
        loading: false,
        submitting: false,
        permissions: {
            create: false,
            update: false,
            delete: false,
        },
        postTypes: [],
        panelVisible: false,
        editingPost: null,
        formErrors: {},
    }),

    getters: {
        totalPages: (s) => Math.max(1, Math.ceil(s.total / s.pageSize)),
        rangeStart: (s) => (s.total === 0 ? 0 : (s.page - 1) * s.pageSize + 1),
        rangeEnd: (s)   => Math.min(s.page * s.pageSize, s.total),

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
                const { data } = await axios.get(`${API()}/publications/list`, {
                    params: {
                        page,
                        pageSize: this.pageSize,
                        search: this.search || undefined,
                        type_id: this.filterType || undefined,
                    },
                });

                if (data.status || Array.isArray(data.data)) {
                    this.items = data.data ?? [];
                    this.total = data.total ?? this.items.length;
                    this.page = page;

                    if (data.permissions) {
                        this.permissions = { ...this.permissions, ...data.permissions };
                    }
                }
            } catch (error) {
                console.error('Error fetching publications:', error);
                window.alertNotify?.('error', 'Error al cargar las publicaciones');
            } finally {
                this.loading = false;
            }
        },

        setSearch(term) {
            this.search = term;
            return this.fetchPage(1);
        },

        setFilter(typeId) {
            this.filterType = typeId;
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

        nextPage() { return this.goToPage(this.page + 1); },
        prevPage() { return this.goToPage(this.page - 1); },
        firstPage() { return this.goToPage(1); },
        lastPage() { return this.goToPage(this.totalPages); },

        openCreatePanel() {
            this.editingPost = null;
            this.formErrors = {};
            this.panelVisible = true;
        },

        async openEditPanel(post) {
            this.editingPost = { ...post };
            this.formErrors = {};
            this.panelVisible = true;

            try {
                const { data } = await axios.get(`${API()}/publications/${post.id}`);
                if (data && data.id) {
                    this.editingPost = { ...data };
                }
            } catch (error) {
                console.warn('Usando datos locales para edición:', error);
            }
        },

        closePanel() {
            this.panelVisible = false;
            this.editingPost = null;
            this.formErrors = {};
        },

        async savePost(formData) {
            this.submitting = true;
            this.formErrors = {};

            try {
                const payload = new FormData();

                if (formData.postId) payload.append('postId', formData.postId);
                payload.append('type_id', formData.type_id ?? '');
                payload.append('titulo', formData.titulo?.trim() ?? '');
                payload.append('titulo_corto', formData.titulo_corto?.trim() ?? '');
                payload.append('resumen', formData.resumen?.trim() ?? '');
                payload.append('contenido', formData.contenido ?? '');
                payload.append('descrip_img', formData.descrip_img?.trim() ?? '');
                payload.append('categories', formData.categories?.trim() ?? '');
                payload.append('meta_content', formData.meta_content?.trim() ?? '');
                payload.append('key_words', formData.key_words?.trim() ?? '');
                payload.append('etiquetas', formData.etiquetas?.trim() ?? '');

                if (formData.img instanceof File) {
                    payload.append('img', formData.img);
                }

                const response = await axios.post(`${API()}/publications/store`, payload, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });

                const resData = response.data;

                if (resData.status) {
                    this.closePanel();
                    window.alertNotify?.(resData.type || 'success', resData.message || 'Publicación guardada exitosamente');
                    await this.fetchPage(this.page);
                    return { success: true, data: resData };
                } else {
                    window.alertNotify?.(resData.type || 'error', resData.message || 'Error al procesar la publicación');
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
                    window.alertNotify?.('error', 'Ocurrió un error al guardar la publicación.');
                }
                return { success: false, error };
            } finally {
                this.submitting = false;
            }
        },

        async destroy(id) {
            try {
                await axios.delete(`${API()}/publications/delete/${id}`);
                window.alertNotify?.('success', 'Publicación eliminada exitosamente');
                const lastPage = this.page > 1 && this.items.length === 1;
                return this.fetchPage(lastPage ? this.page - 1 : this.page);
            } catch (error) {
                window.alertNotify?.('error', 'Error al eliminar la publicación');
            }
        },
    },
});
