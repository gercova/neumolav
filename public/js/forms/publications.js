$(document).ready(function(){
    const tables = {
        postsTable: $("#post_data").DataTable({ ajax: `${API_BASE_URL}/publications/list`, processing: true, order: [] })
    };

    //Eliminar un registro
    DeleteHandler.initButtons([
        {
            selector: '.delete-post',
            endpoint: 'posts',
            table: tables.postsTable
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

    //guardar información del formulario
    $('#postForm').submit(async function(e){
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid is-valid');
        $('#postForm').find('text-danger').remove();
        const submitButton          = $(this).find('button[type="submit"]');
        const originalButtonText    = submitButton.html(); // Guardar el texto original del botón
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        const formData              = new FormData(this);

        try {
            const response = await axios.post('/publications/store', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            if (response.data.status) {
                console.log('data', response.data.data);
                Swal.fire(
                    'Operación exitosa',
                    response.data.message,
                    response.data.type
                ).then((result)=>{
                    if(result.value){
                        window.location.href = response.data.route;
                    }
                });
            } else {
                //throw new Error(response.data.message);
                swal.fire({
                    icon: 'error',
                    title: response.data.message,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar'
                });
            }
        } catch (error) {
            if (error.response && error.response.data.errors) {
                $.each(error.response.data.errors, function(key, value) {
                    let inputElement = $(document).find(`[name="${key}"]`);
                    inputElement.after(`<span class="text-danger">${value[0]}</span>`).closest('.form-control').addClass('is-invalid');
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response?.data?.message || error.message,
                });
            }
            
            return null;
        } finally {
            submitButton.prop('disabled', false).html(originalButtonText);
        }
    });
});