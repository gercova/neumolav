$(document).ready(function(){
    const dni               = $('#dni').val();
    const appointmentId     = $('#appointmentId').val();
    const tables = {
        appointmentDNI: $('#appointment_data').DataTable({ajax: `${API_BASE_URL}/appointments/list/${dni}`, order: [], processing: true }),
        appointmentTableByDNI: $('#appointmentsByDNI').DataTable({ajax: `${API_BASE_URL}/appointments/listAppointments/${dni}`, order: [], processing: true }),
        diagnosticId: $('#diagnostic_data').DataTable({ajax: `${API_BASE_URL}/appointments/listDiagnostic/${appointmentId}`, order: [[0, 'asc']], searching: false, bLengthChange: false, processing: true }),
        medicationId: $('#medication_data').DataTable({ajax: `${API_BASE_URL}/appointments/listMedication/${appointmentId}`, order: [[0, 'asc']], searching: false, bLengthChange: false, processing: true }),
    };
    //Eliminar un registro
    DeleteHandler.initButtons([
        {
            selector: '.delete-appointment',
            endpoint: 'appointments',
            table: tables.appointmentDNI
        },{
            selector: '.delete-diagnostic',
            endpoint: 'ap-dx',
            table: tables.diagnosticId
        },{
            selector: '.delete-medication',
            endpoint: 'ap-mx',
            table: tables.medicationId
        }
    ]);
    //tabla index examenes
    $('#histories').jtable({
        title       : "CITAS Y CONTROLES CLÍNICOS",
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

                    if (permissions.view_ctrl) {
                        buttons += `
                            <button type="button" class="btn btn-info view-row btn-xs" value="${data.record.dni}">
                                <i class="bi bi-folder"></i> Ver
                            </button>&nbsp;
                        `;
                    }
                    if (permissions.add_ctrl) {
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
                window.location.href = `${API_BASE_URL}/appointments/add/${id}`;
            });
            
            $('.view-row').click(function(e) {
                e.preventDefault();
                const id = $(this).attr('value');
                window.location.href = `${API_BASE_URL}/appointments/see/${id}`;
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
    //function view table
    $(document).on('change', '#search-table', function() {
        const table = $(this).val();
        if (table && dni) {
            fetchTableData(table, dni);
        } else {
            clearTableData();
        }
    });
    // Función para cargar datos iniciales si es necesario
    loadInitialData();
    //Funcion para validar datos antes de ser enviados al controlador para guardar o actualizar un registro de control clínico
    $('#appointmentForm').submit(async function (e) {
        e.preventDefault();
        // Limpiar mensajes de error y clases de validación previas
        $('.form-control').removeClass('is-invalid is-valid');
        $('#appointmentForm').find('.text-danger').remove();
        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonText = submitButton.html();
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        const formData = new FormData(this);
        try {
            const response = await axios.post(`${API_BASE_URL}/appointments/store`, formData);
            // Validar que la respuesta sea exitosa y tenga los datos esperados
            if (response.status === 200 && response.data && response.data.status === true) {
                // Limpiar el formulario y mensajes de error
                $('#appointmentForm').trigger('reset');
                $('.form-control').removeClass('is-invalid is-valid');
                $('#appointmentForm').find('.text-danger').remove();
                // Mostrar un mensaje de carga mientras se guarda la información
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
                // Si el temporizador termina, mostrar un mensaje de éxito
                if (result.dismiss === Swal.DismissReason.timer) {
                    await Swal.fire({
                        icon: response.data.type || 'success', // Usar 'success' como valor predeterminado
                        title: response.data.messages || 'Información guardada correctamente',
                        html: `<div class="text-center">
                            <p class="mb-3">Selecciona el formato de impresión:</p>
                            <div class="d-flex justify-content-center gap-3">
                                <a class="btn btn-outline-info d-flex flex-column align-items-center p-3" href="${response.data.print_a4}" target="_blank">
                                    <i class="fas fa-file-pdf fa-2x mb-2"></i>
                                    <span>Formato A4</span>
                                    <small class="badge badge-light mt-1">Carta</small>
                                </a>
                                &nbsp;
                                <a class="btn btn-outline-success d-flex flex-column align-items-center p-3" href="${response.data.print_a5}" target="_blank">
                                    <i class="fas fa-file-pdf fa-2x mb-2"></i>
                                    <span>Formato A5</span>
                                    <small class="badge badge-light mt-1">Medio</small>
                                </a>
                            </div>
                        </div>`,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar',
                    });
                    // Redirigir si hay una ruta definida
                    if (response.data.route) {
                        window.location.href = response.data.route;
                    }
                }
            } else if (response.data && response.data.status === false) {
                // Mostrar un mensaje de error si el servidor indica un fallo
                await Swal.fire({
                    icon: response.data.type || 'error',
                    title: response.data.messages || 'Error al guardar la información',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        } catch (error) {
            console.error('Error al enviar el formulario:', error);
            // Mostrar errores de validación del servidor
            if (error.response && error.response.data && error.response.data.errors) {
                $.each(error.response.data.errors, function (key, value) {
                    const inputElement = $(document).find(`[name="${key}"]`);
                    inputElement.after(`<span class="text-danger">${value[0]}</span>`).closest('.form-control').addClass('is-invalid').focus();
                });
            } else {
                // Mostrar un mensaje de error genérico si no hay errores de validación
                await Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al enviar el formulario. Por favor, inténtelo de nuevo.'
                });
            }
        } finally {
            // Restaurar el botón de envío a su estado original
            submitButton.prop('disabled', false).html(originalButtonText);
        }
    });
});
//view table
function fetchTableData(table, dni) {
    axios.post('/appointments/view-table', {
        table: table,
        dni: dni
    }).then(response => {
        document.getElementById('table_data').innerHTML = response.data.html;
    }).catch(error => {
        console.error('Error:', error);
        document.getElementById('table_data').innerHTML = `
            <div class="callout callout-danger">
                Error al cargar los datos
            </div>
        `;
    });
}

function clearTableData() {
    document.getElementById('table_data').innerHTML = '';
}

function loadInitialData() {
    // Implementa si necesitas cargar datos iniciales
}