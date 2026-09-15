$(document).ready(function () {
    // Configuración inicial de fechas
    let currentDate = $('#selected_date').val() || getTodayDateString();

    // Cargar datos iniciales
    initializeDateDisplay(currentDate);
    loadAppointments(currentDate);
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

            html += `
                <tr data-id="${item.id}" data-patient="${escapeHtml(item.nombres)}" data-date="${item.fecha_cita}" data-time="${item.hora_raw}">
                    <td class="text-center align-middle">
                        <span class="badge badge-turno shadow-xs">#${item.turn_number}</span>
                    </td>
                    <td class="align-middle">
                        <a href="${item.routes.history_edit}" class="font-weight-bold text-primary text-decoration-none" title="Ver Historia">
                            ${escapeHtml(item.nombres)}
                        </a>
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
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Acciones
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm">
                                <h6 class="dropdown-header text-uppercase font-weight-bold small text-muted">Cambiar Estado</h6>
                                ${item.status_id !== 7 ? `<a class="dropdown-item btn-quick-status" href="javascript:void(0)" data-id="${item.id}" data-status="7"><i class="bi bi-clock text-info mr-2"></i> Pasar a 'En Espera'</a>` : ''}
                                ${item.status_id !== 6 ? `<a class="dropdown-item btn-quick-status" href="javascript:void(0)" data-id="${item.id}" data-status="6"><i class="bi bi-check2-circle text-success mr-2"></i> Marcar 'Atendido'</a>` : ''}
                                ${item.status_id !== 1 ? `<a class="dropdown-item btn-quick-status" href="javascript:void(0)" data-id="${item.id}" data-status="1"><i class="bi bi-hourglass-split text-warning mr-2"></i> Marcar 'Pendiente'</a>` : ''}
                                ${item.status_id !== 3 ? `<a class="dropdown-item btn-quick-status text-danger" href="javascript:void(0)" data-id="${item.id}" data-status="3"><i class="bi bi-x-circle text-danger mr-2"></i> Cancelar Cita</a>` : ''}
                                
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header text-uppercase font-weight-bold small text-muted">Gestión de Cita</h6>
                                <a class="dropdown-item btn-open-reschedule" href="javascript:void(0)" data-id="${item.id}" data-patient="${escapeHtml(item.nombres)}" data-date="${item.fecha_formato}" data-rawdate="${item.fecha_cita}" data-time="${item.hora_raw}">
                                    <i class="bi bi-arrow-repeat text-warning mr-2"></i> Reagendar Fecha
                                </a>

                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header text-uppercase font-weight-bold small text-muted">Atención Médica</h6>
                                <a class="dropdown-item" href="${item.routes.control_add}"><i class="bi bi-journal-plus text-primary mr-2"></i> Nuevo Control</a>
                                <a class="dropdown-item" href="${item.routes.exam_add}"><i class="bi bi-file-earmark-medical text-primary mr-2"></i> Nuevo Examen</a>
                                <a class="dropdown-item" href="${item.routes.report_add}"><i class="bi bi-file-earmark-text text-primary mr-2"></i> Nuevo Informe</a>
                                <a class="dropdown-item" href="${item.routes.history_edit}"><i class="bi bi-person-lines-fill text-primary mr-2"></i> Editar Historia</a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger btn-delete-cita" href="javascript:void(0)" data-id="${item.id}">
                                    <i class="bi bi-trash text-danger mr-2"></i> Eliminar Registro
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.html(html);
    }

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
            observaciones: $('#existing_observaciones').val() || null,
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
});
