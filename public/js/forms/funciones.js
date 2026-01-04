$(document).ready(function(){

    $('.exit-system').on('click', async function(e){
        e.preventDefault();
        try {
            const result = await swal.fire({
                title: '¿Quieres salir del sistema?',
                text: '¿Estás seguro que quieres cerrar la sesión?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#16a085",
                confirmButtonText: "Si, salir",
                cancelButtonText: "No, cancelar",
                closeOnConfirm: false,
                animation: "slide-from-top"
            });
            if (result.isConfirmed) {
                const response = await axios.post(`${API_URL}/logout`);
                if(response.status == 200 && response.data.status == true){
                    window.location.href = response.data.redirect;
                }
            }
        } catch (error) {
            console.error('Error al cerrar la sesión:', error);
        }
    });

    // Inicialización de Select2 para la búsqueda de diagnósticos
    $('.searchDiagnostics').select2({
        placeholder: "Buscar diagnóstico por código o descripción",
        minimumInputLength: 3,
        ajax: {
            type: 'POST',
            url: `${API_BASE_URL}/diagnostics/search`,
            dataType: 'json',
            delay: 250,
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
            var markup = "<option value='" + data.id + "'>" + data.text + "</option>";
            return markup;
        },
        templateSelection: function(data) {
            return data.text || data.id;
        },
    });

    // Funciones para agregar diagnóstico al seleccionar en Select2
    $('#diagnostics').on('select2:select', async function (e) {
        // Obtenemos el objeto completo del diagnóstico seleccionado
        const selectedData = e.params.data;

        // Si el elemento seleccionado no tiene ID, es un placeholder o algo incompleto, salimos.
        if (!selectedData.id) return;

        // Extraer datos del diagnóstico seleccionado
        const id    = selectedData.id;
        const name  = selectedData.text; // La etiqueta completa (código - descripción)

        // Obtener solo la descripción para la tabla
        const formattedName = name?.includes(' - ') ? name.split(' - ')[1] : name;
        // --- 1. Validar Duplicado en la Lista Local (El chequeo en tu tabla HTML) ---
        // Tu validador usa un input hidden con el ID como valor.
        if ($(`input[name="diagnostic_id[]"][value="${id}"]`).length) {
            Swal.fire('¡Duplicado!', 'El diagnóstico ya está en la lista local.', 'warning');
            // Limpiar el select2 después del aviso
            $('#diagnostics').val(null).trigger('change');
            return;
        }

        // --- 3. Insertar en la Tabla ---
        $('#tableDiagnostics tbody').append(`
            <tr>
                <td><input type="hidden" name="diagnostic_id[]" value="${id}">${formattedName}</td>
                <td><button type="button" class="btn btn-danger btn-xs btn-remove-diagnosis" value="${id}">
                    <i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `);

        // Limpiar el select2 después de la inserción exitosa
        $('#diagnostics').val(null).trigger('change');
        alertNotify('success', `<h5><b>${formattedName}</b> agregado</h5>`);
    });

    //Función para quitar las filas de la table de diagnósticos
    $(document).on('click','.btn-remove-diagnosis', function(){
        $(this).closest('tr').remove();
    });
    //Funciones para agregar diagnostico
    // Funciones para agregar diagnostico
    $('.searchDrugs').select2({
        placeholder: "Buscar diagnóstico por código o descripción",
        minimumInputLength: 3,
        ajax: {
            type: 'POST',
            url: `${API_BASE_URL}/drugs/search`,
            dataType: 'json',
            delay: 250,
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
            var markup = "<option value='" + data.id + "'>" + data.text + "</option>";
            return markup;
        },
        templateSelection: function(data) {
            return data.text || data.id;
        },
    });

    $('#drugs').on('select2:select', async function (e) {
        // Obtenemos el objeto completo del diagnóstico seleccionado
        const selectedData = e.params.data;

        // Si el elemento seleccionado no tiene ID, es un placeholder o algo incompleto, salimos.
        if (!selectedData.id) return;

        // Extraer datos del diagnóstico seleccionado
        const id = selectedData.id;
        const name = selectedData.text; // La etiqueta completa (código - descripción)

        // Obtener solo la descripción para la tabla
        const formattedName = name?.includes(' - ') ? name.split(' - ')[1] : name;

        if ($(`input[name="drug_id[]"][value="${id}"]`).length) {
            Swal.fire('¡Duplicado!', 'El fármaco ya está en la lista local.', 'warning');
            // Limpiar el select2 después del aviso
            $('#drugs').val(null).trigger('change');
            return;
        }

        $('#tableDrugs tbody').append(`
            <tr>
                <td><input type="hidden" name="drug_id[]" value="${id}">${name}</td>
                <td><input type="text" class="form-control" name="description[]" placeholder="Ingrese descripción"></td>
                <td><button type="button" class="btn btn-danger btn-xs btn-remove-drug" value="${id}"><i class="bi bi-trash"></i></button></td>
            </tr>
        `);

        // Limpiar el select2 después de la inserción exitosa
        $('#drugs').val(null).trigger('change');
        alertNotify('success', `<h5><b>${formattedName}</b> agregado</h5>`);
    });
    //Función para quitar las filas de la table de recetas
    $(document).on('click','.btn-remove-drug', function(){
        $(this).closest('tr').remove();
    });
});

function alertNotify(icon, messages){
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    Toast.fire({
        icon: icon,
        //title: 'Operación realizada',
        //html: messages,
        title: messages,
    })
}
