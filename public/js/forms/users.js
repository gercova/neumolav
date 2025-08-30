$(document).ready(function(){
    const tables = {
        usersTable: $("#user_data").DataTable({ ajax: `${API_BASE_URL}/users/list`, order: [], processing: true }),
    };
    
    // Validación antes de enviar el formulario
    $('#assignPermissionsForm').submit(function(e) {
        if ($('#assignedPermissions tbody tr').length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Debe asignar al menos un permiso al usuario',
            });
        }
    });

    // En el submit del formulario
    $('#assignPermissionsForm').submit(async function(e) {
        e.preventDefault(); // Agrega esto para prevenir el submit normal
        
        const userId = $('#userId').val();
        const selectedCount = $('#assignedPermissions tbody tr').not(':has(.text-muted)').length;
        
        if (selectedCount === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Debe asignar al menos un permiso directo al usuario',
            });
            return false;
        }

        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        
        try {
            const response = await axios.post(
                `${API_BASE_URL}/users/storePermission/${userId}`, 
                $(this).serialize() // Mejor usar serialize() para forms
            );
            
            if(response.data.status){
                Swal.fire({
                    icon: 'success',
                    title: 'Operación exitosa',
                    text: response.data.message,
                }).then(() => {
                    window.location.href = response.data.route;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.data.message,
                });
            }
            
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error del servidor',
                text: 'Intente nuevamente más tarde',
            });
        } finally {
            submitButton.prop('disabled', false).html(originalButtonText);
        }
    });

    //Eliminar un registro
    DeleteHandler.initButtons([
        {
            selector: '.delete-user',
            endpoint: 'users',
            table: tables.usersTable
        } 
    ]);

    // Mostrar nombre de archivo en el input
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
        
        // Vista previa de la imagen
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });

	//formulario usuario
	$('#userForm').submit(async function(e){
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid is-valid');	
        $('#userForm').find('.text-danger').remove();
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html(); // Guardar el texto original del botón
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        const formData = new FormData(this);
        try {
            const response = await axios.post(`${API_BASE_URL}/users/store`, formData);
            if(response.status == 200 && response.data.status == true){
                $('.text-danger').remove();
                $('.form-group').removeClass('is-invalid is-valid');
                Swal.fire(
                    'Operación exitosa',
                    response.data.messages,
                    response.data.type
                ).then((result)=>{
                    if(result.value){
                        window.location.href = response.data.route;
                    }
                });
            }else if(response.data.status == false){
                swal.fire({
                    icon: 'error',
                    title: response.data.messages,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar'
                });
            }
        } catch (error) {
            if(error.response && error.response.data.errors){
                $.each(error.response.data.errors, function(key, value) {
                    let inputElement = $(document).find('[name="' + key + '"]');
                    inputElement.after('<span class="text-danger">' + value[0] + '</span>').closest('.form-control').addClass('is-invalid').focus();
                });
            }
        } finally {
            submitButton.prop('disabled', false).html(originalButtonText);
        }
    });

    //formulario actualizar contraseña
    $('#passwordForm').submit(async function(e) {
        const userId = $('#userId').val();
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid is-valid');	
        $('#passwordForm').find('.text-danger').remove();
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html(); // Guardar el texto original del botón
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        const formData = $(this).serialize();
        try {
            const response = await axios.post(`${API_BASE_URL}/users/storePassword/${userId}`, formData);
            if(response.status == 200 && response.data.status == true){
                $('.text-danger').remove();
                $('.form-group').removeClass('is-invalid is-valid');
                Swal.fire(
                    'Operación exitosa',
                    response.data.messages,
                    response.data.type
                ).then((result)=>{
                    if(result.value){
                        window.location.href = response.data.route;
                    }
                });
            }else if(response.data.status == false){
                swal.fire({
                    icon: 'error',
                    title: response.data.messages,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar'
                });
            }
        } catch (error) {
            if(error.response && error.response.data.errors){
                $.each(error.response.data.errors, function(key, value) {
                    let inputElement = $(document).find('[name="' + key + '"]');
                    inputElement.after('<span class="text-danger">' + value[0] + '</span>').closest('.form-control').addClass('is-invalid').focus();
                });
            }
        } finally {
            submitButton.prop('disabled', false).html(originalButtonText);
        }
    })
    
});