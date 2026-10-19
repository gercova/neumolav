import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL || '/admin';

export const useUsersStore = defineStore('users', {
    state: () => ({
        // Listado y Paginación
        items: [],
        total: 0,
        page: 1,
        pageSize: 10,
        search: '',
        roleFilter: '',
        loading: false,
        submitting: false,
        currentUserId: null,

        // Catálogos
        roles: [],
        specialties: [],

        // Permisos del usuario actual
        permissions: {
            create: false,
            update: false,
            delete: false,
            assign_roles: false,
        },

        // Rutas externas (opcional para fallback de enlaces)
        routes: {
            add: '/admin/users/add',
            role_prefix: '/admin/users/role',
            edit_prefix: '/admin/users/edit',
        },

        // Modal Usuario (Crear / Editar)
        userModalVisible: false,
        editingUser: null,
        userFormErrors: {},

        // Modal Contraseña Rápida
        passwordModalVisible: false,
        passwordTargetUser: null,
        passwordFormErrors: {},
        passwordSubmitting: false,
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
                const { data } = await axios.get(`${API()}/users/list`, {
                    params: {
                        page,
                        pageSize: this.pageSize,
                        search: this.search || undefined,
                        role: this.roleFilter || undefined,
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
                console.error('Error fetching users:', error);
                window.alertNotify?.('error', 'Error al cargar los usuarios');
            } finally {
                this.loading = false;
            }
        },

        setSearch(term) {
            this.search = term;
            return this.fetchPage(1);
        },

        setRoleFilter(role) {
            this.roleFilter = role;
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

        // Modal Usuario (Crear / Editar)
        openCreateModal() {
            this.editingUser = null;
            this.userFormErrors = {};
            this.userModalVisible = true;
        },

        async openEditModal(user) {
            this.editingUser = { ...user };
            this.userFormErrors = {};
            this.userModalVisible = true;

            try {
                const { data } = await axios.get(`${API()}/users/${user.id}`);
                if (data && data.id) {
                    this.editingUser = {
                        ...this.editingUser,
                        ...data,
                    };
                }
            } catch (error) {
                console.warn('Usando datos de fila para edición de usuario:', error);
            }
        },

        closeUserModal() {
            this.userModalVisible = false;
            this.editingUser = null;
            this.userFormErrors = {};
        },

        async saveUser(formDataPayload) {
            this.submitting = true;
            this.userFormErrors = {};
            try {
                let body = formDataPayload;
                let headers = {};

                if (formDataPayload instanceof FormData) {
                    headers['Content-Type'] = 'multipart/form-data';
                }

                const response = await axios.post(`${API()}/users/store`, body, { headers });
                const resData = response.data;

                if (resData.status) {
                    this.closeUserModal();
                    window.alertNotify?.(resData.type || 'success', resData.messages || resData.message || 'Usuario guardado correctamente');
                    await this.fetchPage(this.page);
                    return { success: true, data: resData };
                } else {
                    window.alertNotify?.(resData.type || 'error', resData.messages || resData.message || 'Error al procesar el usuario');
                    return { success: false, data: resData };
                }
            } catch (error) {
                if (error.response?.data?.errors) {
                    this.userFormErrors = error.response.data.errors;
                    const firstKey = Object.keys(this.userFormErrors)[0];
                    if (firstKey && this.userFormErrors[firstKey][0]) {
                        window.alertNotify?.('warning', this.userFormErrors[firstKey][0]);
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

        // Modal Contraseña Rápida
        openPasswordModal(user) {
            this.passwordTargetUser = { ...user };
            this.passwordFormErrors = {};
            this.passwordModalVisible = true;
        },

        closePasswordModal() {
            this.passwordModalVisible = false;
            this.passwordTargetUser = null;
            this.passwordFormErrors = {};
        },

        async savePassword({ password, password_confirmation }) {
            if (!this.passwordTargetUser) return { success: false };
            this.passwordSubmitting = true;
            this.passwordFormErrors = {};

            try {
                const response = await axios.post(
                    `${API()}/users/storePassword/${this.passwordTargetUser.id}`,
                    { password, password_confirmation }
                );

                const resData = response.data;
                if (resData.status) {
                    this.closePasswordModal();
                    window.alertNotify?.(resData.type || 'success', resData.message || resData.messages || 'Contraseña actualizada correctamente');
                    return { success: true, data: resData };
                } else {
                    window.alertNotify?.('error', resData.message || resData.messages || 'Error al actualizar la contraseña');
                    return { success: false, data: resData };
                }
            } catch (error) {
                if (error.response?.data?.errors) {
                    this.passwordFormErrors = error.response.data.errors;
                    const firstKey = Object.keys(this.passwordFormErrors)[0];
                    if (firstKey && this.passwordFormErrors[firstKey][0]) {
                        window.alertNotify?.('warning', this.passwordFormErrors[firstKey][0]);
                    }
                } else {
                    window.alertNotify?.('error', 'Error al actualizar la contraseña');
                }
                return { success: false, error };
            } finally {
                this.passwordSubmitting = false;
            }
        },

        // Eliminar Usuario
        async destroy(id) {
            try {
                const response = await axios.delete(`${API()}/users/${id}`);
                const resData = response.data;
                if (resData.status) {
                    window.alertNotify?.('success', resData.messages || resData.message || 'Usuario eliminado correctamente');
                    const lastPage = this.page > 1 && this.items.length === 1;
                    await this.fetchPage(lastPage ? this.page - 1 : this.page);
                    return { success: true };
                } else {
                    window.alertNotify?.('error', resData.messages || resData.message || 'Error al eliminar usuario');
                    return { success: false };
                }
            } catch (error) {
                const msg = error.response?.data?.messages || error.response?.data?.message || 'Error al eliminar el usuario';
                window.alertNotify?.('error', msg);
                throw error;
            }
        },
    },
});
