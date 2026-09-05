/**
 * Histories Form & JTable Logic
 */
$(document).ready(function() {

    // ==========================================
    // 1. DATE PICKER INITIALIZATION & AGE CALC
    // ==========================================
    let vdp = null;
    if ($('#fecha_nacimiento').length && window.VanillaDatePicker) {
        vdp = new VanillaDatePicker('#fecha_nacimiento', {
            maxDate: new Date(),
            minDate: '1900-01-01',
            triggerButton: '#btnDatepickerToggle',
            autoClose: true,
            onSelect: function(date, dateString) {
                // dateString is dd-mm-yyyy from the picker
                syncISOField(dateString);
                getAge(dateString);
                $('#fecha_nacimiento').removeClass('is-invalid');
                $('#fechaNacimientoFeedback').text('').hide();
            }
        });

        // Compute age on page load if date already has a value
        const initialDisplay = $('#fecha_nacimiento').val();
        if (initialDisplay) {
            syncISOField(initialDisplay);
            getAge(initialDisplay);
        } else {
            // The hidden ISO field may already have a value (server-side old/edit)
            const isoVal = $('#fecha_nacimiento_iso').val();
            if (isoVal) {
                // Convert yyyy-mm-dd -> dd-mm-yyyy for display
                const parts = isoVal.split('-');
                if (parts.length === 3) {
                    const display = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    $('#fecha_nacimiento').val(display);
                    getAge(display);
                }
            }
        }
    }

    // ==========================================
    // 1b. INPUT MASK: dd-mm-yyyy (pure Vanilla JS)
    // ==========================================
    $('#fecha_nacimiento').on('keydown', function(e) {
        const allowed = [
            'Backspace', 'Delete', 'ArrowLeft', 'ArrowRight',
            'Tab', 'Home', 'End'
        ];
        // Allow control keys
        if (allowed.includes(e.key)) return;
        // Block non-digit keys
        if (!/^\d$/.test(e.key)) {
            e.preventDefault();
        }
    });

    $('#fecha_nacimiento').on('input', function() {
        let raw = this.value.replace(/\D/g, '').slice(0, 8); // keep only up to 8 digits
        let masked = '';

        if (raw.length > 0) masked += raw.slice(0, 2);
        if (raw.length > 2) masked += '-' + raw.slice(2, 4);
        if (raw.length > 4) masked += '-' + raw.slice(4, 8);

        this.value = masked;

        // When fully entered (dd-mm-yyyy = 10 chars), sync and compute age
        if (masked.length === 10) {
            syncISOField(masked);
            getAge(masked);
            $(this).removeClass('is-invalid');
            $('#fechaNacimientoFeedback').text('').hide();
        } else {
            $('#fecha_nacimiento_iso').val('');
            $('#age').val('');
        }
    });

    $('#fecha_nacimiento').on('paste', function(e) {
        e.preventDefault();
        let pasted = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
        let digits = pasted.replace(/\D/g, '').slice(0, 8);
        let masked = '';
        if (digits.length > 0) masked += digits.slice(0, 2);
        if (digits.length > 2) masked += '-' + digits.slice(2, 4);
        if (digits.length > 4) masked += '-' + digits.slice(4, 8);
        $(this).val(masked).trigger('input');
    });

    // ==========================================
    // 2. DOCUMENT TYPE & DNI BEHAVIOR
    // ==========================================
    function updateDocTypeRules() {
        const docType = $('#id_td').val();
        const dniInput = $('#dni');
        const labelDni = $('#label_dni');
        const helperText = $('#dniHelperText');
        const searchBtn = $('#btnSearchDni');

        dniInput.removeClass('is-invalid is-valid');

        if (docType === '1') {
            // DNI
            labelDni.html('DNI <span class="text-danger">*</span>');
            dniInput.attr('placeholder', '8 dígitos').attr('maxlength', '8');
            helperText.text('Para DNI presione buscar o Enter (8 dígitos).');
            searchBtn.prop('disabled', false).show();
        } else if (docType === '3') {
            // Carnet de extranjería
            labelDni.html('Carnet de Extranjería <span class="text-danger">*</span>');
            dniInput.attr('placeholder', '8 a 12 caracteres').attr('maxlength', '12');
            helperText.text('Ingrese el número de carnet de extranjería.');
            searchBtn.prop('disabled', true).hide();
        } else if (docType === '4') {
            // Pasaporte
            labelDni.html('Pasaporte <span class="text-danger">*</span>');
            dniInput.attr('placeholder', 'Número de pasaporte').attr('maxlength', '15');
            helperText.text('Ingrese el número de pasaporte.');
            searchBtn.prop('disabled', true).hide();
        } else {
            labelDni.html('Documento <span class="text-danger">*</span>');
            dniInput.attr('placeholder', 'Número de documento').attr('maxlength', '15');
            helperText.text('');
            searchBtn.prop('disabled', false).show();
        }
    }

    $('#id_td').on('change', function() {
        updateDocTypeRules();
        $(this).removeClass('is-invalid');
        $(this).closest('.form-group').find('.invalid-feedback').text('').hide();
    });

    // Only allow numbers for DNI or cellphone
    $('#dni').on('input', function(e) {
        const docType = $('#id_td').val();
        if (docType === '1') {
            this.value = this.value.replace(/\D/g, '').slice(0, 8);
            if (this.value.length === 8) {
                consultaDatosSUNAT(this.value);
            }
        }
    });

    $('#telefono').on('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 11);
    });

    $('#dni').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const dniVal = $(this).val().trim();
            if ($('#id_td').val() === '1' && dniVal.length === 8) {
                consultaDatosSUNAT(dniVal);
            }
        }
    });

    $('#btnSearchDni').on('click', function(e) {
        e.preventDefault();
        const docType = $('#id_td').val();
        const dniVal = $('#dni').val().trim();

        if (!docType) {
            Swal.fire({
                icon: 'warning',
                title: 'Tipo de documento',
                text: 'Por favor, seleccione primero el tipo de documento.',
                confirmButtonText: 'Entendido'
            });
            $('#id_td').addClass('is-invalid').focus();
            return;
        }

        if (docType === '1') {
            if (dniVal.length !== 8) {
                showFieldError('#dni', 'El DNI debe tener 8 dígitos');
                return;
            }
            consultaDatosSUNAT(dniVal);
        }
    });

    // ==========================================
    // 3. FOREIGN VS NATIONAL TOGGLE
    // ==========================================
    $('.extra').on('click', function(e) {
        e.preventDefault();
        $('.extra').addClass('d-none');
        $('.nacional').addClass('d-none');
        $('.pe').removeClass('d-none');
        $('.foreign').removeClass('d-none');
        $('#ubigeo_nacimiento').val(null).trigger('change');
        $('#extranjero').focus();
    });

    $('.pe').on('click', function(e) {
        e.preventDefault();
        $('.pe').addClass('d-none');
        $('.foreign').addClass('d-none');
        $('.nacional').removeClass('d-none');
        $('.extra').removeClass('d-none');
        $('#extranjero').val('');
    });

    // ==========================================
    // 4. SELECT2 AJAX SEARCHES
    // ==========================================
    if ($.fn.select2) {
        // Ubigeo Nacimiento
        $('.buscarUbigeo').select2({
            placeholder: "-- Buscar ubigeo (Distrito, Provincia o Dpto) --",
            minimumInputLength: 2,
            language: {
                inputTooShort: () => "Ingrese 2 o más caracteres...",
                noResults: () => "No se encontraron resultados",
                searching: () => "Buscando ubigeo..."
            },
            ajax: {
                type: 'POST',
                url: `${API_BASE_URL}/histories/location`,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term, _token: token };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return { id: item.ubigeo, text: item.ubigeo };
                        })
                    };
                },
                cache: true
            }
        }).on('change', function() {
            $(this).removeClass('is-invalid');
            $(this).closest('.form-group').find('.invalid-feedback').text('').hide();
        });

        // Ubigeo Residencia
        $('.buscarUbigeoR').select2({
            placeholder: "-- Buscar ubigeo de residencia --",
            minimumInputLength: 2,
            language: {
                inputTooShort: () => "Ingrese 2 o más caracteres...",
                noResults: () => "No se encontraron resultados",
                searching: () => "Buscando residencia..."
            },
            ajax: {
                type: 'POST',
                url: `${API_BASE_URL}/histories/location`,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term, _token: token };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return { id: item.ubigeo, text: item.ubigeo };
                        })
                    };
                },
                cache: true
            }
        }).on('change', function() {
            $(this).removeClass('is-invalid');
            $(this).closest('.form-group').find('.invalid-feedback').text('').hide();
        });

        // Ocupación
        $('.buscarOcupacion').select2({
            placeholder: "-- Buscar ocupación --",
            minimumInputLength: 2,
            language: {
                inputTooShort: () => "Ingrese 2 o más caracteres...",
                noResults: () => "No se encontraron resultados",
                searching: () => "Buscando ocupación..."
            },
            ajax: {
                type: 'POST',
                url: `${API_BASE_URL}/histories/occupation`,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term, _token: token };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return { id: item.ocupacion, text: item.ocupacion };
                        })
                    };
                },
                cache: true
            }
        }).on('change', function() {
            $(this).removeClass('is-invalid');
            $(this).closest('.form-group').find('.invalid-feedback').text('').hide();
        });
    }

    // ==========================================
    // 5. TABAQUISMO (IPA) CALCULATION
    // ==========================================
    function calculateIPA() {
        const cig = parseFloat($('#cig').val()) || 0;
        const af = parseFloat($('#af').val()) || 0;
        if (cig >= 0 && af >= 0) {
            const resultado = (cig * af) / 20;
            $('#r').val(resultado.toFixed(2));
        } else {
            $('#r').val('0.00');
        }
    }

    $('#cig, #af').on('input change', calculateIPA);

    // ==========================================
    // 6. REAL-TIME VALIDATION HELPERS
    // ==========================================
    $(document).on('input change', 'input, select, textarea', function() {
        if ($(this).hasClass('is-invalid')) {
            $(this).removeClass('is-invalid');
            $(this).closest('.form-group').find('.invalid-feedback').text('').hide();
        }
    });

    function showFieldError(selector, message) {
        const el = $(selector);
        el.addClass('is-invalid');
        const feedbackEl = el.closest('.form-group').find('.invalid-feedback');
        if (feedbackEl.length) {
            feedbackEl.text(message).show();
        }
    }

    function clearAllErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('').hide();
    }

    function validateClientSide() {
        clearAllErrors();
        let isValid = true;
        let firstInvalid = null;

        function markInvalid(selector, message) {
            isValid = false;
            showFieldError(selector, message);
            if (!firstInvalid) {
                firstInvalid = $(selector);
            }
        }

        // Tipo documento
        const id_td = $('#id_td').val();
        if (!id_td) markInvalid('#id_td', 'Seleccione el tipo de documento.');

        // DNI / Documento
        const dni = $('#dni').val().trim();
        if (!dni) {
            markInvalid('#dni', 'Ingrese el número de documento.');
        } else if (id_td === '1' && dni.length !== 8) {
            markInvalid('#dni', 'El DNI debe tener exactamente 8 dígitos.');
        }

        // Nombres
        const nombres = $('#nombres').val().trim();
        if (!nombres) {
            markInvalid('#nombres', 'Ingrese los nombres y apellidos del paciente.');
        }

        // Fecha nacimiento — validate via the hidden ISO field (yyyy-mm-dd)
        const fn = $('#fecha_nacimiento').val().trim();
        const fnISO = $('#fecha_nacimiento_iso').val().trim();
        if (!fn || fn.length < 10) {
            markInvalid('#fecha_nacimiento', 'Ingrese la fecha de nacimiento completa (DD-MM-AAAA).');
        } else if (!fnISO) {
            markInvalid('#fecha_nacimiento', 'Fecha de nacimiento inválida. Verifique el formato DD-MM-AAAA.');
        } else {
            const birthDate = new Date(fnISO);
            const today = new Date();
            if (isNaN(birthDate.getTime())) {
                markInvalid('#fecha_nacimiento', 'Fecha de nacimiento inválida.');
            } else if (birthDate > today) {
                markInvalid('#fecha_nacimiento', 'La fecha de nacimiento no puede ser una fecha futura.');
            } else if (birthDate < new Date('1900-01-01')) {
                markInvalid('#fecha_nacimiento', 'Ingrese una fecha de nacimiento posterior al 01/01/1900.');
            }
        }

        // Sexo
        if (!$('#id_sexo').val()) markInvalid('#id_sexo', 'Seleccione el sexo.');

        // Celular
        const tel = $('#telefono').val().trim();
        if (!tel) {
            markInvalid('#telefono', 'Ingrese el número de celular o teléfono.');
        } else if (tel.length < 7 || tel.length > 11) {
            markInvalid('#telefono', 'El teléfono debe tener entre 7 y 11 dígitos.');
        }

        // Grupo sanguíneo
        if (!$('#id_gs').val()) markInvalid('#id_gs', 'Seleccione el grupo sanguíneo.');

        // Ubigeo residencia
        if (!$('#ubigeo_residencia').val()) markInvalid('#ubigeo_residencia', 'Seleccione el lugar de residencia.');

        // Grado instrucción
        if (!$('#id_gi').val()) markInvalid('#id_gi', 'Seleccione el grado de instrucción.');

        // Ocupación
        if (!$('#id_ocupacion').val()) markInvalid('#id_ocupacion', 'Seleccione la ocupación.');

        // Estado civil
        if (!$('#id_estado').val()) markInvalid('#id_estado', 'Seleccione el estado civil.');

        if (!isValid && firstInvalid) {
            $('html, body').animate({
                scrollTop: firstInvalid.offset().top - 120
            }, 300);
            firstInvalid.focus();
        }

        return isValid;
    }

    // ==========================================
    // 7. FORM SUBMIT HANDLER (AJAX)
    // ==========================================
    $('#formHC').on('submit', async function(e) {
        e.preventDefault();

        if (!validateClientSide()) {
            return;
        }

        const form = $(this);
        const submitBtn = $('#btnSubmitHC');
        const originalBtnHtml = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...');

        try {
            const formData = form.serialize();
            const response = await axios.post(`${API_BASE_URL}/histories/store`, formData);

            if (response.data && response.data.status) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación Exitosa!',
                    text: response.data.messages || 'Historia clínica guardada correctamente.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#007bff'
                }).then((result) => {
                    if (result.isConfirmed || result.isDismissed) {
                        window.location.href = response.data.route || `${API_BASE_URL}/histories/home`;
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo guardar',
                    text: response.data.messages || 'Ocurrió un error inesperado al guardar.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Entendido'
                });
            }
        } catch (error) {
            console.error('Error al guardar historia:', error);

            if (error.response && error.response.status === 422 && error.response.data.errors) {
                let firstErrorEl = null;
                $.each(error.response.data.errors, function(field, messages) {
                    const fieldEl = $(`[name="${field}"]`);
                    if (fieldEl.length) {
                        fieldEl.addClass('is-invalid');
                        const feedbackEl = fieldEl.closest('.form-group').find('.invalid-feedback');
                        if (feedbackEl.length) {
                            feedbackEl.text(messages[0]).show();
                        }
                        if (!firstErrorEl) firstErrorEl = fieldEl;
                    }
                });

                if (firstErrorEl) {
                    $('html, body').animate({
                        scrollTop: firstErrorEl.offset().top - 120
                    }, 300);
                    firstErrorEl.focus();
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'Por favor complete y corrija los campos marcados en rojo.',
                    confirmButtonColor: '#007bff'
                });
            } else {
                const errorMsg = (error.response && error.response.data && error.response.data.messages)
                    ? error.response.data.messages
                    : 'Error de servidor al guardar la historia clínica.';

                Swal.fire({
                    icon: 'error',
                    title: 'Error del servidor',
                    text: errorMsg,
                    confirmButtonColor: '#d33'
                });
            }
        } finally {
            submitBtn.prop('disabled', false).html(originalBtnHtml);
        }
    });

    // ==========================================
    // 8. JTABLE (FOR INDEX VIEW ONLY)
    // ==========================================
    if ($('#histories').length && $.fn.jtable) {
        $('#histories').jtable({
            title: "HISTORIAS CLÍNICAS",
            paging: true,
            sorting: true,
            actions: {
                listAction: `${API_BASE_URL}/histories/list`,
            },
            toolbar: {
                items: [{
                    cssClass: 'buscador',
                    text: typeof buscador !== 'undefined' ? buscador : ''
                }]
            },
            fields: {
                fecha: { key: false, title: 'FECHA R.', width: '8%' },
                dni: { key: false, title: 'DNI', width: '6%' },
                nombres: { title: 'PACIENTE', width: '23%' },
                fecha_nacimiento: { title: 'F.N', width: '10%' },
                edad: { title: 'EDAD', width: '5%' },
                sexo: { title: 'SEXO', width: '6%' },
                ver: {
                    title: 'Opciones',
                    width: '14%',
                    sorting: false,
                    edit: false,
                    create: false,
                    display: (data) => {
                        const permissions = data.record.Permissions || {};
                        let buttons = '';
                        buttons += `<button type="button" class="btn btn-info add-quote btn-xs" value="${data.record.id}">
                            <i class="bi bi-file-earmark-plus"></i> Añadir
                        </button>&nbsp;`;
                        if (permissions.update) {
                            buttons += `<button type="button" class="btn btn-warning edit-row btn-xs" value="${data.record.id}">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>&nbsp;`;
                        }
                        if (permissions.delete) {
                            buttons += `<button type="button" class="btn btn-danger delete-row btn-xs" value="${data.record.id}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>`;
                        }
                        return buttons;
                    }
                }
            },
            recordsLoaded: (event, data) => {
                $('.add-quote').off('click').on('click', async function(e) {
                    e.preventDefault();
                    const id = $(this).attr('value');
                    try {
                        const result = await Swal.fire({
                            title: '¿Añadir paciente a la cola de citas?',
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
                            if (response.status === 200 && response.data.status === true) {
                                Swal.fire('Operación exitosa', response.data.messages, 'success');
                            } else {
                                Swal.fire('Operación fallida', response.data.messages, 'error');
                            }
                        }
                    } catch (error) {
                        console.error(error);
                    }
                });

                $('.edit-row').off('click').on('click', function(e) {
                    e.preventDefault();
                    let id = $(this).attr('value');
                    window.location.href = `${API_BASE_URL}/histories/edit/${id}`;
                });

                $('.delete-row').off('click').on('click', async function(e) {
                    e.preventDefault();
                    const id = $(this).attr('value');
                    try {
                        const result = await Swal.fire({
                            title: '¿Estás seguro de eliminar?',
                            text: 'Si borras la historia clínica todos los datos de este paciente serán eliminados.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, borrar',
                            cancelButtonText: 'Cancelar'
                        });
                        if (result.isConfirmed) {
                            const response = await axios.delete(`${API_BASE_URL}/histories/${id}`);
                            if (response.status === 200 && response.data.status === true) {
                                Swal.fire('Operación exitosa', response.data.messages, 'success');
                                if ($('#LoadRecordsButton').length) {
                                    $('#LoadRecordsButton').click();
                                }
                            } else {
                                Swal.fire('Operación fallida', response.data.messages || 'No se pudo eliminar', 'error');
                            }
                        }
                    } catch (error) {
                        console.error(error);
                    }
                });
            }
        });

        const LoadRecordsButton = $('#LoadRecordsButton');
        if (LoadRecordsButton.length) {
            LoadRecordsButton.click(function(e) {
                e.preventDefault();
                $('#histories').jtable('load', {
                    search: $('#search').val()
                });
            });
            LoadRecordsButton.click();
        }
    }
});

// ==========================================
// 9. GLOBAL HELPER FUNCTIONS
// ==========================================

/**
 * Syncs the hidden ISO (yyyy-mm-dd) field from a dd-mm-yyyy display string.
 * Returns the ISO string or empty string if invalid.
 */
function syncISOField(displayValue) {
    if (!displayValue || displayValue.length < 10) {
        $('#fecha_nacimiento_iso').val('');
        return '';
    }
    // Expect dd-mm-yyyy
    const parts = displayValue.split('-');
    if (parts.length !== 3 || parts[2].length !== 4) {
        $('#fecha_nacimiento_iso').val('');
        return '';
    }
    const day   = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10);
    const year  = parseInt(parts[2], 10);

    // Basic sanity checks
    if (day < 1 || day > 31 || month < 1 || month > 12 || year < 1900) {
        $('#fecha_nacimiento_iso').val('');
        return '';
    }

    const d = new Date(year, month - 1, day);
    if (isNaN(d.getTime()) || d.getDate() !== day || d.getMonth() !== month - 1 || d.getFullYear() !== year) {
        $('#fecha_nacimiento_iso').val('');
        return '';
    }

    const iso = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    $('#fecha_nacimiento_iso').val(iso);
    return iso;
}

/**
 * Calculates exact age in years from a dd-mm-yyyy or yyyy-mm-dd string.
 */
function getAge(dateString) {
    if (!dateString) {
        $('#age').val('');
        return;
    }
    const parts = dateString.split(/[-/]/);
    if (parts.length < 3) return;

    let year, month, day;
    if (parts[0].length === 4) {
        // yyyy-mm-dd
        year  = parseInt(parts[0], 10);
        month = parseInt(parts[1], 10) - 1;
        day   = parseInt(parts[2], 10);
    } else {
        // dd-mm-yyyy
        year  = parseInt(parts[2], 10);
        month = parseInt(parts[1], 10) - 1;
        day   = parseInt(parts[0], 10);
    }

    const birthDate = new Date(year, month, day);
    const today = new Date();

    if (isNaN(birthDate.getTime()) || birthDate > today || year < 1900) {
        $('#age').val('');
        return;
    }

    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    $('#age').val(Math.max(0, age));
}

/**
 * RENIEC/SUNAT Lookup for DNI
 */
async function consultaDatosSUNAT(dni) {
    if (!dni || dni.length !== 8) return;

    const btn = $('#btnSearchDni');
    const icon = $('#iconSearchDni');
    const spinner = $('#spinnerSearchDni');
    const feedback = $('#dniFeedback');

    btn.prop('disabled', true);
    icon.addClass('d-none');
    spinner.removeClass('d-none');
    feedback.text('').hide();

    const formData = new FormData();
    formData.append('dni', dni);
    formData.append('_token', typeof token !== 'undefined' ? token : '');

    try {
        const response = await axios.post(`${API_BASE_URL}/histories/dni`, formData);
        
        let data = response.data;
        if (typeof data === 'string') {
            try { data = JSON.parse(data); } catch(e) {}
        }

        if (data && (data.first_name || data.nombres)) {
            const firstName = data.first_name || data.nombres || '';
            const firstLastName = data.first_last_name || data.apellido_paterno || '';
            const secondLastName = data.second_last_name || data.apellido_materno || '';
            const fullName = `${firstName} ${firstLastName} ${secondLastName}`.trim();

            $('#nombres').val(fullName.toUpperCase()).removeClass('is-invalid').addClass('is-valid');
            $('#dni').removeClass('is-invalid').addClass('is-valid');

            // Optional Toast
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: 'Datos obtenidos de RENIEC'
            });
        } else {
            $('#dni').removeClass('is-invalid');
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'info',
                title: 'No se encontró en RENIEC. Ingrese los nombres manualmente.'
            });
        }
    } catch (error) {
        console.warn('Error consultando RENIEC:', error);
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true
        });
        Toast.fire({
            icon: 'info',
            title: 'Servicio RENIEC no disponible. Ingrese nombres manualmente.'
        });
    } finally {
        btn.prop('disabled', false);
        icon.removeClass('d-none');
        spinner.addClass('d-none');
    }
}
