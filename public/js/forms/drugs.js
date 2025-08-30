$(document).ready(function(){
    const tables = {
        drugsTable: $("#drug_data").DataTable({ ajax: `${API_BASE_URL}/drugs/list`, processing: true, order: [] })
    };
    //Eliminar un registro
    DeleteHandler.initButtons([
        {
            selector: '.delete-drug',
            endpoint: 'drugs',
            table: tables.drugsTable
        }
    ]);
	//button modal farmacos
	$('#btn-drug').click(function(e){
        e.preventDefault();
		$('.form-control').removeClass('is-invalid is-valid');
		$('#drugForm').trigger('reset');
        $('#drugForm').find('.text-danger').remove();
        $('#drugId').val('');
		$('#modalDrug').modal('show');
		$('.modal-title').text('Agregar Fármaco');
    });
	//formulario farmacos
	$('#drugForm').submit(async function(e){
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid is-valid');
        $('.text-danger').remove();
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        const formData = {
            id_categoria:       $('#id_categoria').val(),
            id_presentacion:    $('#id_presentacion').val(),
            descripcion:        $('#descripcion').val(),
            detalle:            $('#detalle').val(),
            id:                 $('#drugId').val(),
        };
        try {
            const response = await axios.post(`${API_BASE_URL}/drugs/store`, formData);
            if(response.status == 200 && response.data.status == true){
                $('#drugForm').trigger('reset');
                $('#modalDrug').modal('hide');
                $('#drug_data').DataTable().ajax.reload();
                alertNotify(response.data.type, response.data.messages);
            }else if(response.status == false){
                alertNotify(response.data.type, response.data.messages);
            }
        } catch (error) {
            if(error.response && error.response.data.errors){
                $.each(error.response.data.errors, function(key, value) {
                    let inputElement = $(document).find(`[name="${key}"]`);
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
            const response = await axios.get(`${API_BASE_URL}/drugs/${id}`)
            if (response.status === 200) {
                $('.modal-title').text('Actualizar Fármaco');
                $('.text-danger').remove();
                $('.form-control').removeClass('is-invalid').removeClass('is-valid');
                $('#id_categoria').val(response.data.id_categoria);
                $('#id_presentacion').val(response.data.id_presentacion);
                $('#descripcion').val(response.data.descripcion);
                $('#detalle').val(response.data.detalle);
                $('#drugId').val(response.data.id);      
                $('#modalDrug').modal('show');
            }
        } catch (error) {
            console.log(error);
        }
    });
});