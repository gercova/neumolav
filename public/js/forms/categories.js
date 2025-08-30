$(document).ready(function(){
    const tables = {
        categoriesTable: $("#category_data").DataTable({ ajax: `${API_BASE_URL}/categories/list`, processing: true, order: [] })
    };

    //Eliminar un registro
    DeleteHandler.initButtons([
        {
            selector: '.delete-category',
            endpoint: 'categories',
            table: tables.categoriesTable 
        } 
    ])
	//boton modal
	$('#btn-add-category').click(function(e){
        e.preventDefault();
		$('.form-control').removeClass('is-valid is-invalid');
		$('#categoryForm').trigger('reset');
        $('#categoryForm').find('.text-danger').remove();
        $('#categoryId').val('');
        $('#modalCategory').modal('show');
        $('.modal-title').text('Agregar Categoría');
    });
	//form category
	$('#categoryForm').submit(async function(e){
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid is-valid');
        $('#categoryForm').find('text-danger').remove();
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html(); // Guardar el texto original del botón
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        const formData = {
            descripcion: $('#descripcion').val(),
            detalle: $('#detalle').val(),
            id: $('#categoryId').val(),
        };
        try {
            const response = await axios.post(`${API_BASE_URL}/categories/store`, formData);
            if(response.status == 200 && response.data.status == true){
                $('#categoryForm').trigger('reset');
                $('#modalCategory').modal('hide');
                $('#category_data').DataTable().ajax.reload();
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
	//function get category by id
    $(document).on('click', '.update-row', async function(e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const response = await axios.get(`${API_BASE_URL}/categories/${id}`);
            if (response.status === 200) {
                $('.modal-title').text('Actualizar Categoría');
                $(".text-danger").remove();
                $('.form-control').removeClass('is-invalid is-valid');
                $("#descripcion").val(response.data.descripcion);
                $("#detalle").val(response.data.detalle);
                $('#categoryId').val(response.data.id);      
                $('#modalCategory').modal('show');
            }
        } catch (error) {
            console.log(err);
        }
    });
});