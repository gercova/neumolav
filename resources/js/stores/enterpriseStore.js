import { defineStore } from 'pinia';
import axios from 'axios';

const API = () => window.API_BASE_URL || '/admin';

export const useEnterpriseStore = defineStore('enterprise', {
    state: () => ({
        enterprise: {
            id: 1,
            razon_social: '',
            nombre_comercial: '',
            ruc: '',
            email: '',
            pais: '',
            codigo_pais: '',
            telefono: '',
            pagina_web: '',
            representante_legal: '',
            slogan: '',
            frase: '',
            descripcion: '',
            mision: '',
            vision: '',
            ubigeo: '',
            direccion: '',
            rubro: '',
            fecha_creacion: '',
            autocomplete: 'on',
            iframe_location: '',
        },
        images: {
            foto_representante: null,
            logo: null,
            logo_mini: null,
            logo_receta: null,
        },
        activeTab: 'general',
        loading: false,
        savingGeneral: false,
        uploading: {
            foto_representante: false,
            logo: false,
            logo_mini: false,
            logo_receta: false,
        },
        permissions: {
            update: false,
        },
    }),

    actions: {
        init({ enterprise = {}, images = {}, permissions = {} }) {
            if (enterprise && Object.keys(enterprise).length > 0) {
                this.enterprise = { ...this.enterprise, ...enterprise };
            }
            if (images && Object.keys(images).length > 0) {
                this.images = { ...this.images, ...images };
            }
            if (permissions && Object.keys(permissions).length > 0) {
                this.permissions = { ...this.permissions, ...permissions };
            }
        },

        setActiveTab(tab) {
            this.activeTab = tab;
        },

        async fetchEnterprise() {
            this.loading = true;
            try {
                const { data } = await axios.get(`${API()}/enterprise/data`);
                if (data.status && data.data) {
                    this.enterprise = { ...this.enterprise, ...data.data };
                    if (data.images) {
                        this.images = { ...this.images, ...data.images };
                    }
                    if (data.permissions) {
                        this.permissions = { ...this.permissions, ...data.permissions };
                    }
                }
            } catch (error) {
                console.error('Error al cargar datos de empresa:', error);
                window.alertNotify?.('error', 'Error al cargar los datos de la empresa');
            } finally {
                this.loading = false;
            }
        },

        async saveGeneral(formData) {
            this.savingGeneral = true;
            try {
                const payload = {
                    op: 1,
                    id: this.enterprise.id,
                    ...formData,
                };

                const { data } = await axios.post(`${API()}/enterprise/store`, payload);
                if (data.status) {
                    if (data.data) {
                        this.enterprise = { ...this.enterprise, ...data.data };
                    }
                    window.alertNotify?.(data.type || 'success', data.messages || 'Información actualizada exitosamente');
                    return { success: true, data };
                } else {
                    window.alertNotify?.(data.type || 'error', data.messages || 'No se pudo actualizar la empresa');
                    return { success: false, data };
                }
            } catch (error) {
                console.error('Error al guardar datos generales:', error);
                const msg = error.response?.data?.messages || 'Ocurrió un error al guardar la información';
                window.alertNotify?.('error', msg);
                return { success: false, error };
            } finally {
                this.savingGeneral = false;
            }
        },

        async uploadImage({ op, fieldName, file, imageKey }) {
            if (!file) return { success: false };

            // Validación de tamaño (máx 2MB)
            if (file.size > 2 * 1024 * 1024) {
                window.alertNotify?.('warning', 'La imagen no debe superar los 2MB de peso');
                return { success: false, message: 'La imagen supera los 2MB' };
            }

            this.uploading[imageKey] = true;
            try {
                const formData = new FormData();
                formData.append('op', op);
                formData.append('id', this.enterprise.id);
                formData.append(fieldName, file);

                const { data } = await axios.post(`${API()}/enterprise/store`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });

                if (data.status) {
                    if (data.image_url) {
                        this.images[imageKey] = data.image_url;
                    }
                    window.alertNotify?.(data.type || 'success', data.messages || 'Imagen actualizada exitosamente');
                    return { success: true, data };
                } else {
                    window.alertNotify?.(data.type || 'error', data.messages || 'Error al actualizar la imagen');
                    return { success: false, data };
                }
            } catch (error) {
                console.error(`Error al subir imagen (${fieldName}):`, error);
                const msg = error.response?.data?.messages || 'Error al subir la imagen';
                window.alertNotify?.('error', msg);
                return { success: false, error };
            } finally {
                this.uploading[imageKey] = false;
            }
        },
    },
});
