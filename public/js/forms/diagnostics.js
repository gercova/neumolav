$(document).ready(function(){
    // Si la SPA Vue está montada o no existe la tabla legacy, no inicializar DataTables heredado
    if ($('#vue-diagnostics').length || !$('#diagnostic_data').length) {
        return;
    }

    const tables = {
        diagnosticsTable: $("#diagnostic_data").DataTable({ ajax: `${API_BASE_URL}/diagnostics/list`, processing: true, order: [] })
    };

    // Eliminar un registro
    DeleteHandler.initButtons([
        {
            selector: '.delete-diagnostic',
            endpoint: 'diagnostics',
            table: tables.diagnosticsTable
        },
    ]);

    // Botón modal
    $('#btn-diagnostic').click(function(e){
        e.preventDefault();
        $('.form-control').removeClass('is-invalid is-valid');
        $('#diagnosticForm').trigger('reset');
        $('#diagnosticForm').find('.text-danger').remove();
        $('#diagnosticId').val('');
        $('#modalDiagnostic').modal('show');
        $('.modal-title').text('Agregar Diagnóstico');
    });

    // Formulario diagnóstico
    $('#diagnosticForm').submit(async function(e){
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid is-valid');
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        const formData = {
            descripcion: $('#descripcion').val(),
            id: $('#diagnosticId').val(),
        };
        try {
            const response = await axios.post(`${API_BASE_URL}/diagnostics/store`, formData);
            if(response.status == 200 && response.data.status == true){
                $('#diagnosticForm').trigger('reset');
                $('#modalDiagnostic').modal('hide');
                $('#diagnostic_data').DataTable().ajax.reload();
                alertNotify(response.data.type, response.data.messages);
            } else if(response.data && response.data.status === false){
                alertNotify(response.data.type || 'error', response.data.messages);
            }
        } catch (error) {
            if(error.response && error.response.data && error.response.data.errors) {
                $.each(error.response.data.errors, function(key, value) {
                    let inputElement = $(document).find(`[name="${key}"]`);
                    inputElement.after(`<span class="text-danger small font-weight-500 d-block mt-1">${value[0]}</span>`).closest('.form-control').addClass('is-invalid');
                });
            } else {
                alertNotify('error', 'Ocurrió un error al procesar la solicitud.');
            }
        } finally {
            submitButton.prop('disabled', false).html(originalButtonText);
        }
    });

    // Actualizar registro
    $(document).on('click', '.update-row', async function(e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const response = await axios.get(`${API_BASE_URL}/diagnostics/${id}`);
            if(response.status == 200) {
                $('.modal-title').text('Actualizar Diagnóstico');
                $(".text-danger").remove();
                $('.form-control').removeClass('is-invalid is-valid');
                $("#descripcion").val(response.data.descripcion);
                $('#diagnosticId').val(response.data.id);      
                $('#modalDiagnostic').modal('show');
            }
        } catch (error) {
            console.log(error);
        }
    });
});