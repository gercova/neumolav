$(document).ready(function(){
    const tables = {
        occupationsTable: $("#occupation_data").DataTable({ ajax: `${API_BASE_URL}/occupations/list`, processing: true, order: [] })
    };
    //Eliminar un registro
    DeleteHandler.initButtons([
        {
            selector: '.delete-occupation',
            endpoint: 'occupations',
            table: tables.occupationsTable
        } 
    ]);
	//boton modal
	$('#btn-occupation').click(function(e){
        e.preventDefault();
		$('.form-control').removeClass('is-invalid').removeClass('is-valid');
		$('#occupationForm').trigger('reset');
        $('#occupationForm').find('.text-danger').remove();
        $('#occupationId').val('');
        $('#modalOccupation').modal('show');
        $('.modal-title').text('Agregar Ocupación');
    });
	//formulario categoria
	$('#occupationForm').submit(async function(e){
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid').removeClass('is-valid');
        $('#occupationForm').find('.text-danger').remove();
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html(); // Guardar el texto original del botón
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        const formData = {
            descripcion : $('#descripcion').val(),
            id          : $('#occupationId').val(),
        };
        try {
            const response = await axios.post(`${API_BASE_URL}/occupations/store`, formData);
            if(response.status == 200 && response.data.status == true){
                $('#occupationForm').trigger('reset');
                $('#modalOccupation').modal('hide');
                $('#occupation_data').DataTable().ajax.reload();
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
            }
        } finally {
            submitButton.prop('disabled', false).html(originalButtonText);
        }
    });
	//update item
    $(document).on('click', '.update-row', async function(e) {
        e.preventDefault();
        const id = $(this).attr('value');
        try {
            const response = await axios.get(`${API_BASE_URL}/occupations/${id}`);
            if(response.status == 200){
                $('.modal-title').text('Actualizar Ocupación');
                $(".text-danger").remove();
                $('.form-control').removeClass('is-invalid is-valid');
                $("#descripcion").val(response.data.descripcion);
                $("#occupationId").val(response.data.id); 
                $('#modalOccupation').modal('show');
            }
        } catch (error) {
            console.log(error);
        }
    });
});