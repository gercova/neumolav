let managePermissionsTable;

$(document).ready(function(){
    // Si la aplicación Vue SPA está montada en la vista, se omite la inicialización de DataTables / jQuery
    if ($('#vue-permissions').length > 0) {
        return;
    }

    managePermissionsTable = $("#permission_data").DataTable({
        'ajax': `${API_BASE_URL}/permissions/list`,
        'order': [],
        'processing': true,
    });

    // Botón modal agregar permiso
    $('#btn-add-permission').click(function(e){
        e.preventDefault();
        $('#permissionForm').trigger('reset');
        $('.form-group').removeClass('is-invalid is-valid');
        $('#permissionForm').find('.text-danger').remove();
        $('#permissionId').val('');
        $('#guard_name').val('web');
        $('#modalPermission').modal('show');
        $('.modal-title').text('Agregar Permiso');
    });

    // Formulario permisos
    $('#permissionForm').submit(async function(e){
        e.preventDefault();
        $('.form-group').removeClass('is-invalid is-valid');
        $('#permissionForm').find('.text-danger').remove();
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        
        const formData = {
            id:          $('#permissionId').val() || undefined,
            name:        $('#name').val(),
            description: $('#description').val(),
            guard_name:  $('#guard_name').val() || 'web',
        };

        try {
            const response = await axios.post(`${API_BASE_URL}/permissions/store`, formData);
            if(response.status == 200 && response.data.status == true){
                $('#permissionForm').trigger('reset');
                $('#modalPermission').modal('hide');
                $('#permission_data').DataTable().ajax.reload();
                alertNotify(response.data.type, response.data.messages || response.data.message);
            }else if(response.data.status == false){
                alertNotify(response.data.type, response.data.messages || response.data.message);
            }
        } catch (error) {
            if(error.response && error.response.data.errors){
                $.each(error.response.data.errors, function(key, value) {
                    const inputElement = $(document).find(`[name="${key}"]`);
                    inputElement.after(`<span class="text-danger">${value[0]}</span>`).closest('.form-control').addClass('is-invalid');
                });
            }else {
                alertNotify('error', 'Ocurrió un error al procesar la solicitud.');
            }
        } finally {
            submitButton.prop('disabled', false).html(originalButtonText);
        }
    });

    // Actualizar item permiso
    $(document).on('click', '.update-row', async function(e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const response = await axios.get(`${API_BASE_URL}/permissions/${id}`);
            if (response.status == 200) {
                $('.modal-title').text('Actualizar Permiso');
                $(".text-danger").remove();
                $('.form-group').removeClass('is-invalid is-valid');
                $("#name").val(response.data.name);
                $("#description").val(response.data.description || '');
                $("#guard_name").val(response.data.guard_name || 'web');
                $('#permissionId').val(id);
                $('#modalPermission').modal('show');
            }
        } catch (error) {
            console.log(error);   
        }
    });

    // Eliminar item permiso
    $(document).on('click', '.delete-permission, .delete-row', async function(e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const result = await Swal.fire({
                title: '¿Estás seguro de hacerlo?',
                text: '¡De borrar este permiso!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, borrarlo'
            });
            if (result.isConfirmed) {
                const response = await axios.delete(`${API_BASE_URL}/permissions/${id}`);
                if(response.status == 200 && response.data.status == true){
                    alertNotify(response.data.type, response.data.messages || response.data.message);
                    $('#permission_data').DataTable().ajax.reload();
                } else {
                    alertNotify(response.data.type, response.data.messages || response.data.message);
                }
            }  
        } catch (error) {
            console.log(error);
        }
    });
});