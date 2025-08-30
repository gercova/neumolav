let managePermissionsTable;

$(document).ready(function(){
    managePermissionsTable = $("#permission_data").DataTable({
		'ajax': `${API_BASE_URL}/permissions/list`,
		'order': [],
        'processing': true,
	});
	//boton modal permisos
	$('#btn-add-permission').click(function(e){
        e.preventDefault();
        $('#permissionForm').trigger('reset');
        $('.form-group').removeClass('is-invalid is-valid');
        $('#permissionForm').find('.text-danger').remove();
        $('#permissionId').val('');
        $('#modalPermission').modal('show');
        $('.modal-title').text('Agregar Permiso');
    });
	//formulario permisos
	$('#permissionForm').submit(async function(e){
        e.preventDefault();
        $('.form-group').removeClass('is-invalid is-valid');
        $('#permissionForm').find('.text-danger').remove();
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html(); // Guardar el texto original del botón
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        const formData = $('#permissionForm').serialize();
        try {
            const response = await axios.post(`${API_BASE_URL}/permissions/store`, formData);
            if(response.status == 200 && response.data.status == true){
                $('#permissionForm').trigger('reset');
                $('#modalPermission').modal('hide');
                $('#permission_data').DataTable().ajax.reload();
                alertNotify(response.data.type, response.data.messages);
            }else if(response.data.status == false){
                alertNotify(response.data.type, response.data.messages);
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
	//update item permiso
    $(document).on('click', '.update-row', async function(e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const response = await axios.get(`${API_BASE_URL}/permissions/${id}`);
            if (response.status == 200) {
                $('.modal-title').text('Actualizar Permiso');
                $(".text-danger").remove();
                // remove the form error
                $('.form-group').removeClass('is-invalid is-valid');
                $("#name").val(response.data.name);
                $("#guard_name").val(response.data.guard_name);
                $('#permissionId').val(id);
                $('#modalPermission').modal('show');
            };
        } catch (error) {
            console.log(error);   
        }
    });
    //delete item permiso
    $(document).on('click', '.delete-row', async function(e) {
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
                    alertNotify(response.data.type, response.data.messages);
                    $('#permissions_data').DataTable().ajax.reload();
                } else {
                    alertNotify(response.data.type, response.data.messages);
                }
            }  
        } catch (error) {
            console.log(error);
        }
    });
});