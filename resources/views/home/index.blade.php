@extends('layouts.app')
@section('title', config('global.site_name', 'NeumoTar') . ' - Dashboard & Citas')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/citas.css') }}">
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6 col-12">
                        <h1 class="m-0 text-dark">
                            <i class="bi bi-speedometer2 text-primary"></i> Dashboard
                            <small class="text-muted font-weight-normal ml-2" style="font-size: 1rem;">| Panel de Control y Citas</small>
                        </h1>
                    </div>
                    <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                        <ol class="breadcrumb float-sm-right bg-transparent p-0 mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">
                <!-- 1. Indicadores Globales del Sistema -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $historias }}</h3>
                                <p>Historias Clínicas</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-file-medical-fill"></i>
                            </div>
                            <a href="{{ route('hcl.histories.home') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $examenes }}</h3>
                                <p>Exámenes</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-file-medical-fill"></i>
                            </div>
                            <a href="{{ route('hcl.exams.home') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $controles }}</h3>
                                <p>Controles</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-file-medical-fill"></i>
                            </div>
                            <a href="{{ route('hcl.appointments.home') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $users }}</h3>
                                <p>Usuarios</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <a href="{{ route('security.users.home') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- 2. Indicadores Estadísticos del Día Seleccionado -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="citas-stat-card stat-bg-total">
                            <div>
                                <div class="stat-value" id="stat_total">0</div>
                                <div class="stat-label">Total Citas del Día</div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="citas-stat-card stat-bg-espera">
                            <div>
                                <div class="stat-value" id="stat_espera">0</div>
                                <div class="stat-label">En Espera (Sala)</div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="citas-stat-card stat-bg-atendidos">
                            <div>
                                <div class="stat-value" id="stat_atendidos">0</div>
                                <div class="stat-label">Atendidos</div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="citas-stat-card stat-bg-pendientes">
                            <div>
                                <div class="stat-value" id="stat_pendientes">0</div>
                                <div class="stat-label">Pendientes</div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Barra de Selección de Fecha, Filtros y Acciones -->
                <div class="date-navigation-bar">
                    <div class="row align-items-center">
                        <div class="col-lg-5 col-md-6 col-12 mb-2 mb-lg-0">
                            <div class="d-flex align-items-center flex-wrap">
                                <div class="btn-group mr-2 mb-1" role="group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_prev_day"
                                        title="Día anterior">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm" id="btn_today">
                                        <i class="bi bi-calendar-check"></i> Hoy
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_tomorrow">
                                        Mañana
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_next_day"
                                        title="Día siguiente">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>
                                <div class="input-group input-group-sm mb-1" style="width: 170px;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white"><i class="bi bi-calendar3"></i></span>
                                    </div>
                                    <input type="date" class="form-control" id="selected_date"
                                        value="{{ $selectedDate }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12 mb-2 mb-lg-0">
                            <div class="d-flex align-items-center">
                                <label for="filter_status"
                                    class="mr-2 mb-0 text-muted font-weight-normal text-nowrap">Estado:</label>
                                <select id="filter_status" class="form-control form-control-sm">
                                    <option value="">Todos los estados</option>
                                    @foreach ($statuses as $st)
                                        <option value="{{ $st->id }}">{{ $st->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12 text-lg-right text-left">
                            <div class="btn-group flex-wrap">
                                <a href="{{ route('hcl.histories.add') }}" class="btn btn-primary btn-sm mr-2 mb-1" title="Registrar nueva historia clínica">
                                    <i class="bi bi-plus-circle"></i> Agregar nueva historia
                                </a>
                                <button type="button" class="btn btn-success btn-sm mb-1" id="btn_open_schedule_modal" title="Programar cita médica">
                                    <i class="bi bi-calendar-plus"></i> Agendar Cita
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Card Principal de la Lista de Citas (Orden FIFO) -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title m-0 text-dark font-weight-bold">
                            <i class="bi bi-list-ol text-primary"></i> Cola de Citas y Turnos
                            <span class="badge badge-light border ml-2 font-weight-normal" id="label_current_date_display"></span>
                        </h5>
                        <div>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_refresh_table"
                                title="Actualizar lista">
                                <i class="bi bi-arrow-clockwise"></i> Actualizar
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="table_appointments">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 80px;" class="text-center">Turno</th>
                                        <th>Paciente</th>
                                        <th>Documento</th>
                                        <th>Teléfono</th>
                                        <th>Hora / Llegada</th>
                                        <th>Motivo de Cita</th>
                                        <th style="width: 130px;" class="text-center">Estado</th>
                                        <th style="width: 170px;" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="table_appointments_body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-2">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> <b>Criterio FIFO (First-In, First-Out):</b> Los pacientes son
                            organizados estrictamente en orden correlativo de llegada y agendamiento para garantizar una
                            atención ordenada y justa.
                        </small>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <!-- MODAL: AGENDAR CITA (BÚSQUEDA Y REGISTRO RÁPIDO DE PACIENTES) -->
    <div class="modal fade" id="modal_schedule_appointment" tabindex="-1" role="dialog"
        aria-labelledby="modalScheduleTitle" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalScheduleTitle">
                        <i class="bi bi-calendar-plus text-primary"></i> Programar Cita Médica
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-0">
                    <!-- Pestañas de Navegación del Modal -->
                    <ul class="nav nav-tabs nav-justified bg-light" id="appointmentModalTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active py-3" id="tab-search-link" data-toggle="tab"
                                href="#tab-search-patient" role="tab" aria-controls="tab-search-patient"
                                aria-selected="true">
                                <i class="bi bi-search"></i> Buscar Paciente Existente
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-3" id="tab-quick-link" data-toggle="tab" href="#tab-quick-patient"
                                role="tab" aria-controls="tab-quick-patient" aria-selected="false">
                                <i class="bi bi-person-plus-fill"></i> Registro Rápido de Nuevo Paciente
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-4" id="appointmentModalTabContent">
                        <!-- PESTAÑA 1: BUSCAR PACIENTE EXISTENTE -->
                        <div class="tab-pane fade show active" id="tab-search-patient" role="tabpanel"
                            aria-labelledby="tab-search-link">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">
                                    Buscar Paciente en Base de Datos:
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="search_patient_input"
                                        placeholder="Escriba DNI o Apellidos y Nombres del paciente..."
                                        autocomplete="off">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="btn_clear_search_patient">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Ingrese al menos 2 caracteres para buscar en el
                                    registro histórico de pacientes.</small>
                            </div>

                            <!-- Lista de Resultados de Búsqueda -->
                            <div id="patient_search_results" class="border rounded bg-white mb-3"
                                style="max-height: 190px; overflow-y: auto; display: none;">
                                <!-- Items cargados dinámicamente -->
                            </div>

                            <!-- Tarjeta de Paciente Seleccionado -->
                            <div id="selected_patient_display" class="selected-patient-card" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="font-weight-bold text-success mb-1" id="disp_patient_name">--</h6>
                                        <div class="text-muted small">
                                            <span><b>DNI:</b> <span id="disp_patient_dni">--</span></span> |
                                            <span><b>Teléfono:</b> <span id="disp_patient_phone">--</span></span> |
                                            <span><b>Edad:</b> <span id="disp_patient_age">--</span></span>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                            id="btn_deselect_patient" title="Cambiar paciente">
                                            <i class="bi bi-x-circle"></i> Cambiar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Aviso de paciente no encontrado -->
                            <div class="alert alert-light border mb-3 py-2 text-center" id="box_not_found_prompt">
                                <span class="text-muted">¿No encuentra al paciente registrado?</span>
                                <a href="javascript:void(0)" class="font-weight-bold text-primary ml-1"
                                    id="btn_switch_to_quick_patient">
                                    <i class="bi bi-person-plus"></i> Registrar nuevo paciente aquí
                                </a>
                            </div>

                            <!-- Formulario de Agendamiento para Paciente Seleccionado -->
                            <form id="form_schedule_existing">
                                <input type="hidden" id="existing_patient_id" name="id_historia" value="">

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="existing_fecha_cita" class="font-weight-bold">Fecha de Cita: <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="existing_fecha_cita"
                                                name="fecha_cita" required value="{{ $selectedDate }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="d-block">Tipo de Atención: <span
                                            class="text-danger">*</span></label>
                                    <div class="btn-group btn-group-toggle w-100 chip-tipo-group" data-toggle="buttons">
                                        <label class="btn btn-outline-success chip-tipo-btn flex-fill" data-tipo="1">
                                            <input type="radio" name="existing_tipo_radio" value="1"
                                                autocomplete="off">
                                            <i class="bi bi-person-plus-fill mr-1"></i> <b>Nuevo</b>
                                        </label>
                                        <label class="btn btn-outline-primary chip-tipo-btn active flex-fill"
                                            data-tipo="2">
                                            <input type="radio" name="existing_tipo_radio" value="2"
                                                autocomplete="off" checked>
                                            <i class="bi bi-arrow-repeat mr-1"></i> <b>Control</b>
                                        </label>
                                        <label class="btn btn-outline-warning text-dark chip-tipo-btn flex-fill"
                                            data-tipo="3">
                                            <input type="radio" name="existing_tipo_radio" value="3"
                                                autocomplete="off">
                                            <i class="bi bi-person-check-fill mr-1"></i> <b>Continuador</b>
                                        </label>
                                    </div>
                                    <input type="hidden" name="id_tipo_atencion" id="existing_id_tipo_atencion"
                                        value="2">
                                    <small class="form-text text-muted">Haga clic en una opción para clasificar la atención
                                        del paciente.</small>
                                </div>

                                <div class="text-right">
                                    <button type="button" class="btn btn-secondary mr-2"
                                        data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary" id="btn_submit_existing" disabled>
                                        <i class="bi bi-calendar-check"></i> Agendar Cita
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- PESTAÑA 2: REGISTRO RÁPIDO DE PACIENTE -->
                        <div class="tab-pane fade" id="tab-quick-patient" role="tabpanel"
                            aria-labelledby="tab-quick-link">
                            <form id="form_quick_patient">
                                <div class="alert alert-info py-2 small mb-3 border-0">
                                    <i class="bi bi-info-circle-fill"></i> Ingrese el DNI y presione <b>Buscar RENIEC</b>
                                    para completar automáticamente los nombres del paciente.
                                </div>

                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="quick_id_td" class="font-weight-bold">Tipo Documento: <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" id="quick_id_td" name="id_td" required>
                                                @foreach ($documentTypes as $td)
                                                    <option value="{{ $td->id }}"
                                                        {{ $td->id == 1 ? 'selected' : '' }}>{{ $td->descripcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-12">
                                        <div class="form-group">
                                            <label for="quick_dni" class="font-weight-bold">Número de Documento / DNI:
                                                <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="quick_dni" name="dni"
                                                    placeholder="Ingrese DNI (8 dígitos)" maxlength="8" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-info" type="button"
                                                        id="btn_quick_search_reniec">
                                                        <span id="reniec_spinner"
                                                            class="spinner-border spinner-border-sm mr-1 d-none"
                                                            role="status"></span>
                                                        <i class="bi bi-search" id="reniec_icon"></i> Buscar RENIEC
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="quick_dni_feedback" class="text-danger small mt-1"
                                                style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="quick_nombres" class="font-weight-bold">Nombres y Apellidos Completos:
                                        <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="quick_nombres" name="nombres"
                                        placeholder="APELLIDOS Y NOMBRES" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="quick_fecha_nacimiento" class="font-weight-bold">Fecha de
                                                Nacimiento: <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="quick_fecha_nacimiento"
                                                name="fecha_nacimiento" required max="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="quick_id_sexo" class="font-weight-bold">Sexo: <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" id="quick_id_sexo" name="id_sexo" required>
                                                <option value="">Seleccione...</option>
                                                @foreach ($sexes as $sx)
                                                    <option value="{{ $sx->id }}">{{ $sx->descripcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="quick_telefono" class="font-weight-bold">Teléfono / Celular: <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="quick_telefono"
                                                name="telefono" placeholder="Número de contacto (9 dígitos)"
                                                maxlength="11" required>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-3">
                                <h6 class="font-weight-bold text-dark mb-3"><i
                                        class="bi bi-calendar2-event text-primary"></i> Datos de la Cita Inicial</h6>

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="quick_fecha_cita" class="font-weight-bold">Fecha de Cita: <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="quick_fecha_cita"
                                                name="fecha_cita" required value="{{ $selectedDate }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="font-weight-bold d-block">Tipo de Atención: <span
                                                    class="text-danger">*</span></label>
                                            <div class="btn-group btn-group-toggle w-100 chip-tipo-group"
                                                data-toggle="buttons">
                                                <label class="btn btn-outline-success chip-tipo-btn active flex-fill"
                                                    data-tipo="1">
                                                    <input type="radio" name="quick_tipo_radio" value="1"
                                                        autocomplete="off" checked>
                                                    <i class="bi bi-person-plus-fill mr-1"></i> <b>Nuevo</b>
                                                </label>
                                                <label class="btn btn-outline-primary chip-tipo-btn flex-fill"
                                                    data-tipo="2">
                                                    <input type="radio" name="quick_tipo_radio" value="2"
                                                        autocomplete="off">
                                                    <i class="bi bi-arrow-repeat mr-1"></i> <b>Control</b>
                                                </label>
                                                <label class="btn btn-outline-warning text-dark chip-tipo-btn flex-fill"
                                                    data-tipo="3">
                                                    <input type="radio" name="quick_tipo_radio" value="3"
                                                        autocomplete="off">
                                                    <i class="bi bi-person-check-fill mr-1"></i> <b>Continuador</b>
                                                </label>
                                            </div>
                                            <input type="hidden" name="id_tipo_atencion" id="quick_id_tipo_atencion"
                                                value="1">
                                            <small class="form-text text-muted">Por defecto: Nuevo</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right mt-3">
                                    <button type="button" class="btn btn-secondary mr-2"
                                        data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-success" id="btn_submit_quick">
                                        <i class="bi bi-person-check-fill"></i> Registrar Paciente y Agendar Cita
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: REAGENDAR CITA -->
    <div class="modal fade" id="modal_reschedule_appointment" tabindex="-1" role="dialog"
        aria-labelledby="modalRescheduleTitle" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="modalRescheduleTitle">
                        <i class="bi bi-arrow-repeat text-warning"></i> Reagendar Cita Médica
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form_reschedule_appointment">
                    <input type="hidden" id="reschedule_cita_id" value="">
                    <div class="modal-body p-4">
                        <div class="alert alert-light border mb-3">
                            <div class="small text-muted">Paciente:</div>
                            <div class="font-weight-bold text-dark h6 mb-1" id="reschedule_patient_name">--</div>
                            <div class="small text-muted">Fecha actual de la cita: <b id="reschedule_current_date">--</b>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reschedule_new_date" class="font-weight-bold">Nueva Fecha de Cita: <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="reschedule_new_date" required
                                min="{{ date('Y-m-d') }}">
                            <small class="form-text text-muted">Al reagendar, se le asignará el turno FIFO correspondiente
                                en la nueva fecha.</small>
                        </div>

                        <div class="form-group">
                            <label for="reschedule_new_time" class="font-weight-bold">Nueva Hora: <small
                                    class="text-muted">(Opcional)</small></label>
                            <input type="time" class="form-control" id="reschedule_new_time">
                        </div>

                        <div class="form-group mb-0">
                            <label for="reschedule_note" class="font-weight-bold">Motivo del cambio de fecha: <small
                                    class="text-muted">(Opcional)</small></label>
                            <textarea class="form-control" id="reschedule_note" rows="2"
                                placeholder="Ej. Solicitud del paciente, cruce de horarios, etc."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-warning" id="btn_submit_reschedule">
                            <i class="bi bi-arrow-repeat"></i> Confirmar Reagendamiento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: VISTA RÁPIDA DE HISTORIA CLÍNICA Y CONTROLES PREVIOS -->
    <div class="modal fade" id="modal_patient_quickview" tabindex="-1" role="dialog"
        aria-labelledby="modalPatientQuickviewTitle" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header -->
                <div class="modal-header bg-light py-3 border-bottom">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="bg-primary text-white rounded p-2 mr-3 d-flex align-items-center justify-content-center shadow-xs"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-file-earmark-person-fill" style="font-size: 1.3rem;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title font-weight-bold text-dark mb-0 d-inline-block"
                                id="modalPatientQuickviewTitle">
                                <span id="qv_patient_name">Cargando paciente...</span>
                            </h5>
                            <span class="badge badge-light border text-secondary ml-2 font-weight-normal py-1 px-2"
                                id="qv_patient_dni">
                                <i class="bi bi-card-text mr-1"></i>DNI: --
                            </span>
                            <span id="qv_patient_tipo_badge"></span>
                            <span class="badge badge-danger ml-1 font-weight-normal py-1 px-2"
                                id="qv_patient_deleted_badge" style="display: none;">
                                <i class="bi bi-archive-fill mr-1"></i>Historia Archivada
                            </span>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <!-- Loading Spinner State -->
                    <div id="qv_loading_state" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <h6 class="text-muted font-weight-bold mt-3">Cargando historia clínica y controles...</h6>
                        <p class="text-muted small">Recuperando registros históricos del paciente.</p>
                    </div>

                    <!-- Main Content (hidden while loading) -->
                    <div id="qv_content_state" style="display: none;">
                        <!-- Fila de Resumen Rápido de Filiación -->
                        <div class="p-3 mb-3" style="background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div class="row text-secondary small">
                                <div class="col-md-3 col-6 mb-2 mb-md-0">
                                    <div class="text-muted mb-1"><i class="bi bi-cake2 text-info mr-1"></i> Edad /
                                        Nacimiento:</div>
                                    <div class="font-weight-bold text-dark h6 mb-0">
                                        <span id="qv_patient_edad">--</span> años
                                        <small class="text-muted font-weight-normal">(<span
                                                id="qv_patient_fn">--</span>)</small>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mb-2 mb-md-0">
                                    <div class="text-muted mb-1"><i
                                            class="bi bi-gender-ambiguous text-secondary mr-1"></i> Sexo:</div>
                                    <div class="font-weight-bold text-dark mb-0" id="qv_patient_sexo">--</div>
                                </div>
                                <div class="col-md-3 col-6 mb-2 mb-md-0">
                                    <div class="text-muted mb-1"><i class="bi bi-telephone text-success mr-1"></i>
                                        Teléfono:</div>
                                    <div class="font-weight-bold text-dark mb-0" id="qv_patient_telefono">--</div>
                                </div>
                                <div class="col-md-2 col-6 mb-2 mb-md-0">
                                    <div class="text-muted mb-1"><i class="bi bi-droplet-half text-danger mr-1"></i> Gr.
                                        Sanguíneo:</div>
                                    <div class="font-weight-bold text-dark mb-0" id="qv_patient_gs">--</div>
                                </div>
                                <div class="col-md-2 col-12">
                                    <div class="text-muted mb-1"><i class="bi bi-briefcase text-primary mr-1"></i>
                                        Ocupación:</div>
                                    <div class="font-weight-bold text-dark text-truncate mb-0" id="qv_patient_ocupacion">
                                        --</div>
                                </div>
                            </div>
                        </div>

                        <!-- Pestañas de Navegación -->
                        <ul class="nav nav-tabs font-weight-bold mb-3" id="qvTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active py-2 px-3" id="tab-controles-link" data-toggle="tab"
                                    href="#tab-qv-controles" role="tab" aria-controls="tab-qv-controles"
                                    aria-selected="true">
                                    <i class="bi bi-clock-history text-primary mr-1"></i> Controles Clínicos Previos
                                    <span class="badge badge-primary ml-1" id="qv_appointments_count">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 px-3" id="tab-examenes-link" data-toggle="tab"
                                    href="#tab-qv-examenes" role="tab" aria-controls="tab-qv-examenes"
                                    aria-selected="false">
                                    <i class="bi bi-file-earmark-medical text-success mr-1"></i> Exámenes Clínicos
                                    <span class="badge badge-success ml-1" id="qv_exams_count">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 px-3" id="tab-antecedentes-link" data-toggle="tab"
                                    href="#tab-qv-antecedentes" role="tab" aria-controls="tab-qv-antecedentes"
                                    aria-selected="false">
                                    <i class="bi bi-clipboard2-pulse text-danger mr-1"></i> Antecedentes y Perfil Clínico
                                </a>
                            </li>
                        </ul>

                        <!-- Contenido de las Pestañas -->
                        <div class="tab-content" id="qvTabsContent">
                            <!-- PESTAÑA 1: CONTROLES CLÍNICOS PREVIOS -->
                            <div class="tab-pane fade show active" id="tab-qv-controles" role="tabpanel"
                                aria-labelledby="tab-controles-link">
                                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                    <div class="text-muted small">
                                        <i class="bi bi-info-circle text-info mr-1"></i> Listado ordenado del más reciente
                                        al más antiguo. Soporta pacientes inactivos hasta más de 5 años.
                                    </div>
                                    <div>
                                        <a href="#" id="qv_btn_add_control" target="_blank"
                                            class="btn btn-sm btn-outline-success font-weight-bold">
                                            <i class="bi bi-plus-circle mr-1"></i> Nuevo Control
                                        </a>
                                    </div>
                                </div>

                                <!-- Tabla de Controles -->
                                <div class="table-responsive border rounded bg-white shadow-xs">
                                    <table class="table table-hover align-middle mb-0" id="table_qv_appointments">
                                        <thead class="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th class="text-center" style="width: 45px;">#</th>
                                                <th style="width: 175px;">Fecha / Atención</th>
                                                <th style="width: 260px;">Diagnóstico(s)</th>
                                                <th>Síntomas / Motivo</th>
                                                <th>Plan / Tratamiento</th>
                                                <th class="text-center" style="width: 140px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="qv_appointments_tbody" class="small">
                                            <!-- Rendered dynamically -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Estado Vacío de Controles -->
                                <div id="qv_appointments_empty" class="text-center py-5 border rounded bg-white mt-2"
                                    style="display: none;">
                                    <i class="bi bi-clipboard-x text-muted" style="font-size: 2.5rem; opacity: 0.6;"></i>
                                    <h6 class="font-weight-bold text-dark mt-2 mb-1">No registra controles clínicos previos
                                    </h6>
                                    <p class="text-muted small mb-3">Este paciente no cuenta con atenciones o recetas
                                        registradas anteriormente en el sistema.</p>
                                    <a href="#" id="qv_btn_add_first_control" target="_blank"
                                        class="btn btn-sm btn-primary">
                                        <i class="bi bi-plus-circle mr-1"></i> Registrar Primer Control Clínico
                                    </a>
                                </div>
                            </div>

                            <!-- PESTAÑA 2: EXÁMENES CLÍNICOS -->
                            <div class="tab-pane fade" id="tab-qv-examenes" role="tabpanel"
                                aria-labelledby="tab-examenes-link">
                                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                    <div class="text-muted small">
                                        <i class="bi bi-info-circle text-info mr-1"></i> Exámenes activos registrados,
                                        ordenados del más reciente al más antiguo.
                                    </div>
                                    <div>
                                        <a href="#" id="qv_btn_add_new_exam" target="_blank"
                                            class="btn btn-sm btn-outline-success font-weight-bold">
                                            <i class="bi bi-plus-circle mr-1"></i> Nuevo Examen
                                        </a>
                                    </div>
                                </div>

                                <!-- Tabla de Exámenes -->
                                <div class="table-responsive border rounded bg-white shadow-xs">
                                    <table class="table table-hover align-middle mb-0" id="table_qv_exams">
                                        <thead class="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th class="text-center" style="width: 45px;">#</th>
                                                <th style="width: 175px;">Fecha / Registro</th>
                                                <th style="width: 120px;">Tipo</th>
                                                <th>Diagnóstico(s)</th>
                                                <th>Plan / Observaciones</th>
                                                <th class="text-center" style="width: 140px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="qv_exams_tbody" class="small">
                                            <!-- Rendered dynamically -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Estado Vacío de Exámenes -->
                                <div id="qv_exams_empty" class="text-center py-5 border rounded bg-white mt-2"
                                    style="display: none;">
                                    <i class="bi bi-file-earmark-x text-muted"
                                        style="font-size: 2.5rem; opacity: 0.6;"></i>
                                    <h6 class="font-weight-bold text-dark mt-2 mb-1">No registra exámenes clínicos</h6>
                                    <p class="text-muted small mb-3">Este paciente no cuenta con exámenes registrados
                                        en el sistema.</p>
                                    <a href="#" id="qv_btn_add_first_exam" target="_blank"
                                        class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle mr-1"></i> Registrar Primer Examen
                                    </a>
                                </div>
                            </div>

                            <!-- PESTAÑA 3: ANTECEDENTES Y PERFIL CLÍNICO -->
                            <div class="tab-pane fade" id="tab-qv-antecedentes" role="tabpanel"
                                aria-labelledby="tab-antecedentes-link">
                                <div class="row">
                                    <!-- Antecedentes Patológicos Respiratorios -->
                                    <div class="col-md-6 col-12 mb-3">
                                        <div class="card card-outline card-info h-100 shadow-xs mb-0">
                                            <div class="card-header py-2">
                                                <h6 class="card-title font-weight-bold text-dark mb-0">
                                                    Patología Respiratoria y Pulmonar
                                                </h6>
                                            </div>
                                            <div class="card-body p-3 small">
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Asma Bronquial:</div>
                                                    <div class="col-6 font-weight-bold text-dark" id="qv_ant_asma">--
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">EPOC:</div>
                                                    <div class="col-6 font-weight-bold text-dark" id="qv_ant_epoc">--
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Tuberculosis (TBC):</div>
                                                    <div class="col-6 font-weight-bold text-dark" id="qv_ant_tbc">--</div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Cáncer de Pulmón:</div>
                                                    <div class="col-6 font-weight-bold text-dark" id="qv_ant_cancer">--
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Neumonías previas:</div>
                                                    <div class="col-6 font-weight-bold text-dark" id="qv_ant_neumonias">--
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6 text-muted">Efusión Pleural:</div>
                                                    <div class="col-6 font-weight-bold text-dark" id="qv_ant_efusion">--
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Exposición y Factores de Riesgo -->
                                    <div class="col-md-6 col-12 mb-3">
                                        <div class="card card-outline card-warning h-100 shadow-xs mb-0">
                                            <div class="card-header py-2">
                                                <h6 class="card-title font-weight-bold text-dark mb-0">
                                                    Tabaquismo y Factores de Exposición
                                                </h6>
                                            </div>
                                            <div class="card-body p-3 small">
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Condición de Tabaquismo:</div>
                                                    <div class="col-6 font-weight-bold text-dark" id="qv_ant_tabaco">--
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Índice Paquetes-Año (IPA):</div>
                                                    <div class="col-6 font-weight-bold text-dark" id="qv_ant_ipa">--</div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Contacto TBC:</div>
                                                    <div class="col-6 font-weight-bold text-dark" id="qv_ant_contactotbc">
                                                        --</div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-6 text-muted">Exposición a Biomasa (humo de leña):
                                                    </div>
                                                    <div class="col-6 font-weight-bold text-dark" id="qv_ant_biomasa">--
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6 text-muted">Alergias / Reacción a Drogas:</div>
                                                    <div class="col-6 font-weight-bold text-danger" id="qv_ant_drogas">--
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Antecedentes Quirúrgicos, Hospitalizaciones y Otros -->
                                    <div class="col-12 mb-3">
                                        <div class="card card-outline card-secondary shadow-xs mb-0">
                                            <div class="card-header py-2">
                                                <h6 class="card-title font-weight-bold text-dark mb-0">
                                                    Hospitalizaciones, Cirugías y Medicación Habitual
                                                </h6>
                                            </div>
                                            <div class="card-body p-3 small">
                                                <div class="row">
                                                    <div class="col-md-6 col-12 mb-2">
                                                        <div class="text-muted font-weight-bold mb-1">Cirugías previas:
                                                        </div>
                                                        <div class="text-dark border rounded p-2 bg-light"
                                                            id="qv_ant_cirugias">Ninguna</div>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-2">
                                                        <div class="text-muted font-weight-bold mb-1">Hospitalizaciones:
                                                        </div>
                                                        <div class="text-dark border rounded p-2 bg-light"
                                                            id="qv_ant_hospitalizaciones">Ninguna</div>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-2">
                                                        <div class="text-muted font-weight-bold mb-1">Medicación habitual:
                                                        </div>
                                                        <div class="text-dark border rounded p-2 bg-light"
                                                            id="qv_ant_medicacion">Ninguna</div>
                                                    </div>
                                                    <div class="col-md-6 col-12 mb-2">
                                                        <div class="text-muted font-weight-bold mb-1">Otros antecedentes /
                                                            Transfusiones:</div>
                                                        <div class="text-dark border rounded p-2 bg-light"
                                                            id="qv_ant_otros">--</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Motivo de Consulta y Relato Cronológico original -->
                                    <div class="col-12">
                                        <div class="card card-outline card-primary shadow-xs mb-0">
                                            <div class="card-header py-2">
                                                <h6 class="card-title font-weight-bold text-dark mb-0">
                                                    Motivo de Consulta y Relato Cronológico Inicial
                                                </h6>
                                            </div>
                                            <div class="card-body p-3 small">
                                                <div class="mb-2">
                                                    <div class="text-muted font-weight-bold mb-1">Motivo de consulta:</div>
                                                    <div class="text-dark font-italic" id="qv_ant_motivo">--</div>
                                                </div>
                                                <div>
                                                    <div class="text-muted font-weight-bold mb-1">Relato cronológico:</div>
                                                    <div class="text-dark" id="qv_ant_relato">--</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light py-2 justify-content-between">
                    <div>
                        <a href="#" id="qv_btn_edit_history" class="btn btn-primary mr-2" target="_blank">
                            Ver / Editar Historia Completa
                        </a>
                        <a href="#" id="qv_btn_new_exam" class="btn btn-outline-secondary mr-2" target="_blank">
                            <i class="bi bi-activity mr-1"></i> Nuevo Examen
                        </a>
                        <a href="#" id="qv_btn_new_report" class="btn btn-outline-secondary" target="_blank">
                            <i class="bi bi-file-earmark-text mr-1"></i> Nuevo Informe
                        </a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="bi bi-x-circle mr-1"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/forms/citas.js') }}"></script>
@endsection
