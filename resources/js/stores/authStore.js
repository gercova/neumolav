import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        email: '',
        password: '',
        remember: false,
        passwordVisible: false,
        loading: false,
        alert: {
            visible: false,
            type: 'info', // 'success' | 'danger' | 'warning' | 'info'
            message: '',
        },
        fieldErrors: {},
        appName: 'Neumotar',
        logoUrl: null,
        faviconUrl: null,
        subtitle: 'Especialista en enfermedades respiratorias',
        redirectUrl: '/admin/home',
        routes: {
            login: '/login',
            home: '/admin/home',
            logout: '/logout',
        },
    }),

    getters: {
        hasErrors: (state) => Object.keys(state.fieldErrors).length > 0,
        isSubmitDisabled: (state) => state.loading || !state.email || !state.password,
    },

    actions: {
        initState(initialState = {}) {
            if (initialState.appName) this.appName = initialState.appName;
            if (initialState.logoUrl !== undefined) this.logoUrl = initialState.logoUrl;
            if (initialState.faviconUrl !== undefined) this.faviconUrl = initialState.faviconUrl;
            if (initialState.subtitle) this.subtitle = initialState.subtitle;
            if (initialState.redirectUrl) this.redirectUrl = initialState.redirectUrl;
            if (initialState.routes) this.routes = { ...this.routes, ...initialState.routes };

            if (initialState.csrfToken) {
                const metaCsrf = document.querySelector('meta[name="csrf-token"]');
                if (metaCsrf) {
                    metaCsrf.setAttribute('content', initialState.csrfToken);
                }
                if (window.axios) {
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = initialState.csrfToken;
                }
            }
        },

        togglePasswordVisibility() {
            this.passwordVisible = !this.passwordVisible;
        },

        clearFieldError(field) {
            if (this.fieldErrors[field]) {
                const copy = { ...this.fieldErrors };
                delete copy[field];
                this.fieldErrors = copy;
            }
        },

        clearAlert() {
            this.alert.visible = false;
            this.alert.message = '';
        },

        showAlert(type, message) {
            this.alert = {
                visible: true,
                type,
                message,
            };
        },

        async login() {
            this.loading = true;
            this.clearAlert();
            this.fieldErrors = {};

            // Validación previa en cliente
            const errors = {};
            if (!this.email) {
                errors.email = ['El campo E-mail es requerido'];
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
                errors.email = ['El campo E-mail debe ser un correo válido'];
            }

            if (!this.password) {
                errors.password = ['El campo Contraseña es requerido'];
            } else if (this.password.length < 6) {
                errors.password = ['El campo Contraseña debe tener al menos 6 caracteres'];
            }

            if (Object.keys(errors).length > 0) {
                this.fieldErrors = errors;
                this.showAlert('danger', 'Por favor complete correctamente las credenciales.');
                this.loading = false;
                return { success: false, validationClient: true };
            }

            try {
                const payload = {
                    email: this.email,
                    password: this.password,
                    remember: this.remember,
                };

                const baseUrl = window.API_BASE_URL || '';
                const url = this.routes.login || `${baseUrl}/login`;

                const response = await axios.post(url, payload);

                if (response.status === 200 && response.data?.status === true) {
                    const message = response.data.message || 'Inicio de sesión exitoso.';
                    this.showAlert('success', message);

                    // Actualizar token CSRF y almacenamiento local
                    const tokenValue = response.data.token || response.data.csrf_token;
                    if (tokenValue) {
                        try {
                            localStorage.setItem('token', tokenValue);
                        } catch (e) {
                            console.warn('LocalStorage error:', e);
                        }
                    }

                    if (response.data.csrf_token) {
                        const metaCsrf = document.querySelector('meta[name="csrf-token"]');
                        if (metaCsrf) {
                            metaCsrf.setAttribute('content', response.data.csrf_token);
                        }
                        if (window.axios) {
                            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = response.data.csrf_token;
                        }
                    }

                    // Puente con listeners legacy o globales
                    if (window.__neumoEvents) {
                        window.__neumoEvents.dispatchEvent(
                            new CustomEvent('auth:login-success', { detail: response.data })
                        );
                    }

                    // Redirección hacia el dashboard / home
                    const targetUrl = response.data.redirect || this.redirectUrl || '/admin/home';
                    setTimeout(() => {
                        window.location.href = targetUrl;
                    }, 350);

                    return { success: true, data: response.data };
                }

                const fallbackMsg = response.data?.message || 'Error en las credenciales.';
                this.showAlert('danger', fallbackMsg);
                return { success: false, message: fallbackMsg };
            } catch (error) {
                console.error('Error durante autenticación:', error);

                if (error.response) {
                    const status = error.response.status;
                    const data = error.response.data || {};

                    if (status === 422 && data.errors) {
                        this.fieldErrors = data.errors;
                        const firstMsg = Object.values(data.errors)[0]?.[0] || 'Error en los datos enviados.';
                        this.showAlert('danger', firstMsg);
                    } else if (status === 401) {
                        const msg = data.message || 'Credenciales inválidas.';
                        this.showAlert('danger', msg);
                        this.fieldErrors = { email: [msg] };
                    } else if (status === 403) {
                        this.showAlert('danger', data.message || 'Tu cuenta está desactivada.');
                    } else if (status === 429) {
                        const msg = data.message || data.errors?.email?.[0] || 'Demasiados intentos de acceso. Por favor espere unos momentos.';
                        this.showAlert('warning', msg);
                    } else {
                        this.showAlert('danger', data.message || 'Ocurrió un error al procesar la solicitud.');
                    }
                } else {
                    this.showAlert('danger', 'Error de red o conexión al servidor. Inténtalo de nuevo.');
                }

                return { success: false, error };
            } finally {
                this.loading = false;
            }
        },
    },
});
