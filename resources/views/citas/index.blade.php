@extends('layouts.app')
@section('title', config('global.site_name', 'NeumoTar') . ' - Citas y Turnos')

@section('content')
<style>
    /* Estilos limpios y planos - Sin colores degradados */
    .citas-stat-card {
        border-radius: 8px;
        padding: 18px 20px;
        color: #ffffff;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.15s ease;
    }
    .citas-stat-card:hover {
        transform: translateY(-2px);
    }
    .stat-bg-total { background-color: #2563eb; }      /* Azul sólido */
    .stat-bg-espera { background-color: #0284c7; }     /* Celeste sólido */
    .stat-bg-atendidos { background-color: #16a34a; }  /* Verde sólido */
    .stat-bg-pendientes { background-color: #d97706; } /* Ámbar sólido */
    
    .citas-stat-card .stat-value {
        font-size: 2.1rem;
        font-weight: 700;
        line-height: 1.1;
    }
    .citas-stat-card .stat-label {
        font-size: 0.95rem;
        font-weight: 500;
        opacity: 0.95;
    }
    .citas-stat-card .stat-icon {
        font-size: 2.5rem;
        opacity: 0.35;
    }

    .date-navigation-bar {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 18px;
        margin-bottom: 20px;
    }

    .badge-turno {
        background-color: #1e293b;
        color: #ffffff;
        font-size: 0.9rem;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 6px;
    }

    .patient-search-result-item {
        cursor: pointer;
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.15s ease;
    }
    .patient-search-result-item:hover {
        background-color: #eff6ff;
    }

    .selected-patient-card {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 14px;
        margin-bottom: 15px;
    }

    /* Modales limpios con bordes planos */
    .modal-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .nav-tabs .nav-link.active {
        font-weight: 600;
        border-bottom: 3px solid #2563eb !important;
        color: #2563eb !important;
    }
</style>

<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        <i class="bi bi-calendar2-check text-primary"></i> Módulo de Citas y Turnos
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Citas y Turnos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Indicadores Estadísticos del Día Seleccionado (Colores Planos) -->
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

            <!-- Barra de Selección de Fecha y Filtros (Priorizando el Día de Hoy) -->
            <div class="date-navigation-bar">
                <div class="row align-items-center">
                    <div class="col-md-5 col-12 mb-2 mb-md-0">
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="btn-group mr-2 mb-1" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_prev_day" title="Día anterior">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" id="btn_today">
                                    <i class="bi bi-calendar-check"></i> Hoy
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_tomorrow">
                                    Mañana
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_next_day" title="Día siguiente">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                            <div class="input-group input-group-sm mb-1" style="width: 170px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="bi bi-calendar3"></i></span>
                                </div>
                                <input type="date" class="form-control" id="selected_date" value="{{ $selectedDate }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-12 mb-2 mb-md-0">
                        <div class="d-flex align-items-center">
                            <label for="filter_status" class="mr-2 mb-0 text-muted font-weight-normal text-nowrap">Estado:</label>
                            <select id="filter_status" class="form-control form-control-sm">
                                <option value="">Todos los estados</option>
                                @foreach($statuses as $st)
                                    <option value="{{ $st->id }}">{{ $st->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 col-12 text-md-right text-left">
                        <button type="button" class="btn btn-success" id="btn_open_schedule_modal">
                            <i class="bi bi-plus-circle"></i> Agendar Cita
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card Principal de la Lista de Citas (Orden FIFO) -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title m-0 text-dark">
                        <i class="bi bi-list-ol text-primary"></i> Cola de Citas y Turnos
                        <span class="badge badge-light border ml-2" id="label_current_date_display"></span>
                    </h5>
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_refresh_table" title="Actualizar lista">
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
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <div class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div>
                                        Cargando lista de turnos...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-2">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> <b>Criterio FIFO (First-In, First-Out):</b> Los pacientes son organizados estrictamente en orden correlativo de llegada y agendamiento para garantizar una atención ordenada y justa.
                    </small>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- MODAL: AGENDAR CITA (BÚSQUEDA Y REGISTRO RÁPIDO DE PACIENTES)    -->
<div class="modal fade" id="modal_schedule_appointment" tabindex="-1" role="dialog" aria-labelledby="modalScheduleTitle" aria-hidden="true" data-backdrop="static">
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
                        <a class="nav-link active py-3" id="tab-search-link" data-toggle="tab" href="#tab-search-patient" role="tab" aria-controls="tab-search-patient" aria-selected="true">
                            <i class="bi bi-search"></i> Buscar Paciente Existente
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3" id="tab-quick-link" data-toggle="tab" href="#tab-quick-patient" role="tab" aria-controls="tab-quick-patient" aria-selected="false">
                            <i class="bi bi-person-plus-fill"></i> Registro Rápido de Nuevo Paciente
                        </a>
                    </li>
                </ul>

                <div class="tab-content p-4" id="appointmentModalTabContent">
                    <!-- PESTAÑA 1: BUSCAR PACIENTE EXISTENTE -->
                    <div class="tab-pane fade show active" id="tab-search-patient" role="tabpanel" aria-labelledby="tab-search-link">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                Buscar Paciente en Base de Datos:
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                </div>
                                <input type="text" class="form-control" id="search_patient_input" placeholder="Escriba DNI o Apellidos y Nombres del paciente..." autocomplete="off">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="btn_clear_search_patient">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Ingrese al menos 2 caracteres para buscar en el registro histórico de pacientes.</small>
                        </div>

                        <!-- Lista de Resultados de Búsqueda -->
                        <div id="patient_search_results" class="border rounded bg-white mb-3" style="max-height: 190px; overflow-y: auto; display: none;">
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
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="btn_deselect_patient" title="Cambiar paciente">
                                        <i class="bi bi-x-circle"></i> Cambiar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Aviso de paciente no encontrado -->
                        <div class="alert alert-light border mb-3 py-2 text-center" id="box_not_found_prompt">
                            <span class="text-muted">¿No encuentra al paciente registrado?</span>
                            <a href="javascript:void(0)" class="font-weight-bold text-primary ml-1" id="btn_switch_to_quick_patient">
                                <i class="bi bi-person-plus"></i> Registrar nuevo paciente aquí
                            </a>
                        </div>

                        <!-- Formulario de Agendamiento para Paciente Seleccionado -->
                        <form id="form_schedule_existing">
                            <input type="hidden" id="existing_patient_id" name="id_historia" value="">

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="existing_fecha_cita" class="font-weight-bold">Fecha de Cita: <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="existing_fecha_cita" name="fecha_cita" required value="{{ $selectedDate }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="existing_observaciones" class="font-weight-bold">Observaciones adicionales: <small class="text-muted">(Opcional)</small></label>
                                <textarea class="form-control" id="existing_observaciones" name="observaciones" rows="2" placeholder="Notas adicionales para la recepción o el médico..."></textarea>
                            </div>

                            <div class="text-right">
                                <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary" id="btn_submit_existing" disabled>
                                    <i class="bi bi-calendar-check"></i> Agendar Cita
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- PESTAÑA 2: REGISTRO RÁPIDO DE PACIENTE -->
                    <div class="tab-pane fade" id="tab-quick-patient" role="tabpanel" aria-labelledby="tab-quick-link">
                        <form id="form_quick_patient">
                            <div class="alert alert-info py-2 small mb-3 border-0">
                                <i class="bi bi-info-circle-fill"></i> Ingrese el DNI y presione <b>Buscar RENIEC</b> para completar automáticamente los nombres del paciente.
                            </div>

                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="quick_id_td" class="font-weight-bold">Tipo Documento: <span class="text-danger">*</span></label>
                                        <select class="form-control" id="quick_id_td" name="id_td" required>
                                            @foreach($documentTypes as $td)
                                                <option value="{{ $td->id }}" {{ $td->id == 1 ? 'selected' : '' }}>{{ $td->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8 col-12">
                                    <div class="form-group">
                                        <label for="quick_dni" class="font-weight-bold">Número de Documento / DNI: <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="quick_dni" name="dni" placeholder="Ingrese DNI (8 dígitos)" maxlength="8" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-info" type="button" id="btn_quick_search_reniec">
                                                    <span id="reniec_spinner" class="spinner-border spinner-border-sm mr-1 d-none" role="status"></span>
                                                    <i class="bi bi-search" id="reniec_icon"></i> Buscar RENIEC
                                                </button>
                                            </div>
                                        </div>
                                        <div id="quick_dni_feedback" class="text-danger small mt-1" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="quick_nombres" class="font-weight-bold">Nombres y Apellidos Completos: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="quick_nombres" name="nombres" placeholder="APELLIDOS Y NOMBRES" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="quick_fecha_nacimiento" class="font-weight-bold">Fecha de Nacimiento: <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="quick_fecha_nacimiento" name="fecha_nacimiento" required max="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="quick_id_sexo" class="font-weight-bold">Sexo: <span class="text-danger">*</span></label>
                                        <select class="form-control" id="quick_id_sexo" name="id_sexo" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($sexes as $sx)
                                                <option value="{{ $sx->id }}">{{ $sx->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="quick_telefono" class="font-weight-bold">Teléfono / Celular: <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="quick_telefono" name="telefono" placeholder="Número de contacto (9 dígitos)" maxlength="11" required>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">
                            <h6 class="font-weight-bold text-dark mb-3"><i class="bi bi-calendar2-event text-primary"></i> Datos de la Cita Inicial</h6>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="quick_fecha_cita" class="font-weight-bold">Fecha de Cita: <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="quick_fecha_cita" name="fecha_cita" required value="{{ $selectedDate }}">
                                    </div>
                                </div>
                            </div>
                            <div class="text-right mt-3">
                                <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Cancelar</button>
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
<div class="modal fade" id="modal_reschedule_appointment" tabindex="-1" role="dialog" aria-labelledby="modalRescheduleTitle" aria-hidden="true" data-backdrop="static">
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
                        <div class="small text-muted">Fecha actual de la cita: <b id="reschedule_current_date">--</b></div>
                    </div>

                    <div class="form-group">
                        <label for="reschedule_new_date" class="font-weight-bold">Nueva Fecha de Cita: <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="reschedule_new_date" required min="{{ date('Y-m-d') }}">
                        <small class="form-text text-muted">Al reagendar, se le asignará el turno FIFO correspondiente en la nueva fecha.</small>
                    </div>

                    <div class="form-group">
                        <label for="reschedule_new_time" class="font-weight-bold">Nueva Hora: <small class="text-muted">(Opcional)</small></label>
                        <input type="time" class="form-control" id="reschedule_new_time">
                    </div>

                    <div class="form-group mb-0">
                        <label for="reschedule_note" class="font-weight-bold">Motivo del cambio de fecha: <small class="text-muted">(Opcional)</small></label>
                        <textarea class="form-control" id="reschedule_note" rows="2" placeholder="Ej. Solicitud del paciente, cruce de horarios, etc."></textarea>
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

<script src="{{ asset('js/forms/citas.js') }}"></script>
@endsection
