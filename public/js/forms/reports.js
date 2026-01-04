$(document).ready(function(){
    const hc        = $('#id_historia').val();
    const reportId  = $('#reportId').val();

    const tables = {
       reportDNI: $("#report_data").DataTable({ ajax: `${API_BASE_URL}/reports/list/${hc}`, order: [], processing: true }),
       diagnosticId: $("#diagnostic_data").DataTable({ ajax: `${API_BASE_URL}/reports/listDiagnostic/${reportId}`, order: [], searching: false, bLengthChange: false, processing: true }),
       reportTableByDNI: $("#reportsByDNI").DataTable({ ajax: `${API_BASE_URL}/reports/listReports/${hc}`, order: [], processing: true }),
    };
    //Eliminar un registro
    DeleteHandler.initButtons([
        {
            selector: '.delete-report',
            endpoint: 'reports',
            table: tables.reportDNI
        },
        {
            selector: '.delete-diagnostic',
            endpoint: 'rp-dx',
            table: tables.diagnosticId
        }
    ]);
    //tabla index reports
    $('#histories').jtable({
        title       : "INFORMES CLÍNICOS",
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
                key: false,
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
                    if (permissions.view_rpt) {
                        buttons += `
                            <button type="button" class="btn btn-info view-row btn-xs" value="${data.record.id}">
                                <i class="bi bi-folder"></i> Ver
                            </button>&nbsp;
                        `;
                    }
                    if (permissions.add_rpt) {
                        buttons += `
                            <button type="button" class="btn btn-success add-new btn-xs" value="${data.record.id}">
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
                const hc = $(this).attr('value');
                window.location.href = `${API_BASE_URL}/reports/add/${hc}`;
            });

            $('.view-row').click(function(e) {
                e.preventDefault();
                const hc = $(this).attr('value');
                window.location.href = `${API_BASE_URL}/reports/see/${hc}`;
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
    $('#reportForm').submit(async function(e){
        e.preventDefault();
        $('.text-danger').remove();
        $('.form-group').removeClass('is-invalid is-valid');
        const formData = new FormData(this);
        try {
            const response = await axios.post(`${API_BASE_URL}/reports/store`, formData);
            if(response.status == 200 && response.data.status == true){
                $('#examForm').trigger('reset');
                $('.form-group').removeClass('is-invalid is-valid');
                $('#reportForm').find('.text-danger').remove();
                const result = await Swal.fire({
                    title: 'Guardando información',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    timer: 4000,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                        const timer = Swal.getHtmlContainer().querySelector('b');
                        timerInterval = setInterval(() => {
                            timer.textContent = Swal.getTimerLeft();
                        }, 100);
                    },
                    willClose: () => {
                        clearInterval(timerInterval);
                    }
                });
                if (result.dismiss === Swal.DismissReason.timer) {
                    await Swal.fire({
                        icon: response.data.type || 'success',
                        title: response.data.messages || 'Información guardada correctamente',
                        html: response.data.route_print ?
                            `<a class="btn btn-info" href="${response.data.route_print}" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> Imprimir prescripción
                            </a>` : '',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar',
                    });
                    // Redirigir si hay una ruta definida
                    if (response.data.route) {
                        window.location.href = response.data.route;
                    }
                }
            }else if(response.data.status == false){
                await Swal.fire({
                    icon: response.data.type || 'error',
                    title: response.data.messages || 'Error al guardar la información',
                    showConfirmButton: false,
                    showCancelButton: false,
                    timer: 2000
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
    //Función para ver el detalle del diagnóstico y la receta de un examen
    $(document).on('click', '.view-report', async function(e){
        e.preventDefault();
        $('.modal-body').empty();
        $('.modal-footer').empty();
        $('.modal-title').empty();
        const id = $(this).attr('value');
        try {
            const response = await axios.get(`${API_BASE_URL}/reports/viewDetail/${id}`);
            if(response.status == 200 && response.data.hc && response.data.rp && response.data.dx) {
                const { rp, hc, dx } = response.data;
                const fecha = new Date(rp.created_at);
                const fechaFormateada = fecha.toLocaleString('es-ES', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                $('.modal-title').text(`Detalles del Examen ${rp.dni}-${rp.id}`);
                html = `
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th width="70%">DNI: ${hc.dni}</th>
                                        <th>Fecha: ${fechaFormateada}</th>
                                    </tr>
                                </thead>
                            </table>
                            <p class="text-uppercase"><strong>Nombres y Apellidos:</strong> ${hc.nombres}</p>
                            <hr>
                            <p>
                                <strong>Antecedentes:</strong><br>
                                ${rp.antecedentes}
                            </p>
                            <p>
                                <strong>Historial enfermedad:</strong><br>
                                ${rp.historial_enfermedad}
                            </p>
                            <p>
                                <strong>Examen físico:</strong><br>
                                ${rp.examen_fisico}
                            </p>
                            <p>
                                <strong>Examen complementario:</strong><br>
                                ${rp.examen_complementario}
                            </p>
                            <p><strong>Diagnóstico:</strong></p>
                            <div class="col-12" style="float: none; margin: 0 auto;">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Diagnóstico</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${dx.map((value, i) => `
                                            <tr>
                                                <td>${i + 1}</td>
                                                <td>${value.diagnostic}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                            <p>
                                <strong>Tratamiento:</strong><br>
                                ${rp.tratamiento}
                            </p>
                            <p>
                                <strong>Sugerencia:</strong><br>
                                ${rp.sugerencia}
                            </p>
                        </div>
                    </div>
                `;
                button = `
                    <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cerrar</button>
                    <a class="btn btn-primary pull-right" href="${API_BASE_URL}/reports/print/${rp.id}" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> Imprimir
                    </a>
                `;
                $('.modal-body').append(html);
                $('.modal-footer').append(button);
                $('#modal-default').modal('show');
            }else {
                throw new Error('Datos de la respuesta no válidos');
            }
        } catch (error) {
            console.error('Error al cargar los detalles del informe:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los detalles del informe. Por favor, inténtelo de nuevo.'
            });
        }
    });
});
