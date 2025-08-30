$(document).ready(function(){
    const tables = {
        usersTable: $("#user_data").DataTable({ ajax: `${API_BASE_URL}/users/list`, order: [], processing: true }),
    };
    //funciones para el formulario de ROLE en la vista security.users.role
    $(document).on('click', '.add-permission', function() {
        const row = $(this).closest('tr');
        const permissionId = row.data('id');
        
        // Mover la fila a la tabla de asignados
        row.find('.add-permission')
            .removeClass('btn-primary add-permission')
            .addClass('btn-danger remove-permission')
            .html('<i class="fas fa-minus"></i> Quitar');
        
        row.appendTo('#assignedPermissions tbody');
        
        updateHiddenInput();
    });
    
    // Quitar permiso individual
    $(document).on('click', '.remove-permission', function() {
        const row = $(this).closest('tr');
        const permissionId = row.data('id');
        
        // Mover la fila a la tabla de disponibles
        row.find('.remove-permission')
            .removeClass('btn-danger remove-permission')
            .addClass('btn-primary add-permission')
            .html('<i class="fas fa-plus"></i> Agregar');
        
        row.appendTo('#availablePermissions tbody');
        
        updateHiddenInput();
    });
    
    // Agregar todos los permisos
    $('#addAllPermissions').click(function() {
        $('#availablePermissions tbody tr').each(function() {
            const row = $(this);
            const permissionId = row.data('id');
            
            row.find('.add-permission')
                .removeClass('btn-primary add-permission')
                .addClass('btn-danger remove-permission')
                .html('<i class="bi bi-trash"></i> Quitar');
            
            row.appendTo('#assignedPermissions tbody');
        });
        
        updateHiddenInput();
    });
    
    // Quitar todos los permisos
    $('#removeAllPermissions').click(function() {
        $('#assignedPermissions tbody tr').each(function() {
            const row = $(this);
            const permissionId = row.data('id');
            
            row.find('.remove-permission')
                .removeClass('btn-danger remove-permission')
                .addClass('btn-primary add-permission')
                .html('<i class="fas fa-plus"></i> Agregar');
            
            row.appendTo('#availablePermissions tbody');
        });
        
        updateHiddenInput();
    });
    
    // Actualizar el input oculto con los IDs de los permisos seleccionados
    /*function updateHiddenInput() {
        const permissionIds = [];
        $('#assignedPermissions tbody tr').each(function() {
            permissionIds.push($(this).data('id'));
        });
        
        $('#selectedPermissions').val(permissionIds.join(','));
    }*/

    // Modificar la función updateHiddenInput para excluir permisos heredados
    function updateHiddenInput() {
        const permissionIds = [];
        $('#assignedPermissions tbody tr').each(function() {
            // Solo incluir si no es heredado
            if (!$(this).find('td:first-child .badge').length) {
                permissionIds.push($(this).data('id'));
            }
        });
        
        $('#selectedPermissions').val(permissionIds.join(','));
    }

    // Deshabilitar el botón de quitar para permisos heredados
    $(document).on('mouseover', '.remove-permission', function() {
        if ($(this).parent().find('.text-muted').length) {
            $(this).prop('disabled', true);
        }
    });
    
    // Validación antes de enviar el formulario
    /*$('#assignPermissionsForm').submit(function(e) {
        if ($('#assignedPermissions tbody tr').length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Debe asignar al menos un permiso al usuario',
            });
        }
    });*/

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

    // fin de funciones para el formulario de ROLE en la vista security.users.role
    $('#availablePermissions, #assignedPermissions, #permissionsRoleTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
    });
    //Eliminar un registro
    DeleteHandler.initButtons([
        {
            selector: '.delete-user',
            endpoint: 'users',
            table: tables.usersTable
        } 
    ]);
	//boton modal
	$('#btn-add-user').click(function(e){
        e.preventDefault();
		$('.form-control').removeClass('is-invalid is-valid');
        $('#userForm').trigger('reset');
        $('#userForm').find('.text-danger').remove();
        $('#userId').val('');
        $('#modalUser').modal('show');
        $('.modal-title').text('Agregar Usuario');
    });

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

	//formulario categoria
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
    // search permission
    $('#search_permission').autocomplete({
        source: async function (request, response) {
            try {
                const result = await axios.post(`${API_BASE_URL}/permissions/search`, {
                    q: request.term
                });
                response(result.data);
            } catch (error) {
                console.error('Error en la búsqueda:', error);
                response([]);
            }
        },
        minLength: 2,
        select: function (event, ui) {
            const data = `${ui.item.id}*${ui.item.label}`;
            $('#btn-add-permission').val(data).trigger('click');
            $('#search_permission').val('');
            return false;
        }
    });
    // Función para agregar el permiso a la lista
    $('#btn-add-permission').on('click', function () {
        const data = $(this).val();
        if (data) {
            const permission = data.split('*');
            const permissionId = permission[0];
            const permissionName = permission[1];
            if ($(`input[value="${permissionId}"]`).length > 0) {
                Swal.fire('¡Duplicado!', 'El permiso ya está en la lista.', 'warning');
                return;
            }
            const rowCount = $('#permissionTable tbody tr').length + 1;
            const html_data = `
                <tr>
                    <td>${rowCount}</td>
                    <td><input type="hidden" name="permission_id[]" value="${permissionId}">${permissionName}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-xs btn-remove-permission"><i class="bi bi-x-lg"></i></button>
                    </td>
                </tr>
            `;
            $('#permissionTable tbody').append(html_data);
            $('#btn-add-permission').val(null);
            $('#search_permission').val(null);
            alertNotify('success', `<h5>${permissionName} agregado</h5>`);
        } else {
            Swal.fire('¡Vacío!', 'Selecciona un permiso válido.', 'error');
        }
    });
    // Función para quitar las filas de la tabla de permisos
    $(document).on('click', '.btn-remove-permission', function () {
        $(this).closest('tr').remove();
        updateRowNumbers();
    });
    // Función para actualizar los números de fila
    function updateRowNumbers() {
        $('#permissionTable tbody tr').each(function (index) {
            $(this).find('td:first').text(index + 1);
        });
    }
});

$('[data-toggle="tooltip"]').tooltip();