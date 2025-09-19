$(document).ready(function(){
    let slimSelect;
    /*const manageSpecialtyTable = $("#specialty_data").DataTable({
		'ajax': `${API_BASE_URL}/specialties/list`,
		'order': [],
        'processing': true,
	});*/

    const tables = {
        specialtiesTable: $("#specialty_data").DataTable({ ajax: `${API_BASE_URL}/specialties/list`, processing: true, order: [] })
    };

    //Eliminar un registro
    DeleteHandler.initButtons([
        {
            selector: '.delete-specialty',
            endpoint: 'specialties',
            table: tables.specialtiesTable
        } 
    ]);

	//boton modal
	$('#btn-add-specialty').click(function(e){
        e.preventDefault();
        if (slimSelect) slimSelect.destroy();
        slimSelect = new SlimSelect({
            select: '#id_ocupacion',
            placeholder: 'Seleccione un cargo',
            allowDeselect: true
        });
		$('.form-control').removeClass('is-valid is-invalid');
        $('#specialtyForm').trigger('reset');
        $('#specialtyForm').find('.text-danger').remove();
        $('#specialtyId').val('');
        $('#modalSpecialty').modal('show');
        $('.modal-title').text('Agregar Especialidad');
    });
    $('#modalSpecialty').on('shown.bs.modal', function () {
        new SlimSelect({
            select: '.slim-select',
            placeholder: 'Seleccione una opción',
        });
    });
	//formulario categoria
	$('#specialtyForm').submit(async function(e){
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid is-valid');
        $('.text-danger').remove();
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        const formData = {
            id_ocupacion:   $('#id_ocupacion').val(),    
            descripcion:    $('#descripcion').val(),
            detalle:        $('#detalle').val(),
            id:             $('#specialtyId').val(),
        }
        try {
            const response = await axios.post(`${API_BASE_URL}/specialties/store`, formData);
            if(response.status == 200 && response.data.status == true){
                $('#specialtyForm').trigger('reset');
                $('.form-control').removeClass('is-invalid is-valid');
                $('#specialtyForm').find('.text-danger').remove();
                $('#modalSpecialty').modal('hide');
                $('#specialty_data').DataTable().ajax.reload();
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
        if (slimSelect) slimSelect.destroy();
        slimSelect = new SlimSelect({
            select: '#id_ocupacion',
            placeholder: 'Seleccione un cargo',
            allowDeselect: true
        });
        try {
            const response = await axios.get(`${API_BASE_URL}/specialties/${id}`);
            if(response.status == 200){
                $('.modal-title').text('Actualizar Especialidad');
                $(".text-danger").remove();
                $('.form-control').removeClass('is-invalid is-valid');
                slimSelect.set(response.data.id_ocupacion);
                $("#descripcion").val(response.data.descripcion);
                $("#detalle").val(response.data.detalle);
                $("#specialtyId").val(response.data.id);      
                $('#modalSpecialty').modal('show');
            }
        } catch (error) {
            console.log(error);
        }
    });
})