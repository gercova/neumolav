// ⚠️ NO importes jquery. El layout ya carga jQuery clásico global.
//    Tu única responsabilidad aquí es Axios + interceptores + eventos puente.

import axios from 'axios';

window.axios = axios;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

axios.interceptors.response.use(
    r => r,
    error => {
        const { response } = error;
        if (response?.status === 419) { window.location.reload(); return Promise.reject(error); }
        if (response?.status === 401) { window.location.href = '/login'; return Promise.reject(error); }
        if (response?.status === 403) {
            window.Swal?.fire({
                icon: 'error',
                title: 'Acceso denegado',
                text: response.data?.message ?? 'Sin permiso.',
            });
        }
        return Promise.reject(error);
    }
);

// Puente de eventos Vue ↔ jQuery legacy
window.__neumoEvents = window.__neumoEvents || new EventTarget();