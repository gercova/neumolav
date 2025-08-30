let manageModulesTable;
$(document).ready(function() {
    manageModulesTable = $("#module_data").DataTable({
        'ajax': `${API_BASE_URL}/modules/list`,
        'order': [],
        'processing': true,
    });
    //boton modal Module
	$('#btn-add-module').click(function(e){
        e.preventDefault();
		$('.form-control').removeClass('is-valid').removeClass('is-invalid');
		$('#moduleForm').trigger('reset');
        $('#moduleForm').find('.text-danger').remove();
        $('#moduleId').val('');
        $('#modalModule').modal('show');
        $('.modal-title').text('Agregar Módulo');
    });
    //boton modal Submodule
	$('#btn-add-submodule').click(function(e){
        e.preventDefault();
		$('.form-control').removeClass('is-valid').removeClass('is-invalid');
		$('#submoduleForm').trigger('reset');
        $('#submoduleForm').find('.text-danger').remove();
        $('#submoduleId').val('');
        $('#modalSubmodule').modal('show');
        $('.modal-title').text('Agregar Submódulo');
    });
    //form module
	$('#moduleForm').submit(async function(e){        
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid').removeClass('is-valid');
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html(); // Guardar el texto original del botón
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        const formData = $('#moduleForm').serialize();
        try {
            const response = await axios.post(`${API_BASE_URL}/modules/storeModule`, formData);
            if(response.status == 200 && response.data.status == true){
                $(this).trigger('reset');
                $('#modalModule').modal('hide');
                $('#module_data').DataTable().ajax.reload();
                alertNotify(response.data.type, response.data.messages);
            }else if(response.data.status == false){
                alertNotify(response.data.type, response.data.messages);
            }
        } catch (error) {
            if (error.response && error.response.data.errors) {
                $.each(error.response.data.errors, function(key, value) {
                    let inputElement = $(document).find(`[name="${key}"]`);
                    inputElement.after(`<span class="text-danger">${value[0]}</span>`).closest('.form-control').addClass('is-invalid');
                });
            } else {
                alertNotify('error', 'Ocurrió un error al procesar la solicitud.');
            }
        } finally {
            submitButton.prop('disabled', false).html(originalButtonText);
        }
    });
    //form module
    $('#submoduleForm').submit(async function (e) {
        e.preventDefault();
        // Limpiar errores y estilos de validación
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid is-valid');
        // Obtener el botón de guardar y deshabilitarlo
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html(); // Guardar el texto original del botón
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...'); // Mostrar "Cargando..."
        try {
            // Serializar los datos del formulario
            const formData = $(this).serialize();
            // Enviar la solicitud POST con Axios
            const response = await axios.post(`${API_BASE_URL}/modules/storeSubmodule`, formData);
            if (response.status === 200 && response.data.status === true) {
                // Restablecer el formulario y cerrar el modal
                $(this).trigger('reset');
                $('#modalSubmodule').modal('hide');
                // Recargar la tabla DataTable
                $('#module_data').DataTable().ajax.reload();
                // Mostrar notificación de éxito
                alertNotify(response.data.type, response.data.messages);
            } else if (response.data.status === false) {
                // Mostrar notificación de error
                alertNotify(response.data.type, response.data.messages);
            }
        } catch (error) {
            // Mostrar errores de validación en el formulario
            if (error.response && error.response.data.errors) {
                $.each(error.response.data.errors, function (key, value) {
                    let inputElement = $(document).find(`[name="${key}"]`);
                    inputElement.after(`<span class="text-danger">${value[0]}</span>`)
                        .closest('.form-control')
                        .addClass('is-invalid');
                });
            } else {
                // Mostrar un mensaje de error genérico si no hay errores de validación
                alertNotify('error', 'Ocurrió un error al procesar la solicitud.');
            }
        } finally {
            // Restaurar el botón a su estado original
            submitButton.prop('disabled', false).html(originalButtonText);
        }
    });
	//function get module by id
    $(document).on('click', '.update-row-module', async function(e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const response = await axios.get(`${API_BASE_URL}/modules/module/${id}`);
            if(response.status == 200){
                $('.modal-title').text('Actualizar Módulo');
                $(".text-danger").remove();
                $('.form-control').removeClass('is-invalid is-valid');
                $("#descripcion").val(response.data.descripcion);
                $("#detalle").val(response.data.detalle);
                $("#icono").val(response.data.icono);
                $('#moduleId').val(response.data.id);      
                $('#modalModule').modal('show');
            }
        } catch (error) {
            console.log(error);
        }
    });
    //function get submodule by id
    $(document).on('click', '.update-row-submodule', async function(e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const response = await axios.get(`${API_BASE_URL}/modules/submodule/${id}`);
            if (response.status == 200) {
                console.log(response.data);
                $('.modal-title').text('Actualizar Submódulo');
                $(".text-danger").remove();
                $('.form-control').removeClass('is-invalid is-valid');
                $("#module_id").val(response.data.module_id);
                $("#sm_descripcion").val(response.data.descripcion);
                $("#sm_nombre").val(response.data.nombre);
                $("#sm_detalle").val(response.data.detalle);
                $("#sm_icono").val(response.data.icono);
                $('#submoduleId').val(response.data.id);      
                $('#modalSubmodule').modal('show');
            }   
        } catch (error) {
            console.log(error);
        }
    });
    //function delete module
    $(document).on('click', '.delete-row-module', async function(e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const result = await swal.fire({
                title: '¿Estás seguro de hacerlo?',
                text: 'Este registro será borrado',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Sí, borrarlo'
            });
            if (result.isConfirmed) {
                const response = await axios.delete(`${API_BASE_URL}/modules/module/${id}`);
                if(response.status == 200){
                    if(response.status == 200 && response.data.status == true){
                        alertNotify(response.data.type, response.data.message);
                        $('#module_data').DataTable().ajax.reload();
                    }else{
                        alertNotify(response.data.type, response.data.message);
                    }
                } else {
                    alertNotify('error', 'Ocurrió un error al procesar la solicitud.');
                }
            }
        } catch (error) {
            console.log(error);
        }
    });

    //function delete submodule
    $(document).on('click', '.delete-row-submodule', async function(e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const result = await swal.fire({
                title: '¿Estás seguro de hacerlo?',
                text: 'Este registro será borrado',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Sí, borrarlo'
            });
            if (result.isConfirmed) {
                const response = await axios.delete(`${API_BASE_URL}/modules/submodule/${id}`);
                if(response.status == 200 && response.data.status == true){
                    if(response.status == 200 && response.data.status == true){
                        alertNotify(response.data.type, response.data.message);
                        $('#module_data').DataTable().ajax.reload();
                    }else{
                        alertNotify(response.data.type, response.data.message);
                    }
                }
            }
        } catch (error) {
            console.log(error);
        }
    });
});