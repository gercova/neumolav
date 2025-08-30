$(document).ready(function(){
    //validar dni
    $("#dni").change(function() {
        let dni = $(this).val();
        let regex = /^\d{8}$/;
        if (!regex.test(dni)) {
            swal.fire({
                icon: 'error',
                title: 'Ingrese un número de 8 dígitos',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Aceptar',
            });
            $('#dni').addClass('is-invalid').focus();
        }else{
            $('#dni').removeClass('is-invalid');
        }
    });
    //tipear número DNI
    $('#dni').change(function(){
        let doc_type    = $('#id_td').val();
        let dni         = $(this).val();
        if (doc_type == ''){
            Swal.fire('Upsss!!!', 'Seleccione el Tipo de documento', 'warning')
            $('#id_td').addClass('is-invalid');
        } else {
            if(doc_type == 1 && dni.length == 8){
                consultaDatosSUNAT(dni);
                $('#id_td').removeClass('is-invalid');
            }
        }
    });
    //boton extranjero
    $('.extra').click(function(e){
        e.preventDefault();
        $('.extra').addClass('d-none');
        $('.nacional').addClass('d-none');
        $('.pe').removeClass('d-none');
        $('.foreign').removeClass('d-none');
    });
    //paciente nacional
    $('.pe').click(function(){
        $('.pe').addClass('d-none');
        $('.foreign').addClass('d-none');
        $('.nacional').removeClass('d-none');
        $('.extra').removeClass('d-none');
    });
    //buscar ubigeo nacimiento
    $('.buscarUbigeo').select2({
        placeholder: "Buscar ubigeo",
        minimumInputLength: 3,
        ajax: {
            type: 'POST',
            url: `${API_BASE_URL}/histories/location`,
            dataType: 'json', 
            delay: 300,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data,
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: function(data) {
            if (data.loading) {
                return data.text;
            }
            var markup = "<option value='" + data.id + "'>" + data.ubigeo + "</option>";
            return markup;
        },
        templateSelection: function(data) {
            return data.ubigeo || data.id;
        },
    });
    //buscar ubigeo residencia
    $('.buscarUbigeoR').select2({
        placeholder: "Buscar ubigeo residencia",
        minimumInputLength: 3,
        ajax: {
            type: 'POST',
            url: `${API_BASE_URL}/histories/location`,
            dataType: 'json', 
            delay: 300,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data,
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: function(data) {
            if (data.loading) {
                return data.text;
            }
            var markup = "<option value='" + data.id + "'>" + data.ubigeo + "</option>";
            return markup;
        },
        templateSelection: function(data) {
            return data.ubigeo || data.id;
        },
    });
    // buscar ocupacion
    $('.buscarOcupacion').select2({
        placeholder: "Buscar ocupacion",
        minimumInputLength: 3,
        ajax: {
            type: 'POST',
            url: `${API_BASE_URL}/histories/occupation`,
            dataType: 'json', 
            delay: 300,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data,
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: function(data) {
            if (data.loading) {
                return data.text;
            }
            var markup = "<option value='" + data.id + "'>" + data.ocupacion + "</option>";
            return markup;
        },
        templateSelection: function(data) {
            return data.ocupacion || data.id;
        },
    });
    //calcular índice de tabaquismo
    $('#cig, #af').on('input', function() {
        let cig = parseFloat($('#cig').val());
        let af = parseFloat($('#af').val());
        if (!isNaN(cig) && !isNaN(af) && af > 0) {
            let resultado = (parseFloat(cig)*parseFloat(af))/20;
            $('#r').val(resultado.toFixed(2));
        }
    });
    //tabla index historiales
    $('#histories').jtable({
        title       : "HISTORIAS CLÍNICAS",
        paging      : true,
        overflow    : scroll,
        sorting     : true,
        actions: {
            listAction: `${API_BASE_URL}/histories/list`,
        },
        toolbar: {
            items: [{
                cssClass: 'buscador',
                text: buscador
            }]
        },
        fields: {
            fecha: {
                key: false,
                title: 'FECHA R.',
                width: '8%' ,
            },
            dni: {
                key: true,
                title: 'DNI',
                width: '6%' ,
            },
            nombres: {
                title: 'PACIENTE',
                width: '23%' ,
            },
            fecha_nacimiento: {
                title: 'F.N',
                width: '10%',
            },
            edad: {
                title: 'EDAD',
                width: '5%',
            },
            sexo: {
                title: 'SEXO',
                width: '6%' ,
            },
            ver: {
                title: 'Opciones',
                width: '14%',
                sorting: false,
                edit: false,
                create: false,
                display: (data) => {
                    const permissions = data.record.Permissions || {}; // Obtenemos los permisos del registro
                    let buttons = '';
                    buttons += `
                        <button type="button" class="btn btn-info add-quote btn-xs" value="${data.record.id}">
                            <i class="bi bi-file-earmark-plus"></i> Añadir
                        </button>&nbsp;
                    `;
                    if (permissions.update) {
                        buttons += `
                            <button type="button" class="btn btn-warning edit-row btn-xs" value="${data.record.id}">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>&nbsp;
                        `;
                    }
                    if (permissions.delete) {
                        buttons += `
                            <button type="button" class="btn btn-danger delete-row btn-xs" value="${data.record.id}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        `;
                    }
                    return buttons;
                }
            }
        },
        recordsLoaded: (event, data) => {
            $('.add-quote').click(async function(e){
                e.preventDefault();
                const id = $(this).attr('value');
                try {
                    const result = await swal.fire({
                        title: '¿Estás seguro de añadir este paciente a la cola de citas?',
                        text: 'Desea añadir este paciente a la agenda de hoy',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, añadir',
                        cancelButtonText: 'Cancelar'
                    });
                    if (result.isConfirmed) {
                        const response = await axios.get(`${API_BASE_URL}/histories/quotes/${id}`);
                        if(response.status == 200 & response.data.status == true){
                            Swal.fire('Operación exitosa', response.data.messages, response.data.type);
                        }else{
                            Swal.fire('Operación fallida', response.data.messages, response.data.type);
                        }
                    }
                } catch (error) {
                    console.error(error);
                }
            });
            
            $('.edit-row').click(function(e){
                e.preventDefault();
                let id = $(this).attr('value');
                window.location.href = `${API_BASE_URL}/histories/edit/${id}`;
            });
            
            $('.delete-row').click(async function(e) {
                e.preventDefault();
                const id = $(this).attr('value');
                try {
                    const result = await swal.fire({
                        title: '¿Estás seguro de hacerlo?',
                        text: 'Si borras la historia clínica todos los datos de este paciente serán borrados',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, borrarlo',
                        cancelButtonText: 'Cancelar',
                    });
                    if (result.isConfirmed) {
                        const response = await axios.delete(`${API_BASE_URL}/histories/${id}`);    
                        if(response.status == 200 && response.data.status == true){
                            Swal.fire({
                                title: 'Cargando...',
                                html: "Borrando historia clínica y demás registros... <b></b> milisegundos.",
                                timer: 8000,  // 8000 ms = 8 segundos
                                timerProgressBar: true,  // Muestra la barra de progreso
                                didOpen: () => {
                                    Swal.showLoading();
                                    const timer = Swal.getPopup().querySelector("b");
                                        timerInterval = setInterval(() => {
                                        timer.textContent = `${Swal.getTimerLeft()}`;
                                    }, 100);
                                },
                                willClose: () => {
                                    clearInterval(timerInterval);
                                    console.log('Alerta cerrada automáticamente después de 8 segundos.');
                                }
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    //console.log(response);
                                    Swal.fire('Operación exitosa', response.data.messages, response.data.type)
                                    LoadRecordsButton.click();
                                }
                            });
                        }else{
                            Swal.fire('Operación fallida', response.data.messages, response.data.type)
                        }
                    }
                } catch (error) {
                    console.error(error);   
                }
            });
        }
    });
    
    LoadRecordsButton = $('#LoadRecordsButton');
    LoadRecordsButton.click(function (e) {
        e.preventDefault();
        console.log($('#search').val())
        $('#histories').jtable('load', {
            search: $('#search').val()
        });
    });
    LoadRecordsButton.click();
    
    /**
     * Función para validar guardar y/o actualizar los datos de una historia clínica
     */
    $('#formHC').submit(async function(e){
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-group').removeClass('is-invalid is-valid');
        const formData = new FormData(this);

        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html(); // Guardar el texto original del botón
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');

        try {
            const response = await axios.post(`${API_BASE_URL}/histories/store`, formData);
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
});

//Funcion calular edad
function getAge(dateString){
    var today       = new Date(); 
    var birthDate   = new Date(dateString);
    var age         = today.getFullYear() - birthDate.getFullYear();
    var m           = today.getMonth() - birthDate.getMonth();
    if(m < 0 || (m === 0 && today.getDate() < birthDate.getDate())){age--;}
    $("#age").val(age);
}
//ingresar solo numeros
function solo_numeros(e){
	let keynum = window.event ? window.event.keyCode : e.which;
	if((keynum == 8)){return true;}
	return /\d/.test(String.fromCharCode(keynum));
}
//funcion encontrar nombres por DNI
async function consultaDatosSUNAT(dni){
    let data = {
        dni: dni
    };
    const formData = new FormData();
    formData.append('dni', data.dni);
    try {
        const response = await axios.post(`${API_BASE_URL}/histories/dni`, formData);
        console.log(response);
        if(response.status == 200){
            $('#dni').closest('.col-md-2').removeClass('is-invalid');
            $('#dni').closest('.col-md-2').addClass('is-valid');
            $('#nombres').closest('.col-6').addClass('is-valid');
            $('#dni').val(response.data.document_number);
            $('#nombres').val(response.data.first_name + ' ' + response.data.first_last_name + ' ' + response.data.second_last_name);
        }else{
            $("#dni").val(dni);
            //$("#nombres").val('');
        }
    } catch (error) {
        console.error(error);
    }
}