$(document).ready(function () {
    // Configuración inicial de fechas
    let currentDate = $('#selected_date').val() || getTodayDateString();

    // Cargar datos iniciales
    initializeDateDisplay(currentDate);
    loadStats(currentDate);

    /* 1. NAVEGACIÓN Y SELECCIÓN DE FECHAS */
    $('#selected_date').on('change', function () {
        const val = $(this).val();
        if (val) {
            currentDate = val;
            updateDateButtonsState(currentDate);
            initializeDateDisplay(currentDate);
            loadAppointments(currentDate, $('#filter_status').val());
            loadStats(currentDate);
        }
    });

    $('#btn_today').on('click', function () {
        currentDate = getTodayDateString();
        $('#selected_date').val(currentDate);
        updateDateButtonsState(currentDate);
        initializeDateDisplay(currentDate);
        loadAppointments(currentDate, $('#filter_status').val());
        loadStats(currentDate);
    });

    $('#btn_tomorrow').on('click', function () {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        currentDate = formatDateToString(tomorrow);
        $('#selected_date').val(currentDate);
        updateDateButtonsState(currentDate);
        initializeDateDisplay(currentDate);
        loadAppointments(currentDate, $('#filter_status').val());
        loadStats(currentDate);
    });

    $('#btn_prev_day').on('click', function () {
        const d = new Date(currentDate + 'T00:00:00');
        d.setDate(d.getDate() - 1);
        currentDate = formatDateToString(d);
        $('#selected_date').val(currentDate);
        updateDateButtonsState(currentDate);
        initializeDateDisplay(currentDate);
        loadAppointments(currentDate, $('#filter_status').val());
        loadStats(currentDate);
    });

    $('#btn_next_day').on('click', function () {
        const d = new Date(currentDate + 'T00:00:00');
        d.setDate(d.getDate() + 1);
        currentDate = formatDateToString(d);
        $('#selected_date').val(currentDate);
        updateDateButtonsState(currentDate);
        initializeDateDisplay(currentDate);
        loadAppointments(currentDate, $('#filter_status').val());
        loadStats(currentDate);
    });

    $('#filter_status').on('change', function () {
        loadAppointments(currentDate, $(this).val());
    });

    $('#btn_refresh_table').on('click', function () {
        loadAppointments(currentDate, $('#filter_status').val());
        loadStats(currentDate);
        alertNotify('info', 'Lista actualizada');
    });

    /* 2. CARGA DE CITAS (ORDEN FIFO) Y ESTADÍSTICAS */

    async function loadAppointments(date, status = '') {
        const tbody = $('#table_appointments_body');
        tbody.html(`
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div>
                    Cargando lista de turnos...
                </td>
            </tr>
        `);

        try {
            const url = `${API_BASE_URL}/citas/list?date=${encodeURIComponent(date)}${status ? '&status=' + encodeURIComponent(status) : ''}`;
            const response = await axios.get(url);

            if (response.data && response.data.status) {
                const items = response.data.data;
                renderAppointmentsTable(items);
            } else {
                tbody.html(`<tr><td colspan="8" class="text-center py-4 text-danger">Error al cargar la información.</td></tr>`);
            }
        } catch (error) {
            console.error('Error cargando citas:', error);
            tbody.html(`<tr><td colspan="8" class="text-center py-4 text-danger">Error de conexión con el servidor.</td></tr>`);
        }
    }

    async function loadStats(date) {
        try {
            const response = await axios.get(`${API_BASE_URL}/citas/stats?date=${encodeURIComponent(date)}`);
            if (response.data && response.data.status) {
                $('#stat_total').text(response.data.total || 0);
                $('#stat_espera').text(response.data.en_espera || 0);
                $('#stat_atendidos').text(response.data.atendidos || 0);
                $('#stat_pendientes').text(response.data.pendientes || 0);
            }
        } catch (error) {
            console.error('Error cargando estadísticas:', error);
        }
    }

    function renderAppointmentsTable(items) {
        const tbody = $('#table_appointments_body');

        if (!items || items.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="text-muted mb-2">
                            <i class="bi bi-calendar-x" style="font-size: 2.5rem; opacity: 0.6;"></i>
                        </div>
                        <h6 class="text-dark font-weight-bold">No hay pacientes agendados para este día</h6>
                        <p class="text-muted small mb-3">La cola de atención está vacía para la fecha seleccionada.</p>
                        <button type="button" class="btn btn-primary btn-sm font-weight-bold btn-open-schedule-from-empty">
                            <i class="bi bi-plus-circle"></i> Agendar Cita para este día
                        </button>
                    </td>
                </tr>
            `);
            return;
        }

        let html = '';
        items.forEach((item) => {
            const statusBadge = getStatusBadge(item.status_id, item.status_desc);
            // Serialise routes so the floating menu can read them from the button
            const routesJson = escapeHtml(JSON.stringify(item.routes || {}));

            html += `
                <tr data-id="${item.id}"
                    data-history-id="${item.history_id || ''}"
                    data-dni="${escapeHtml(item.dni || '')}"
                    data-patient="${escapeHtml(item.nombres)}"
                    data-date="${item.fecha_cita}"
                    data-time="${item.hora_raw}"
                    title="Clic para ver historia clínica y controles previos">
                    <td class="text-center align-middle">
                        <span class="badge badge-turno shadow-xs">#${item.turn_number}</span>
                    </td>
                    <td class="align-middle">
                        <a href="javascript:void(0)" class="font-weight-bold text-primary text-decoration-none btn-quick-view-patient" data-history-id="${item.history_id || ''}" title="Ver Historia y Controles">
                            ${escapeHtml(item.nombres)}
                        </a>
                        ${item.tipo_atencion_badge || ''}
                    </td>
                    <td class="align-middle">
                        <span class="badge badge-light border font-weight-normal">${escapeHtml(item.dni)}</span>
                    </td>
                    <td class="align-middle text-muted">
                        ${item.telefono !== '--' ? `<i class="bi bi-telephone text-secondary mr-1"></i>${escapeHtml(item.telefono)}` : '<span class="text-muted">--</span>'}
                    </td>
                    <td class="align-middle">
                        <i class="bi bi-clock text-info mr-1"></i>
                        <span class="font-weight-bold ${item.hora_raw ? 'text-dark' : 'text-muted'}">${escapeHtml(item.hora_cita)}</span>
                    </td>
                    <td class="align-middle">
                        <span class="text-truncate d-inline-block" style="max-width: 220px;" title="${escapeHtml(item.motivo)}">
                            ${escapeHtml(item.motivo)}
                        </span>
                    </td>
                    <td class="text-center align-middle">
                        ${statusBadge}
                    </td>
                    <td class="text-center align-middle">
                        <button type="button"
                            class="btn btn-sm btn-outline-secondary btn-cita-actions"
                            data-id="${item.id}"
                            data-status="${item.status_id}"
                            data-patient="${escapeHtml(item.nombres)}"
                            data-date="${item.fecha_formato}"
                            data-rawdate="${item.fecha_cita}"
                            data-time="${item.hora_raw}"
                            data-routes="${routesJson}"
                            title="Abrir acciones">
                            <i class="bi bi-three-dots-vertical"></i> Acciones
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.html(html);
    }

    /* ── FLOATING ACTIONS MENU ─────────────────────────────────────────────────── */
    // One reusable floating panel injected into <body> once
    const $fcm = $('<div id="floating-cita-menu"></div>').appendTo('body');

    function closeFCM() {
        $fcm.hide().empty();
    }

    // Close on outside click / scroll
    $(document).on('click.fcm', function (e) {
        if (!$(e.target).closest('#floating-cita-menu, .btn-cita-actions').length) {
            closeFCM();
        }
    });
    $(window).on('scroll.fcm resize.fcm', function () { closeFCM(); });
    $('#table_appointments').closest('.table-responsive').on('scroll.fcm', function () { closeFCM(); });

    $(document).on('click', '.btn-cita-actions', function (e) {
        e.stopPropagation();
        const $btn    = $(this);
        const id      = $btn.data('id');
        const statusId= parseInt($btn.data('status'));
        const patient = $btn.data('patient');
        const dateFmt = $btn.data('date');
        const rawdate = $btn.data('rawdate');
        const time    = $btn.data('time');
        let   routes  = {};
        try { routes = JSON.parse($btn.attr('data-routes') || '{}'); } catch(e) {}

        // Build menu HTML
        let menuHtml = `
            <h6 class="dropdown-header">CAMBIAR ESTADO</h6>
            ${statusId !== 7 ? `<a class="dropdown-item btn-quick-status" href="javascript:void(0)" data-id="${id}" data-status="7"><i class="bi bi-clock text-info"></i> Pasar a 'En Espera'</a>` : ''}
            ${statusId !== 6 ? `<a class="dropdown-item btn-quick-status" href="javascript:void(0)" data-id="${id}" data-status="6"><i class="bi bi-check2-circle text-success"></i> Marcar 'Atendido'</a>` : ''}
            ${statusId !== 1 ? `<a class="dropdown-item btn-quick-status" href="javascript:void(0)" data-id="${id}" data-status="1"><i class="bi bi-hourglass-split text-warning"></i> Marcar 'Pendiente'</a>` : ''}
            ${statusId !== 3 ? `<a class="dropdown-item btn-quick-status text-danger" href="javascript:void(0)" data-id="${id}" data-status="3"><i class="bi bi-x-circle"></i> Cancelar Cita</a>` : ''}
            <div class="dropdown-divider"></div>
            <h6 class="dropdown-header">GESTIÓN DE CITA</h6>
            <a class="dropdown-item btn-open-reschedule" href="javascript:void(0)"
                data-id="${id}" data-patient="${patient}" data-date="${dateFmt}"
                data-rawdate="${rawdate}" data-time="${time}">
                <i class="bi bi-arrow-repeat text-warning"></i> Reagendar Fecha
            </a>
            <div class="dropdown-divider"></div>
            <h6 class="dropdown-header">ATENCIÓN MÉDICA</h6>
            ${routes.control_add  ? `<a class="dropdown-item" href="${routes.control_add}"><i class="bi bi-journal-plus text-primary"></i> Nuevo Control</a>` : ''}
            ${routes.exam_add     ? `<a class="dropdown-item" href="${routes.exam_add}"><i class="bi bi-file-earmark-medical text-primary"></i> Nuevo Examen</a>` : ''}
            ${routes.report_add   ? `<a class="dropdown-item" href="${routes.report_add}"><i class="bi bi-file-earmark-text text-primary"></i> Nuevo Informe</a>` : ''}
            ${routes.history_edit ? `<a class="dropdown-item" href="${routes.history_edit}"><i class="bi bi-person-lines-fill text-primary"></i> Editar Historia</a>` : ''}
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger btn-delete-cita" href="javascript:void(0)" data-id="${id}">
                <i class="bi bi-trash"></i> Eliminar Registro
            </a>
        `;

        $fcm.html(menuHtml);

        // Position relative to button using viewport coordinates (fixed positioning)
        const rect = this.getBoundingClientRect();
        const menuH = 380; // estimated max height
        const viewH = window.innerHeight;

        let top = rect.bottom + 4;
        // Flip upward if not enough space below
        if (top + menuH > viewH - 16) {
            top = rect.top - menuH - 4;
            if (top < 8) top = 8;
        }

        const menuW = 224;
        let left = rect.right - menuW;
        if (left < 8) left = rect.left;
        if (left + menuW > window.innerWidth - 8) left = window.innerWidth - menuW - 8;

        $fcm.css({ top: top + 'px', left: left + 'px' }).show();
    });

    function getStatusBadge(statusId, statusDesc) {
        // Colores planos y sólidos - SIN DEGRADADOS
        switch (parseInt(statusId)) {
            case 1: // PENDIENTE
                return `<span class="badge badge-warning text-dark font-weight-bold px-2 py-1"><i class="bi bi-hourglass-split"></i> PENDIENTE</span>`;
            case 2: // CONFIRMADO
                return `<span class="badge badge-primary font-weight-bold px-2 py-1"><i class="bi bi-calendar-check"></i> CONFIRMADO</span>`;
            case 3: // CANCELADO
                return `<span class="badge badge-danger font-weight-bold px-2 py-1"><i class="bi bi-x-circle"></i> CANCELADO</span>`;
            case 4: // REAGENDADO
                return `<span class="badge badge-secondary font-weight-bold px-2 py-1"><i class="bi bi-arrow-repeat"></i> REAGENDADO</span>`;
            case 5: // NO ASISTIO
                return `<span class="badge badge-dark font-weight-bold px-2 py-1"><i class="bi bi-person-x"></i> NO ASISTIÓ</span>`;
            case 6: // ATENDIDO
                return `<span class="badge badge-success font-weight-bold px-2 py-1"><i class="bi bi-check2-circle"></i> ATENDIDO</span>`;
            case 7: // EN ESPERA
                return `<span class="badge badge-info font-weight-bold px-2 py-1"><i class="bi bi-clock"></i> EN ESPERA</span>`;
            default:
                return `<span class="badge badge-light border font-weight-bold px-2 py-1">${escapeHtml(statusDesc)}</span>`;
        }
    }

    /* 3. MODAL AGENDAR CITA & BÚSQUEDA DE PACIENTES */
    function openScheduleModal() {
        // Sincronizar fecha del modal con la fecha activa
        $('#existing_fecha_cita').val(currentDate);
        $('#quick_fecha_cita').val(currentDate);

        // Limpiar búsqueda
        resetPatientSearch();

        // Limpiar formulario rápido
        $('#form_quick_patient')[0].reset();
        $('#quick_fecha_cita').val(currentDate);
        $('#quick_dni_feedback').hide().text('');

        // Activar pestaña 1 por defecto
        $('#tab-search-link').tab('show');

        $('#modal_schedule_appointment').modal('show');
    }

    $('#btn_open_schedule_modal').on('click', openScheduleModal);
    $(document).on('click', '.btn-open-schedule-from-empty', openScheduleModal);

    $('#btn_switch_to_quick_patient').on('click', function () {
        $('#tab-quick-link').tab('show');
        $('#quick_dni').focus();
    });

    let searchTimeout = null;
    $('#search_patient_input').on('input', function () {
        const query = $(this).val().trim();
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            $('#patient_search_results').hide().empty();
            return;
        }

        searchTimeout = setTimeout(async () => {
            try {
                const res = await axios.get(`${API_BASE_URL}/citas/search-patients?q=${encodeURIComponent(query)}`);
                const results = res.data;
                const container = $('#patient_search_results');

                if (!results || results.length === 0) {
                    container.html(`
                        <div class="p-3 text-center text-muted small">
                            No se encontraron pacientes con esa búsqueda.
                            <a href="javascript:void(0)" class="font-weight-bold text-primary ml-1" id="link_goto_quick_from_results">
                                Registrar nuevo paciente
                            </a>
                        </div>
                    `).show();
                    return;
                }

                let listHtml = '';
                results.forEach((p) => {
                    listHtml += `
                        <div class="patient-search-result-item" data-id="${p.id}" data-dni="${p.dni}" data-name="${escapeHtml(p.nombres)}" data-phone="${escapeHtml(p.telefono)}" data-age="${p.edad}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="font-weight-bold text-dark">${escapeHtml(p.nombres)}</div>
                                    <div class="small text-muted">DNI: <span class="badge badge-light border">${p.dni}</span> | Celular: ${p.telefono} | Edad: ${p.edad}</div>
                                </div>
                                <div>
                                    <span class="btn btn-xs btn-outline-primary"><i class="bi bi-check-lg"></i> Seleccionar</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                container.html(listHtml).show();
            } catch (err) {
                console.error('Error buscando paciente:', err);
            }
        }, 300);
    });

    $(document).on('click', '#link_goto_quick_from_results', function () {
        $('#tab-quick-link').tab('show');
        $('#quick_dni').focus();
    });

    $(document).on('click', '.patient-search-result-item', function () {
        const id = $(this).data('id');
        const dni = $(this).data('dni');
        const name = $(this).data('name');
        const phone = $(this).data('phone');
        const age = $(this).data('age');

        selectPatient({ id, dni, name, phone, age });
    });

    function selectPatient(patient) {
        $('#existing_patient_id').val(patient.id);
        $('#disp_patient_name').text(patient.name);
        $('#disp_patient_dni').text(patient.dni);
        $('#disp_patient_phone').text(patient.phone);
        $('#disp_patient_age').text(patient.age);

        // Preseleccionar chip de Control (2) por defecto para paciente existente
        $('#existing_id_tipo_atencion').val('2');
        $('#form_schedule_existing .chip-tipo-btn').removeClass('active');
        $('#form_schedule_existing .chip-tipo-btn[data-tipo="2"]').addClass('active');

        $('#selected_patient_display').show();
        $('#patient_search_results').hide().empty();
        $('#search_patient_input').val('').prop('disabled', true);
        $('#box_not_found_prompt').hide();

        $('#btn_submit_existing').prop('disabled', false);
    }

    $('#btn_deselect_patient').on('click', function () {
        resetPatientSearch();
    });

    $('#btn_clear_search_patient').on('click', function () {
        resetPatientSearch();
    });

    function resetPatientSearch() {
        $('#existing_patient_id').val('');
        $('#search_patient_input').val('').prop('disabled', false).focus();
        $('#selected_patient_display').hide();
        $('#patient_search_results').hide().empty();
        $('#box_not_found_prompt').show();
        $('#btn_submit_existing').prop('disabled', true);
    }

    // Manejador de botones Chip para Selección de Tipo de Atención en 1 clic
    $(document).on('click', '.chip-tipo-btn', function (e) {
        e.preventDefault();
        const group = $(this).closest('.chip-tipo-group');
        group.find('.chip-tipo-btn').removeClass('active');
        $(this).addClass('active');
        const tipoVal = $(this).data('tipo');
        const targetInput = $(this).closest('.form-group').find('input[name="id_tipo_atencion"]');
        targetInput.val(tipoVal);
        $(this).find('input[type="radio"]').prop('checked', true);
    });

    /* 4. ENVÍO: AGENDAR PACIENTE EXISTENTE */
    $('#form_schedule_existing').on('submit', async function (e) {
        e.preventDefault();

        const patientId = $('#existing_patient_id').val();
        if (!patientId) {
            Swal.fire('Atención', 'Debe seleccionar un paciente de la lista antes de continuar.', 'warning');
            return;
        }

        const btn = $('#btn_submit_existing');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Guardando...');

        const formData = {
            id_historia: patientId,
            fecha_cita: $('#existing_fecha_cita').val(),
            hora_cita: $('#existing_hora_cita').val() || null,
            motivo: $('#existing_motivo').val() || null,
            id_tipo_atencion: $('#existing_id_tipo_atencion').val() || 2,
            _token: token,
        };

        try {
            const response = await axios.post(`${API_BASE_URL}/citas/store`, formData);

            if (response.data && response.data.status) {
                $('#modal_schedule_appointment').modal('hide');
                alertNotify('success', response.data.messages);

                // Si la fecha agendada coincide con la vista actual, recargar; de lo contrario actualizar estadísticas
                if ($('#existing_fecha_cita').val() === currentDate) {
                    loadAppointments(currentDate, $('#filter_status').val());
                } else {
                    Swal.fire({
                        title: '¡Cita Programada!',
                        text: `La cita fue asignada para el ${formatDateSpanish($('#existing_fecha_cita').val())} (Turno #${response.data.numero_turno}). ¿Desea ir a esa fecha?`,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, ver esa fecha',
                        cancelButtonText: 'Permanecer aquí'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            currentDate = $('#existing_fecha_cita').val();
                            $('#selected_date').val(currentDate);
                            updateDateButtonsState(currentDate);
                            initializeDateDisplay(currentDate);
                            loadAppointments(currentDate, $('#filter_status').val());
                        }
                    });
                }
                loadStats(currentDate);
            }
        } catch (error) {
            console.error('Error al agendar cita:', error);
            if (error.response && error.response.status === 422) {
                Swal.fire('Aviso de Validación', error.response.data.messages || 'Revise los campos.', 'warning');
            } else {
                Swal.fire('Error', 'No se pudo agendar la cita. Intente de nuevo.', 'error');
            }
        } finally {
            btn.prop('disabled', false).html('<i class="bi bi-calendar-check"></i> Agendar Cita');
        }
    });

    /* 5. REGISTRO RÁPIDO DE PACIENTE & RENIEC */
    $('#btn_quick_search_reniec').on('click', async function () {
        const dni = $('#quick_dni').val().trim();
        if (!dni || dni.length !== 8) {
            $('#quick_dni_feedback').text('Ingrese un DNI válido de 8 dígitos para consultar.').show();
            $('#quick_dni').focus();
            return;
        }
        $('#quick_dni_feedback').hide();

        const btn = $(this);
        const icon = $('#reniec_icon');
        const spinner = $('#reniec_spinner');

        btn.prop('disabled', true);
        icon.addClass('d-none');
        spinner.removeClass('d-none');

        const formData = new FormData();
        formData.append('dni', dni);
        formData.append('_token', token);

        try {
            const response = await axios.post(`${API_BASE_URL}/histories/dni`, formData);
            let data = response.data;
            if (typeof data === 'string') {
                try { data = JSON.parse(data); } catch (e) { }
            }

            if (data && (data.first_name || data.nombres)) {
                const firstName = data.first_name || data.nombres || '';
                const firstLastName = data.first_last_name || data.apellido_paterno || '';
                const secondLastName = data.second_last_name || data.apellido_materno || '';
                const fullName = `${firstLastName} ${secondLastName} ${firstName}`.trim();

                $('#quick_nombres').val(fullName.toUpperCase());
                alertNotify('success', 'Datos obtenidos de RENIEC');
            } else {
                $('#quick_dni_feedback').text('No se encontraron datos en RENIEC para este DNI. Ingrese los nombres manualmente.').show();
            }
        } catch (error) {
            console.error('Error consulta RENIEC:', error);
            $('#quick_dni_feedback').text('No se pudo conectar con el servicio RENIEC. Ingrese los nombres manualmente.').show();
        } finally {
            btn.prop('disabled', false);
            icon.removeClass('d-none');
            spinner.addClass('d-none');
        }
    });

    // Enter en campo DNI activa consulta RENIEC
    $('#quick_dni').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#btn_quick_search_reniec').click();
        }
    });

    $('#form_quick_patient').on('submit', async function (e) {
        e.preventDefault();

        const btn = $('#btn_submit_quick');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Registrando...');

        const formData = {
            id_td: $('#quick_id_td').val(),
            dni: $('#quick_dni').val().trim(),
            nombres: $('#quick_nombres').val().trim(),
            fecha_nacimiento: $('#quick_fecha_nacimiento').val(),
            id_sexo: $('#quick_id_sexo').val(),
            telefono: $('#quick_telefono').val().trim(),
            email: $('#quick_email').val().trim() || null,
            fecha_cita: $('#quick_fecha_cita').val(),
            hora_cita: $('#quick_hora_cita').val() || null,
            motivo: $('#quick_motivo').val() || 'Primera consulta / Registro rápido',
            id_tipo_atencion: $('#quick_id_tipo_atencion').val() || 1,
            _token: token,
        };

        try {
            const response = await axios.post(`${API_BASE_URL}/citas/quick-patient`, formData);

            if (response.data && response.data.status) {
                $('#modal_schedule_appointment').modal('hide');
                alertNotify('success', response.data.messages);

                if ($('#quick_fecha_cita').val() === currentDate) {
                    loadAppointments(currentDate, $('#filter_status').val());
                } else {
                    currentDate = $('#quick_fecha_cita').val();
                    $('#selected_date').val(currentDate);
                    updateDateButtonsState(currentDate);
                    initializeDateDisplay(currentDate);
                    loadAppointments(currentDate, $('#filter_status').val());
                }
                loadStats(currentDate);
            }
        } catch (error) {
            console.error('Error registro rápido:', error);
            if (error.response && error.response.status === 422) {
                Swal.fire('Aviso de Validación', error.response.data.messages || 'Verifique los campos del formulario.', 'warning');
            } else {
                Swal.fire('Error', 'No se pudo registrar el paciente. Verifique si el DNI ya existe.', 'error');
            }
        } finally {
            btn.prop('disabled', false).html('<i class="bi bi-person-check-fill"></i> Registrar Paciente y Agendar Cita');
        }
    });

    /* 6. REAGENDAMIENTO DE CITAS */
    $(document).on('click', '.btn-open-reschedule', function () {
        const id = $(this).data('id');
        const patient = $(this).data('patient');
        const currentDateFormat = $(this).data('date');
        const rawDate = $(this).data('rawdate');
        const time = $(this).data('time');

        $('#reschedule_cita_id').val(id);
        $('#reschedule_patient_name').text(patient);
        $('#reschedule_current_date').text(currentDateFormat);
        $('#reschedule_new_date').val(rawDate);
        $('#reschedule_new_time').val(time);
        $('#reschedule_note').val('');

        $('#modal_reschedule_appointment').modal('show');
    });

    $('#form_reschedule_appointment').on('submit', async function (e) {
        e.preventDefault();

        const id = $('#reschedule_cita_id').val();
        const newDate = $('#reschedule_new_date').val();
        const newTime = $('#reschedule_new_time').val();
        const note = $('#reschedule_note').val();

        if (!newDate) {
            Swal.fire('Atención', 'Debe seleccionar una nueva fecha.', 'warning');
            return;
        }

        const btn = $('#btn_submit_reschedule');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Procesando...');

        try {
            const response = await axios.post(`${API_BASE_URL}/citas/${id}/reschedule`, {
                fecha_cita: newDate,
                hora_cita: newTime || null,
                motivo: note || null,
                _token: token,
            });

            if (response.data && response.data.status) {
                $('#modal_reschedule_appointment').modal('hide');
                alertNotify('success', response.data.messages);

                loadAppointments(currentDate, $('#filter_status').val());
                loadStats(currentDate);
            }
        } catch (error) {
            console.error('Error reagendando:', error);
            if (error.response && error.response.status === 422) {
                Swal.fire('Validación', error.response.data.messages || 'No se pudo reagendar.', 'warning');
            } else {
                Swal.fire('Error', 'No se pudo reagendar la cita. Intente de nuevo.', 'error');
            }
        } finally {
            btn.prop('disabled', false).html('<i class="bi bi-arrow-repeat"></i> Confirmar Reagendamiento');
        }
    });

    /* 7. CAMBIO RÁPIDO DE ESTADO & ELIMINACIÓN */
    $(document).on('click', '.btn-quick-status', async function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const statusId = $(this).data('status');

        try {
            const response = await axios.post(`${API_BASE_URL}/citas/${id}/status`, {
                id_estado: statusId,
                _token: token,
            });

            if (response.data && response.data.status) {
                alertNotify('success', response.data.messages);
                loadAppointments(currentDate, $('#filter_status').val());
                loadStats(currentDate);
            }
        } catch (error) {
            console.error('Error cambiando estado:', error);
            Swal.fire('Error', 'No se pudo cambiar el estado.', 'error');
        }
    });

    $(document).on('click', '.btn-delete-cita', async function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        const result = await Swal.fire({
            title: '¿Eliminar cita?',
            text: 'Esta acción cancelará el turno asignado al paciente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                const response = await axios.delete(`${API_BASE_URL}/citas/${id}`, {
                    data: { _token: token }
                });

                if (response.data && response.data.status) {
                    alertNotify('success', response.data.messages);
                    loadAppointments(currentDate, $('#filter_status').val());
                    loadStats(currentDate);
                }
            } catch (error) {
                console.error('Error eliminando cita:', error);
                Swal.fire('Error', 'No se pudo eliminar el registro.', 'error');
            }
        }
    });

    /* 8. UTILITARIOS Y FORMATEO DE FECHAS */
    function getTodayDateString() {
        return formatDateToString(new Date());
    }

    function formatDateToString(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function formatDateSpanish(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }

    function initializeDateDisplay(dateStr) {
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            const dateObj = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formatted = dateObj.toLocaleDateString('es-ES', options);
            const capitalized = formatted.charAt(0).toUpperCase() + formatted.slice(1);
            $('#label_current_date_display').text(capitalized);
        }
    }

    function updateDateButtonsState(selected) {
        const today = getTodayDateString();
        const tom = new Date();
        tom.setDate(tom.getDate() + 1);
        const tomorrow = formatDateToString(tom);

        if (selected === today) {
            $('#btn_today').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#btn_tomorrow').removeClass('btn-primary').addClass('btn-outline-secondary');
        } else if (selected === tomorrow) {
            $('#btn_tomorrow').removeClass('btn-outline-secondary').addClass('btn-primary');
            $('#btn_today').removeClass('btn-primary').addClass('btn-outline-secondary');
        } else {
            $('#btn_today').removeClass('btn-primary').addClass('btn-outline-secondary');
            $('#btn_tomorrow').removeClass('btn-primary').addClass('btn-outline-secondary');
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /* ── DIRECT-CLICK ROW -> PATIENT CLINICAL HISTORY & PREVIOUS CHECK-UPS ─── */
    // Click on row (excluding action button and floating menu)
    $(document).on('click', '#table_appointments_body tr', function (e) {
        if ($(e.target).closest('.btn-cita-actions, #floating-cita-menu, .dropdown-menu').length) {
            return;
        }

        const historyId = $(this).data('history-id');
        const patientName = $(this).data('patient');

        if (!historyId) {
            Swal.fire({
                icon: 'warning',
                title: 'Historia clínica no encontrada',
                text: `El paciente ${patientName || ''} no tiene una historia clínica vinculada.`,
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        openPatientQuickView(historyId);
    });

    // Also support clicking on the patient name link
    $(document).on('click', '.btn-quick-view-patient', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const historyId = $(this).data('history-id') || $(this).closest('tr').data('history-id');
        if (historyId) {
            openPatientQuickView(historyId);
        }
    });

    // Clic en "Ver Receta" dentro de la tabla de controles del modal
    $(document).on('click', '.qv-view-appointment', function (e) {
        e.preventDefault();
        const appointmentId = $(this).attr('value');
        if (window.ModalDetails && typeof window.ModalDetails.showDetails === 'function') {
            window.ModalDetails.showDetails({
                id: appointmentId,
                type: 'appointments',
                titlePrefix: 'Detalles de la Cita'
            });
        }
    });

    // Mantener clase modal-open en body cuando se cierra un modal anidado (#modal-default)
    $(document).on('hidden.bs.modal', '#modal-default', function () {
        if ($('#modal_patient_quickview').hasClass('show')) {
            $('body').addClass('modal-open');
        }
    });

    async function openPatientQuickView(historyId) {
        const $modal = $('#modal_patient_quickview');
        const $loading = $('#qv_loading_state');
        const $content = $('#qv_content_state');

        // Reset and show loading state
        $loading.show();
        $content.hide();
        $('#qv_patient_name').text('Cargando datos del paciente...');
        $('#qv_patient_dni').html('<i class="bi bi-card-text mr-1"></i>DNI: --');
        $('#qv_patient_tipo_badge').empty();
        $('#qv_patient_deleted_badge').hide();
        $('#qv_appointments_tbody').empty();
        $('#qv_appointments_empty').hide();
        $('#table_qv_appointments').show();
        $('#tab-controles-link').tab('show');

        // Show modal
        $modal.modal('show');

        try {
            const response = await axios.get(`${API_BASE_URL}/histories/quick-view/${historyId}`);
            if (!response.data || !response.data.status) {
                throw new Error(response.data?.message || 'Error al obtener datos');
            }

            const { history, appointments, routes } = response.data;

            // Fill header
            $('#qv_patient_name').text(history.nombres || '--');
            $('#qv_patient_dni').html(`<i class="bi bi-card-text mr-1"></i>${history.tipo_documento || 'DNI'}: <b>${escapeHtml(history.dni || '--')}</b>`);
            
            if (history.tipo_atencion_desc) {
                $('#qv_patient_tipo_badge').html(`
                    <span class="badge ${history.tipo_atencion_color || 'badge-secondary'} ml-1 font-weight-normal py-1 px-2">
                        <i class="bi bi-tag-fill mr-1"></i>${escapeHtml(history.tipo_atencion_desc)}
                    </span>
                `);
            } else {
                $('#qv_patient_tipo_badge').empty();
            }

            if (history.is_deleted) {
                $('#qv_patient_deleted_badge').show();
            } else {
                $('#qv_patient_deleted_badge').hide();
            }

            // Fill Quick Summary Strip
            $('#qv_patient_edad').text(history.edad !== null ? history.edad : '--');
            $('#qv_patient_fn').text(history.fecha_nacimiento || '--');
            $('#qv_patient_sexo').text(history.sexo || '--');
            $('#qv_patient_telefono').html(history.telefono && history.telefono !== '--' 
                ? `<a href="tel:${escapeHtml(history.telefono)}" class="text-dark font-weight-bold text-decoration-none">${escapeHtml(history.telefono)}</a>`
                : '<span class="text-muted">--</span>');
            $('#qv_patient_gs').text(history.grupo_sanguineo || '--');
            $('#qv_patient_ocupacion').text(history.ocupacion || '--');

            // Fill Action Buttons in Footer and Header
            $('#qv_btn_edit_history').attr('href', routes?.history_edit || '#');
            $('#qv_btn_add_control').attr('href', routes?.control_add || '#');
            $('#qv_btn_add_first_control').attr('href', routes?.control_add || '#');
            $('#qv_btn_new_exam').attr('href', routes?.exam_add || '#');
            $('#qv_btn_new_report').attr('href', routes?.report_add || '#');

            // Fill Appointments (Check-ups) Table
            const count = appointments ? appointments.length : 0;
            $('#qv_appointments_count').text(count);

            if (count === 0) {
                $('#table_qv_appointments').hide();
                $('#qv_appointments_empty').show();
            } else {
                $('#table_qv_appointments').show();
                $('#qv_appointments_empty').hide();

                let appointmentsHtml = '';
                appointments.forEach((ap) => {
                    const deletedBadge = ap.is_deleted ? '<span class="badge badge-danger ml-1">Eliminado</span>' : '';
                    const timeBadge = ap.antiguedad ? `<span class="badge badge-light border text-muted font-weight-normal d-block mt-1">${escapeHtml(ap.antiguedad)}</span>` : '';

                    appointmentsHtml += `
                        <tr class="${ap.is_deleted ? 'table-danger' : ''}">
                            <td class="text-center align-middle font-weight-bold text-muted">${ap.index}</td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-dark"><i class="bi bi-calendar3 text-primary mr-1"></i>${escapeHtml(ap.fecha_formato)}</div>
                                ${timeBadge}
                                ${deletedBadge}
                            </td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-dark">${escapeHtml(ap.diagnostico || '--')}</div>
                            </td>
                            <td class="align-middle text-muted">
                                <span class="d-inline-block text-truncate" style="max-width: 250px;" title="${escapeHtml(ap.sintomas || '')}">
                                    ${escapeHtml(ap.sintomas || '--')}
                                </span>
                            </td>
                            <td class="align-middle">
                                <span class="d-inline-block text-truncate" style="max-width: 250px;" title="${escapeHtml(ap.tratamiento || ap.plan || '')}">
                                    ${escapeHtml(ap.tratamiento || ap.plan || '--')}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-xs qv-view-appointment mr-1" value="${ap.id}" title="Ver receta e indicaciones">
                                        <i class="bi bi-eye"></i> Receta
                                    </button>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-printer"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="${ap.print_a4}" target="_blank"><i class="bi bi-file-earmark-pdf text-danger mr-1"></i> Imprimir A4</a>
                                            <a class="dropdown-item" href="${ap.print_a5}" target="_blank"><i class="bi bi-file-earmark-pdf text-info mr-1"></i> Imprimir A5</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                $('#qv_appointments_tbody').html(appointmentsHtml);
            }

            // Fill Antecedents Tab
            const ant = history.antecedentes || {};
            $('#qv_ant_asma').html(renderBadgeYesNo(ant.asma));
            $('#qv_ant_epoc').html(renderBadgeYesNo(ant.epoc));
            $('#qv_ant_tbc').html(renderBadgeYesNo(ant.tuberculosis));
            $('#qv_ant_cancer').html(renderBadgeYesNo(ant.cancerpulmon));
            $('#qv_ant_neumonias').html(renderBadgeYesNo(ant.neumonias));
            $('#qv_ant_efusion').html(renderBadgeYesNo(ant.efusionpleural));

            $('#qv_ant_tabaco').text(ant.tabaquismo || 'No');
            $('#qv_ant_ipa').html(ant.ipa ? `<span class="badge badge-warning text-dark font-weight-bold">${ant.ipa} paquetes/año</span>` : '<span class="text-muted">No registrado</span>');
            $('#qv_ant_contactotbc').html(renderBadgeYesNo(ant.contactotbc));
            $('#qv_ant_biomasa').html(renderBadgeYesNo(ant.biomasa));
            $('#qv_ant_drogas').text(ant.alergias_drogas || 'No refiere alergias');

            $('#qv_ant_cirugias').text(ant.cirugias || 'No refiere cirugías');
            $('#qv_ant_hospitalizaciones').text(ant.hospitalizaciones || 'No refiere hospitalizaciones previas');
            $('#qv_ant_medicacion').text(ant.medicacion_habitual || 'Ninguna medicación continua registrada');
            $('#qv_ant_otros').text(ant.otros || ant.transfusiones ? `Transfusiones: ${ant.transfusiones || 'No'} | ${ant.otros || ''}` : 'Sin antecedentes adicionales');

            $('#qv_ant_motivo').text(ant.motivoconsulta || 'No especificado en filiación inicial');
            $('#qv_ant_relato').text(ant.relatocronologico || 'Sin relato cronológico registrado');

            // Switch to content
            $loading.hide();
            $content.show();

        } catch (error) {
            console.error('Error al cargar vista rápida de historia clínica:', error);
            $loading.html(`
                <div class="text-danger py-4">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem;"></i>
                    <h5 class="font-weight-bold mt-2">No se pudo cargar la historia clínica</h5>
                    <p class="small text-muted mb-3">${escapeHtml(error.message || 'Ocurrió un error inesperado al conectar con el servidor.')}</p>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
                </div>
            `);
        }
    }

    function renderBadgeYesNo(val) {
        if (!val || val.trim().toLowerCase() === 'no' || val.trim().toLowerCase() === 'negativo' || val.trim().toLowerCase() === 'no refiere' || val.trim().toLowerCase() === '0') {
            return '<span class="badge badge-light border text-muted">No refiere</span>';
        }
        return `<span class="badge badge-danger font-weight-bold"><i class="bi bi-check-circle mr-1"></i>${escapeHtml(val)}</span>`;
    }
});
