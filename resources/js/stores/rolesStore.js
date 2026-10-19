import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL || '/admin';

export const useRolesStore = defineStore('roles', {
    state: () => ({
        // Roles list & pagination
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

        // Role form modal (crear / editar)
        roleModalVisible: false,
        editingRole: null,
        roleFormErrors: {},

        // Permissions dual-panel modal
        permissionsModalVisible: false,
        permRoleId: null,
        permRoleName: '',
        availablePermissions: [],
        assignedPermissions: [],
        selectedAvailable: [],
        selectedAssigned: [],
        availableSearch: '',
        assignedSearch: '',
        loadingPermissions: false,
        syncingPermissions: false,

        // Users modal
        usersModalVisible: false,
        usersRoleName: '',
        roleUsers: [],
        loadingUsers: false,
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

        filteredAvailable: (s) => {
            const q = s.availableSearch.toLowerCase().trim();
            if (!q) return s.availablePermissions;
            return s.availablePermissions.filter(
                (p) =>
                    p.name.toLowerCase().includes(q) ||
                    (p.description && p.description.toLowerCase().includes(q))
            );
        },

        filteredAssigned: (s) => {
            const q = s.assignedSearch.toLowerCase().trim();
            if (!q) return s.assignedPermissions;
            return s.assignedPermissions.filter(
                (p) =>
                    p.name.toLowerCase().includes(q) ||
                    (p.description && p.description.toLowerCase().includes(q))
            );
        },
    },

    actions: {
        // Listado y Paginación
        async fetchPage(page = this.page) {
            this.loading = true;
            try {
                const { data } = await axios.get(`${API()}/roles/list`, {
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
                console.error('Error fetching roles:', error);
                window.alertNotify?.('error', 'Error al cargar los roles');
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

        // Modal Rol (Crear / Editar)
        openCreateModal() {
            this.editingRole = null;
            this.roleFormErrors = {};
            this.roleModalVisible = true;
        },

        async openEditModal(role) {
            this.editingRole = { ...role };
            this.roleFormErrors = {};
            this.roleModalVisible = true;

            try {
                const { data } = await axios.get(`${API()}/roles/${role.id}`);
                if (data && data.id) {
                    this.editingRole = {
                        id: data.id,
                        name: data.name,
                        guard_name: data.guard_name || 'web',
                    };
                }
            } catch (error) {
                console.warn('Usando datos de fila para edición de rol:', error);
            }
        },

        closeRoleModal() {
            this.roleModalVisible = false;
            this.editingRole = null;
            this.roleFormErrors = {};
        },

        async saveRole(formData) {
            this.submitting = true;
            this.roleFormErrors = {};
            try {
                const payload = {
                    id: formData.id || undefined,
                    name: formData.name?.trim(),
                    guard_name: formData.guard_name?.trim() || 'web',
                };

                const response = await axios.post(`${API()}/roles/store`, payload);
                const resData = response.data;

                if (resData.status) {
                    this.closeRoleModal();
                    window.alertNotify?.(resData.type || 'success', resData.messages || resData.message || 'Operación exitosa');
                    await this.fetchPage(this.page);
                    return { success: true, data: resData };
                } else {
                    window.alertNotify?.(resData.type || 'error', resData.messages || resData.message || 'Error al procesar el rol');
                    return { success: false, data: resData };
                }
            } catch (error) {
                if (error.response?.data?.errors) {
                    this.roleFormErrors = error.response.data.errors;
                    const firstKey = Object.keys(this.roleFormErrors)[0];
                    if (firstKey && this.roleFormErrors[firstKey][0]) {
                        window.alertNotify?.('warning', this.roleFormErrors[firstKey][0]);
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
            await axios.delete(`${API()}/roles/${id}`);
            const lastPage = this.page > 1 && this.items.length === 1;
            return this.fetchPage(lastPage ? this.page - 1 : this.page);
        },

        // Modal Permisos Dual-Panel
        async openPermissionsModal(role) {
            this.permRoleId = role.id;
            this.permRoleName = role.name;
            this.availablePermissions = [];
            this.assignedPermissions = [];
            this.selectedAvailable = [];
            this.selectedAssigned = [];
            this.availableSearch = '';
            this.assignedSearch = '';
            this.loadingPermissions = true;
            this.permissionsModalVisible = true;

            try {
                const { data } = await axios.get(`${API()}/roles/${role.id}/permissions`);
                this.availablePermissions = data.available || [];
                this.assignedPermissions = data.assigned || [];
            } catch (error) {
                console.error('Error al cargar permisos del rol:', error);
                window.alertNotify?.('error', 'Error al cargar los permisos del rol');
            } finally {
                this.loadingPermissions = false;
            }
        },

        closePermissionsModal() {
            this.permissionsModalVisible = false;
            this.permRoleId = null;
            this.permRoleName = '';
            this.availablePermissions = [];
            this.assignedPermissions = [];
            this.selectedAvailable = [];
            this.selectedAssigned = [];
            this.availableSearch = '';
            this.assignedSearch = '';
        },

        toggleSelectAvailable(id) {
            const idx = this.selectedAvailable.indexOf(id);
            if (idx > -1) {
                this.selectedAvailable.splice(idx, 1);
            } else {
                this.selectedAvailable.push(id);
            }
        },

        toggleSelectAssigned(id) {
            const idx = this.selectedAssigned.indexOf(id);
            if (idx > -1) {
                this.selectedAssigned.splice(idx, 1);
            } else {
                this.selectedAssigned.push(id);
            }
        },

        transferPermission(perm, direction) {
            if (direction === 'add') {
                this.availablePermissions = this.availablePermissions.filter((p) => p.id !== perm.id);
                this.selectedAvailable = this.selectedAvailable.filter((id) => id !== perm.id);
                if (!this.assignedPermissions.some((p) => p.id === perm.id)) {
                    this.assignedPermissions.push(perm);
                }
            } else {
                this.assignedPermissions = this.assignedPermissions.filter((p) => p.id !== perm.id);
                this.selectedAssigned = this.selectedAssigned.filter((id) => id !== perm.id);
                if (!this.availablePermissions.some((p) => p.id === perm.id)) {
                    this.availablePermissions.push(perm);
                }
            }
        },

        transferAll(direction) {
            if (direction === 'add') {
                const toMove = [...this.filteredAvailable];
                toMove.forEach((perm) => {
                    this.availablePermissions = this.availablePermissions.filter((p) => p.id !== perm.id);
                    if (!this.assignedPermissions.some((p) => p.id === perm.id)) {
                        this.assignedPermissions.push(perm);
                    }
                });
                this.selectedAvailable = [];
            } else {
                const toMove = [...this.filteredAssigned];
                toMove.forEach((perm) => {
                    this.assignedPermissions = this.assignedPermissions.filter((p) => p.id !== perm.id);
                    if (!this.availablePermissions.some((p) => p.id === perm.id)) {
                        this.availablePermissions.push(perm);
                    }
                });
                this.selectedAssigned = [];
            }
        },

        transferSelected(direction) {
            if (direction === 'add') {
                const selectedIds = new Set(this.selectedAvailable);
                const toMove = this.availablePermissions.filter((p) => selectedIds.has(p.id));
                toMove.forEach((perm) => this.transferPermission(perm, 'add'));
                this.selectedAvailable = [];
            } else {
                const selectedIds = new Set(this.selectedAssigned);
                const toMove = this.assignedPermissions.filter((p) => selectedIds.has(p.id));
                toMove.forEach((perm) => this.transferPermission(perm, 'remove'));
                this.selectedAssigned = [];
            }
        },

        async saveRolePermissions() {
            if (!this.permRoleId) return;
            this.syncingPermissions = true;
            try {
                const permIds = this.assignedPermissions.map((p) => p.id);
                const response = await axios.post(
                    `${API()}/roles/${this.permRoleId}/syncPermissions`,
                    { permissions: permIds }
                );

                const resData = response.data;
                if (resData.status) {
                    this.closePermissionsModal();
                    window.alertNotify?.(resData.type || 'success', resData.messages || resData.message || 'Permisos actualizados');
                    await this.fetchPage(this.page);
                } else {
                    window.alertNotify?.('error', resData.messages || resData.message || 'Error al guardar los permisos');
                }
            } catch (error) {
                console.error('Error syncing permissions:', error);
                window.alertNotify?.('error', 'Error al sincronizar los permisos');
            } finally {
                this.syncingPermissions = false;
            }
        },

        // Modal Usuarios del Rol
        async openUsersModal(role) {
            this.usersRoleName = role.name;
            this.roleUsers = [];
            this.loadingUsers = true;
            this.usersModalVisible = true;

            try {
                const { data } = await axios.get(`${API()}/roles/${role.id}/users`);
                this.roleUsers = data.users || [];
            } catch (error) {
                console.error('Error fetching role users:', error);
                window.alertNotify?.('error', 'Error al cargar los usuarios del rol');
            } finally {
                this.loadingUsers = false;
            }
        },

        closeUsersModal() {
            this.usersModalVisible = false;
            this.usersRoleName = '';
            this.roleUsers = [];
        },
    },
});
