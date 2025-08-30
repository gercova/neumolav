$(document).ready(function(){
    $('#appointment-form').submit(async function(e) {
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-control').removeClass('has-error has-success');
        $('#appointment-form').find('text-danger').remove();
        
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html(); // Guardar el texto original del botón
        submitButton.prop('disabled', true).html('Enviando...');
        
        try {
            // Obtener los valores del formulario
            let name    = $('#name').val();
            let email   = $('#email').val();
            let phone   = $('#phone').val();
            let message = $('#message').val();
            // Configurar el cuerpo de la solicitud
            const data = { name, email, phone, message };
            // Enviar la solicitud con Axios (usando async/await)
            const response = await axios.post(`${API_BASE_URL}/sendmail`, data);
            // Procesar la respuesta
            if (response.status === 200 && response.data.status === true) {
                $('#result').html(`<div class="alert alert-success alert-dismissible">${response.data.message}</div>`).fadeIn().delay(3000).fadeOut();
                $('#appointment-form').trigger('reset');
                Swal.fire(
                    response.data.message,
                    response.data.text,
                    response.data.type,
                );
            } else if (response.data.status === false) {
                $('#result').html(`<div class="alert alert-danger alert-dismissible">${response.data.message}</div>`).fadeIn().delay(3000).fadeOut();
            }
        } catch (error) {
            console.error('Error:', error);
            if (error.response && error.response.data.errors) {
                $.each(error.response.data.errors, function(key, value) {
                    let inputElement = $(document).find(`[name="${key}"]`);
                    inputElement.after(`<span class="text-danger">${value[0]}</span>`).closest('.form-control').addClass('has-error');
                });
            } else {
                $('#result').html('<div class="alert alert-danger alert-dismissible">Error en la solicitud. Inténtalo de nuevo.</div>').fadeIn().delay(3000).fadeOut();
            }
        } finally {
            // Habilitar el botón de envío nuevamente
            submitButton.prop('disabled', false).html(originalButtonText);
        }

    });
});