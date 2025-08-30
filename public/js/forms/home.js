$(document).ready(function(){
    
    $("#quotes_data").DataTable({ ajax: `${API_BASE_URL}/qoutes`, processing: true, order: []})

    $(document).on('click', '.changeStatus', async function(e) {
        e.preventDefault();
        let id = $(this).attr('value');
        const result = await Swal.fire({
            title: '¿Estás seguro de cambiar el estado de la cita?',
            text: 'Verifica que el paciente haya sido atendido',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar estado',
            cancelButtonText: 'Cancelar'
        });
        if (result.isConfirmed) {
            const response = await axios.get(`${API_BASE_URL}/checkStatusPatient/${id}`);
            console.log(response);
            //return;
            if(response.status === 200){
                // Swal.fire('¡Hecho!', res.messages, 'success');
                alertNotify(response.data.type, response.data.messages);
                $('#quotes_data').DataTable().ajax.reload();
            }else{
                Swal.fire('¡Upsss!', response.messages, 'error')
            }
        }
    });
});