$(document).ready(function(){
    const tables = {
        presentationsTable: $("#presentation_data").DataTable({ ajax: `${API_BASE_URL}/presentations/list`, processing: true, order: [] }) 
    }
    //Eliminar un registro
    DeleteHandler.initButtons([
        {
            selector: '.delete-presentation',
            endpoint: 'presentations',
            table: tables.presentationsTable
        }
    ]);
    //boton modal presentacion farmacos
	$('#btn-tpd').click(function(e){
        e.preventDefault();
		$('.form-control').removeClass('is-invalid is-valid');
        $('#presentationForm').trigger('reset');
        $('#tdpId').val('');
		$('#modalTDP').modal('show');
		$('.modal-title').text('Agregar Presentación de Fármaco');
    });
    //formulario tipo presentación de fármaco
	$('#presentationForm').submit(async function(e){
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid is-valid');
        $('#presentationForm').find('text-danger').remove();
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html(); // Guardar el texto original del botón
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        const formData = {
            descripcion: $('#descripcion').val(),
            aka: $('#aka').val(),
            id: $('#tdpId').val(),
        };
        try {
            const response = await axios.post(`${API_BASE_URL}/presentations/store`, formData);
            if(response.status == 200 && response.data.status == true){
                $('#presentationForm').trigger('reset');
                $('#modalTDP').modal('hide');
                $('#presentation_data').DataTable().ajax.reload();
                alertNotify(response.data.type, response.data.messages);
            }else if(response.data.success == false){
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
            const response = await axios.get(`${API_BASE_URL}/presentations/${id}`);
            if (response.status == 200) {
                $('.modal-title').text('Actualizar Fármaco');
                $('.form-control').removeClass('is-invalid is-valid');
                $('#presentationForm').find(".text-danger").remove();
                $("#descripcion").val(response.data.descripcion);
                $("#aka").val(response.data.aka);
                $("#tdpId").val(response.data.id);      
                $('#modalTDP').modal('show');
            }
        } catch (error) {
            console.error(error);
        }
    });
    //delete item
    /*$(document).on('click', '.delete-row', async function(e) {
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
                const response = await axios.delete(`${API_BASE_URL}/presentations/${id}`);
                if (response.status == 200) {
                    alertNotify(response.data.type, response.data.messages);
                    $('#tdp_data').DataTable().ajax.reload();
                } else {
                    alertNotify(response.data.type, response.data.messages);
                }
            }
        } catch (error) {
            console.error(error);
        }
    });*/
});