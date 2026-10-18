$(document).ready(function(){
    // Si la SPA Vue ya está montada, ceder el control a los componentes Vue
    if (window.__vueLoginMounted) {
        return;
    }

    const API_BASE_URL = window.API_BASE_URL || '';
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (window.axios && token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    }

    $(document).on('submit', '#loginForm', async function (e) {
        // Si el evento ya fue prevenido o manejado por Vue, no duplicar la solicitud
        if (window.__vueLoginMounted || e.defaultPrevented || e.isDefaultPrevented()) {
            return;
        }

        e.preventDefault();

        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid is-valid');
        $('#loginForm').find('.text-danger').remove();

        // Deshabilitar el botón de envío
        const submitButton = $('#loginButton');
        submitButton.prop('disabled', true);
        submitButton.html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Cargando...');

        try {
            // Obtener los valores del formulario
            let email    = $('#email').val();
            let password = $('#password').val();
            let remember = $('#remember').is(':checked');

            const data = { email, password, remember };

            // Enviar la solicitud con Axios
            const response = await axios.post(`${API_BASE_URL}/login`, data);

            // Procesar la respuesta
            if (response.status === 200 && response.data.status === true) {
                $('#message').html(`<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-check-circle mr-2"></i>${response.data.message}</div>`);
                
                // Guardar token CSRF / bearer en localStorage
                const tokenVal = response.data.token || response.data.csrf_token;
                if (tokenVal) {
                    localStorage.setItem('token', tokenVal);
                }

                // Disparar evento para bridge reactivo
                if (window.__neumoEvents) {
                    window.__neumoEvents.dispatchEvent(new CustomEvent('auth:login-success', { detail: response.data }));
                }

                // Redirigir al usuario
                setTimeout(function() {
                    window.location.href = response.data.redirect || '/admin/home';
                }, 350);
            } else {
                const msg = response.data?.message || 'Error en las credenciales.';
                $('#message').html(`<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-exclamation-circle mr-2"></i>${msg}</div>`);
            }
        } catch (error) {
            console.error('Error:', error);
            if (error.response && error.response.data && error.response.data.errors) {
                $.each(error.response.data.errors, function(key, value) {
                    let inputElement = $(document).find(`[name="${key}"]`);
                    inputElement.after(`<span class="text-danger small font-weight-500 d-block mt-1">${value[0]}</span>`).closest('.form-control').addClass('is-invalid');
                });
                const firstMsg = Object.values(error.response.data.errors)[0]?.[0];
                if (firstMsg) {
                    $('#message').html(`<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-exclamation-circle mr-2"></i>${firstMsg}</div>`);
                }
            } else if (error.response && error.response.data && error.response.data.message) {
                $('#message').html(`<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-exclamation-circle mr-2"></i>${error.response.data.message}</div>`);
            } else {
                $('#message').html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-exclamation-circle mr-2"></i>Error en la solicitud. Inténtalo de nuevo.</div>');
            }
        } finally {
            // Habilitar el botón de envío nuevamente
            submitButton.prop('disabled', false);
            submitButton.html('<i class="fas fa-sign-in-alt mr-1"></i> Iniciar sesión');
        }
    });
});