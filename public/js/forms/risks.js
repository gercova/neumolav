$(document).ready(function(){
    const dni   = $('#dni').val();
    const tables = {
       riskId:  $("#risk_data").DataTable({ ajax: `${API_BASE_URL}/risks/list/${dni}`, order: [], processing: true }),
       riskTableByDNI: $("#risksByDNI").DataTable({ ajax: `${API_BASE_URL}/risks/listRisks/${dni}`, order: [], processing: true }),
    };
    //Eliminar un registro
    DeleteHandler.initButtons([
        {
            selector: '.delete-risk',
            endpoint: 'risks',
            table: tables.riskId
        },
    ]);
    //tabla index reports
    $('#histories').jtable({
        title       : "INFORMES DE RIESGOS CLÍNICOS",
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
                title: 'FECHA',
                width: '6%' ,
            },
            dni: {
                key: true,
                title: 'DNI',
                width: '6%' ,
            },
            nombres: {
                title: 'NOMBRES',
                width: '20%',

            },
            fecha_nacimiento: {
                title: 'F.N',
                width: '6%',
            },
            edad: {
                title: 'EDAD',
                width: '4%',
            },
            sexo: {
                title: 'SEXO',
                width: '4%' ,
            },
            ver:{
                title: 'OPCIONES',
                width: '10%',
                sorting:false,
                edit:false,
                create:false,
                display: (data) => {
                    const permissions = data.record.Permissions || {}; // Obtenemos los permisos del registro
                    let buttons = '';

                    if (permissions.view_rsk) {
                        buttons += `
                            <button type="button" class="btn btn-info view-row btn-xs" value="${data.record.dni}">
                                <i class="bi bi-folder"></i> Ver
                            </button>&nbsp;
                        `;
                    }
                    if (permissions.add_rsk) {
                        buttons += `
                            <button type="button" class="btn btn-success add-new btn-xs" value="${data.record.dni}">
                                <i class="bi bi-plus-square-fill"></i> Nuevo
                            </button>
                        `;
                    }
                    
                    return buttons;
                }
            },
        },
        recordsLoaded: (event, data) => {
            $('.add-new').click(function(e){
                e.preventDefault();
                const id = $(this).attr('value');
                window.location.href = `${API_BASE_URL}/risks/add/${id}`;
            });
            
            $('.view-row').click(function(e) {
                e.preventDefault();
                const id = $(this).attr('value');
                window.location.href = `${API_BASE_URL}/risks/see/${id}`;
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
    //Funcion para validar datos antes de ser enviados al controlador para guardar o actualizar un examen
    $('#riskForm').submit(async function(e){
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-group').removeClass('is-invalid is-valid');
        const formData = new FormData(this);
        try {
            const response = await axios.post(`${API_BASE_URL}/risks/store`, formData);
            if(response.status == 200 && response.data.status == true){
                $('#riskForm').trigger('reset');
                $('.text-danger').remove();
                $('.form-group').removeClass('is-invalid is-valid');
            
                const result = await Swal.fire({
                    title: 'Guardando información',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    timer: 4000,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading()
                        const timer = Swal.getHtmlContainer().querySelector('b')
                        timerInterval = setInterval(() => {
                            timer.textContent = Swal.getTimerLeft()
                        }, 100)
                    },
                    willClose: () => { clearTimeout(timerInterval); }
                });
                if (result.dismiss === Swal.DismissReason.timer) {
                    await Swal.fire({
                        icon: response.data.type || 'success',
                        title: response.data.messages || 'Información guardada correctamente',
                        html: response.data.route_print ? 
                            `<a class="btn btn-info" href="${response.data.route_print}" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> Imprimir informe
                            </a>` : '',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar',
                    });
                    if (response.data.route) {
                        window.location.href = response.data.route;
                    };
                }
            }else if(response.data.status == false){
                swal.fire({
                    icon: response.data.type,
                    title: response.data.messages,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } catch (error) {
            if(error.response && error.response.data.errors){
                $.each(error.response.data.errors, function(key, value) {
                    let inputElement = $(document).find(`[name="${key}"]`);
                    inputElement.after(`<span class="text-danger">${value[0]}</span>`).closest('.form-control').addClass('is-invalid').focus();
                });
            }
        }
    });
    //Función para ver el informe de riesgo por id
    $(document).on('click', '.view-risk', async function(e){
        e.preventDefault();
        $('.modal-body').empty();
        $('.modal-footer').empty();
        $('.modal-title').empty();
        const id = $(this).attr('value');
        try {
            const response = await axios.get(`${API_BASE_URL}/risks/viewDetail/${id}`)
            if(response.status == 200 && response.data.hc && response.data.rk){
                const { hc, rk } = response.data;
                const fecha = new Date(rk.created_at);
                const fechaFormateada = fecha.toLocaleString('es-ES', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                $('.modal-title').text(`Detalles del Examen ${rk.dni}-${rk.id}`);
                html = `
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th width="70%">DNI: ${hc[0].dni}</th><th>Fecha: ${fechaFormateada}</th>
                                    </tr>
                                </thead>
                            </table>
                            <p class="text-uppercase"><strong>Nombres y Apellidos:</strong> ${hc[0].nombres}</p>
                            <div class="card">
                                <div class="card-body">
                                    <strong>Motivo:</strong><p>${rk.motivo}</p>
                                    <strong>Antecedente:</strong><p>${rk.antecedente}</p>
                                    <strong>Síntomas:</strong><p>${rk.sintomas}</p>
                                    <strong>Examen físico:</strong><p>${rk.examen_fisico}</p>
                                    <strong>Examen complementario:</strong><p>${rk.examen_complementario}</p>
                                    <strong>Riesgo neumologico:</strong><p>${rk.riesgo_neumologico}</p>
                                    <strong>Sugerencia:</strong><p>${rk.sugerencia}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                button = `
                    <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cerrar</button>
                    <a class="btn btn-primary pull-right" href="${API_BASE_URL}/risks/print/${rk.id}" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> Imprimir
                    </a>
                `;
                $('.modal-body').empty().append(html);
                $('.modal-footer').empty().append(button);
                $('#modal-default').modal('show');
            }else {
                throw new Error('Datos de la respuesta no válidos');
            }
        } catch (error) {
            console.error('Error al cargar los detalles del examen:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los detalles del examen. Por favor, inténtelo de nuevo.'
            });
        }
    });
});