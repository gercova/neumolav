$(document).ready(function(){
    const dni       = $('#dni').val();
    const examId    = $('#examId').val();
    const tables    = {
        examDNI: $('#exam_data').DataTable({ ajax: `${API_BASE_URL}/exams/list/${dni}`, processing: true }),
        //Listado de diagnosticos por examen
        diagnosticId: $('#diagnostic_data').DataTable({ ajax: `${API_BASE_URL}/exams/listDiagnostic/${examId}`, searching: false, bLengthChange: false, processing: true }),
        //Listado de examenes radiologicos por examen
        imgId: $('#image_data').DataTable({ ajax: `${API_BASE_URL}/exams/listImg/${examId}`, searching: false, bLengthChange: false, processing: true }),
        //Receta por examen
        examId: $('#medication_data').DataTable({ ajax: `${API_BASE_URL}/exams/listMedication/${examId}`, searching: false, bLengthChange: false, processing: true }),
        //Tabla de pdf
        documentId: $('#document_data').DataTable({ ajax: `${API_BASE_URL}/listDocument/${examId}`, searching: false, bLengthChange: false, processing: true }),
    };
    //Eliminar un registro
    DeleteHandler.initButtons([
        {
            selector: '.delete-exam',
            endpoint: 'exams',
            table: tables.examDNI
        },
        {
            selector: '.delete-diagnostic',
            endpoint: 'ex-dx',
            table: tables.diagnosticId
        },
        {
            selector: '.delete-drug',
            endpoint: 'ex-mx',
            table: tables.examId
        },
        {
            selector: '.delete-image',
            endpoint: 'ex-img',
            table: tables.imgId
        }
    ]);
    //tabla index examenes
    $('#histories').jtable({
        title       : "EXÁMENES CLÍNICOS",
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
                    if (permissions.view_exm) {
                        buttons += `<button type="button" class="btn btn-info view-row btn-xs" value="${data.record.id}">
                            <i class="bi bi-folder"></i> Ver
                        </button>&nbsp;`;
                    }
                    if (permissions.add_exm) {
                        buttons += `<button type="button" class="btn btn-success add-new btn-xs" value="${data.record.id}">
                            <i class="bi bi-plus-square-fill"></i> Nuevo
                        </button>&nbsp;`;
                    }

                    return buttons;
                }
            },
        },
        recordsLoaded: (event, data) => {
            $('.add-new').click(function(e){
                e.preventDefault();
                const hc = $(this).attr('value');
                window.location.href = `${API_BASE_URL}/exams/add/${hc}`;
            });

            $('.view-row').click(function(e) {
                e.preventDefault();
                const hc = $(this).attr('value');
                window.location.href = `${API_BASE_URL}/exams/see/${hc}`;
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
    //calcular imc
    $('#peso, #talla').on('input', function() {
        let peso = parseFloat($('#peso').val());
        let altura = parseFloat($('#talla').val());
        if (!isNaN(peso) && !isNaN(altura) && altura > 0) {
            let imc = peso / (altura * altura);
            $('#imc').val(imc.toFixed(2));
        }
    });
    //Funcion para validar datos antes de ser enviados al controlador para guardar o actualizar un examen
    $('#examForm').submit(async function (e) {
        e.preventDefault();
        // Limpiar mensajes de error y clases de validación previas
        $('.text-danger').remove();
        $('.form-control').removeClass('is-invalid is-valid');
        const formData = new FormData(this);
        try {
            const response = await axios.post(`${API_BASE_URL}/exams/store`, formData);
            // Validar que la respuesta sea exitosa y tenga los datos esperados
            if (response.status === 200 && response.data && response.data.status === true) {
                console.log(response.data);
                // Limpiar el formulario y mensajes de error
                $('#examForm').trigger('reset');
                $('#examForm').find('.text-danger').remove();
                $('.form-control').removeClass('is-invalid is-valid');
                // Mostrar un mensaje de carga mientras se suben los archivos
                const result = await Swal.fire({
                    title: 'Subiendo archivos y guardando información',
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
                    willClose: () => { clearInterval(timerInterval); }
                });
                // Si el temporizador termina, mostrar un mensaje de éxito
                if (result.dismiss === Swal.DismissReason.timer) {
                    await Swal.fire({
                        icon: response.data.type || 'success', // Usar 'success' como valor predeterminado
                        title: response.data.message || 'Información guardada correctamente',
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
                    showCancelButton: false,
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
        }
    });
    //Funcion para ver una imagen y sus detalles
    $(document).on('click', '.view-img-exam', async function(e){
        e.preventDefault();
        $('.modal-body').empty();
        $('.modal-footer').empty();
        try {
            const id = $(this).attr('value');
            const response = await axios.get(`${API_BASE_URL}/exams/viewImg/${id}`);
            if (response.status == 200) {
                $('.modal-title').text('Info del examen radiológico');
                const html = `
                    <div class="row">
                        <div class="col-12">
                            <img src="${API_BASE_URL}/storage/${response.data.imagen}" class="img-thumbnail mx-auto d-block" width="550">
                            <hr>
                            <p class="text-center"><strong>Fecha del examen:</strong> ${response.data.fecha_examen}</p>
                        </div>
                    </div>
                `;
                button = `<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cerrar</button>`;
                $('.modal-body').append(html);
                $('.modal-footer').append(button);
                $('#modal-default').modal('show');
            } else {
                swal.fire({
                    icon: response.data.type,
                    title: response.data.messages,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } catch (error) {
            console.error(error);
        }
    });
    //añadir campo imagen / fecha / botón remover
    $('#btn-file').on('click', function(){
        html = '<tr>';
        html += '<td><input type="file" accept="image/*" name="img[]" required></td>';
        html += '<td><input type="date" class="form-control" name="dateImagen[]" required></td>';
        html += '<td><button type="button" class="btn btn-danger btn-remove-info btn-xs"><span class="bi bi-x-lg"></span></button></td>';
        html += '</tr>';
        $('#tbfiles tbody').append(html);
        $('#img').val(null);
        $('#fecha').val(null);
    });
    //remover modulos filas de la tabla
    $(document).on('click','.btn-remove-info', function(){
        $(this).closest('tr').remove();
    });
    //añadir campo para examen radiológico
    $('#btn-add-img').on('click', function(){
        html = `
            <tr>
                <td><input type="file" accept="image/*" name="img[]" class="form-control" required></td>
                <td><input type="date" class="form-control date" name="dateImg[]" required></td>
                <td><button type="button" class="btn btn-danger remove-row-img"><i class="bi bi-x-lg"></i></button></td>';
            </tr>
        `;
        $('#tableImg tbody').append(html);
    });
    //remover modulos filas de la tabla de exámenes radiológicos
    $(document).on('click','.remove-row-img', function(){
        $(this).closest('tr').remove();
    });
    //añadir campo para examen
    $('#btn-add-test').on('click', function(){
        html = `
            <tr>
                <td><input type="text" class="form-control" name="exm[]" placeholder="Nombre del examen" required></td>
                <td><input type="date" class="form-control date" name="fecha[]" required></td>
                <td><input type="file" name="filePdf[]" accept="application/pdf" required class="form-control"></td>
                <td><button type="button" class="btn btn-danger remove-row-test"><i class="bi bi-x-lg"></i></button></td>
            </tr>
        `;
        $('#tabletest tbody').append(html);
    });
    //remover modulos filas de la tabla
    $(document).on('click','.remove-row-test', function(){
        $(this).closest('tr').remove();
    });
});
