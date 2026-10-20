# Neumo-LAV / Neumotar

> **Plataforma Integral de Gestión de Historias Clínicas Electrónicas (HCL), Control de Citas y Turnos en Sala de Espera, Diagnósticos Neumológicos Especializados, Salud Ocupacional, Prescripción Farmacológica Dual (A4/A5) y Analítica Epidemiológica.**

[![Laravel 10](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8.1+](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Vue 3](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white)](https://vuejs.org)
[![Pinia](https://img.shields.io/badge/Pinia-4.0-FFE564?style=for-the-badge&logo=pinia&logoColor=black)](https://pinia.vuejs.org)
[![Vite](https://img.shields.io/badge/Vite-5.0-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![MySQL 8](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)
[![AdminLTE 3](https://img.shields.io/badge/AdminLTE-3.2-1E88E5?style=for-the-badge)](https://adminlte.io)
[![Bootstrap 4](https://img.shields.io/badge/Bootstrap-4.6-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![Version](https://img.shields.io/badge/Version-v3.3.0-0ea5e9?style=for-the-badge)](README.md)
[![License](https://img.shields.io/badge/License-Proprietary-blue?style=for-the-badge)]()

---

## Tabla de Contenidos

- [1. Descripción del Proyecto](#1-descripción-del-proyecto)
- [2. Capacidades y Características Clínicas](#2-capacidades-y-características-clínicas)
- [3. Arquitectura del Sistema](#3-arquitectura-del-sistema)
- [4. Estructura de Controladores y Módulos](#4-estructura-de-controladores-y-módulos)
- [5. Flujo Operativo y Ciclo de Vida del Paciente](#5-flujo-operativo-y-ciclo-de-vida-del-paciente)
- [6. Seguridad, RBAC, Credenciales Médicas y Auditoría](#6-seguridad-rbac-credenciales-médicas-y-auditoría)
- [7. Base de Datos: Modelos, Procedimientos Almacenados y Vistas](#7-base-de-datos-modelos-procedimientos-almacenados-y-vistas)
- [8. Servicios Especializados e Integraciones Externas](#8-servicios-especializados-e-integraciones-externas)
- [9. Requisitos del Entorno](#9-requisitos-del-entorno)
- [10. Guía de Instalación y Puesta en Marcha](#10-guía-de-instalación-y-puesta-en-marcha)
- [11. Usuarios y Roles Preconfigurados](#11-usuarios-y-roles-preconfigurados)
- [12. Copias de Seguridad y Tareas Programadas](#12-copias-de-seguridad-y-tareas-programadas)
- [13. Documento de Definición de Producto (PDR)](#13-documento-de-definición-de-producto-pdr)
- [14. Historial de Versiones y Release Notes (v3.3.0)](#14-historial-de-versiones-y-release-notes-v330)

---

## 1. Descripción del Proyecto

**Neumo-LAV** (denominado comercialmente en la atención de pacientes como **Neumotar**) es una solución informática médica de nivel empresarial diseñada específicamente para digitalizar, optimizar y auditar la gestión asistencial y administrativa de centros especializados en **Neumología, Medicina Respiratoria y Salud Ocupacional**. Concebido para la empresa **Medytarq SAC** en Tarapoto (San Martín, Perú), el sistema erradica el uso del papel mediante un ecosistema digital seguro, rápido y conforme a los estándares sanitarios del Ministerio de Salud (MINSA).

### Objetivos Clave y Alcance

1. **Filiación Asistida e Identidad Validada:** Conexión sincrónica con la base de datos de RENIEC (vía API REST Decolecta) para validación infalible del DNI y codificación geográfica normalizada según el estándar de Ubigeo nacional (1,874 distritos).
2. **Gestión de Flujo y Turnos en Espera (FIFO):** Tablero operativo en tiempo real en la página principal (`/admin/home`) con asignación secuencial de turnos, semaforización de estados (`En Espera`, `Atendido`, `Cancelado`, `Reagendado`) y categorización por tipo de atención (`Nuevo`, `Control`, `Continuador`).
3. **Registro Rápido de Pacientes (`quickPatient`):** Admisión express en una única transacción atómica para pacientes que requieren atención inmediata en mostrador.
4. **Evaluación de Factores de Riesgo Pulmonar:** Cálculo automático del **Índice Paquete Año (IPA)** para tabaquismo, detección de exposición a humo de biomasa/leña (propio de la Amazonía peruana) y trazabilidad de contacto con Tuberculosis (TBC) y COVID-19.
5. **Consultas Médicas y Recetas Duales (A4 / A5):** Formulación posológica de medicamentos vinculada a diagnósticos **CIE-10**, con generación e impresión instantánea de recetas médicas en formato institucional **A4** o recetario de bolsillo **A5** con membrete, logotipo y firma digitalizada del facultativo.
6. **Apoyo al Diagnóstico, Espirometría e Imagenología:** Registro de parámetros de función pulmonar (FEV1, FVC, FEV1/FVC basal y post-broncodilatador), almacenamiento multimedia de imágenes diagnósticas (radiografías de tórax, tomografías axiales computarizadas) y visor de imágenes integrado.
7. **Informes Médicos y Riesgo Quirúrgico:** Emisión estructurada de dictámenes clínicos oficiales y evaluaciones de riesgo quirúrgico neumológico/cardiovascular preoperatorio con clasificación ASA e índices predictivos.
8. **Analítica Epidemiológica y Drill-Down Interactivo:** Tableros estadísticos interactivos desarrollados con Highcharts y Vue 3 que visualizan morbilidad respiratoria, Top 15 de diagnósticos, Top 15 de medicamentos prescritos, métricas sociodemográficas y desglose mensual interactivo con un clic en el reporte anual de producción.
9. **UI Reactiva Híbrida (Vue 3 + Pinia + Vite):** Modernización integral de la interfaz con Single File Components (`.vue`), 30 stores reactivos de Pinia, componentes Select2 asíncronos (`SearchSelect.vue`) con limitación inteligente de resultados para evitar sobrecarga del servidor, modales clínicos de inspección inmediata y paginación de cliente (10 registros por página).
10. **Portal Web y Reservas Omnicanal:** Portal web público con catálogo de servicios, blog divulgativo y solicitud de citas en línea con confirmación vía **WhatsApp Cloud API** y correo electrónico transaccional.

---

## 2. Capacidades y Características Clínicas

### 2.1 Módulo Operativo de Citas y Turnos (`CitasController`)

- **Control de Sala de Espera del Día:** Visualización cronológica y por turno (FIFO) de los pacientes agendados para la fecha seleccionada.
- **Métricas Instantáneas del Día:** Contadores dinámicos de total de citas, pacientes en espera física en sala, atendidos, pendientes y cancelados.
- **Tipos de Atención:** Distinción visual mediante badges interactivos:
  - <span style="color:#28a745; font-weight:bold;">Nuevo (Verde):</span> Primera consulta del paciente en el centro.
  - <span style="color:#007bff; font-weight:bold;">Control (Azul):</span> Cita de seguimiento o revisión de tratamiento.
  - <span style="color:#ffc107; font-weight:bold;">Continuador (Amarillo):</span> Paciente recurrente con tratamiento crónico en curso.
  - Cambio inmediato de tipo de atención con un clic desde la tabla de citas, sincronizando el estado tanto en la cita como en la historia clínica.
- **Reagendamiento Seguro:** Reprogramación de fecha y hora validando que el paciente no posea ya otra cita activa en el día destino, recalculando su turno de forma secuencial.
- **Acciones Directas desde Mostrador:** Menú contextual para abrir la historia, iniciar un nuevo control, registrar un examen auxiliar, emitir un informe o evaluar riesgo quirúrgico.
- **QuickView Modal Optimizado:** Modal reactivo de resumen rápido en `/admin/home` con pestañas de filiación, antecedentes, controles previos y exámenes auxiliares; incluye botones de acción de alta legibilidad y contraste ("Receta", "Ver Examen" e "Imprimir") con apertura directa de modales clínicos.

### 2.2 Historia Clínica Neumológica (HCL) (`HistoriesController`)

- **Validación RENIEC en Vivo:** Autocompletado de nombres completos a partir del número de DNI.
- **Cálculo Automatizado de Carga Tabáquica (IPA):**
  $$
  \text{IPA} = \frac{\text{Cigarrillos al día} \times \text{Años fumando}}{20}
  $$
- **Riesgo Ambiental Regional:** Exposición a humo de leña/biomasa en cocina tradicional y antecedentes de tuberculosis pulmonar (contacto intradomiciliario TBC).
- **Antecedentes Mórbidos:** Asma bronquial, EPOC, EPID, neoplasias pulmonares, efusión pleural y neumonías previas.
- **Resumen Clínico Rápido (`quickView`):** Modal reactivo que extrae en una sola llamada el resumen demográfico del paciente, sus últimas funciones vitales y su historial reciente de exámenes y controles.

### 2.3 Controles Médicos y Prescripción Farmacológica (`AppointmentsController`)

- **Anamnesis Evolutiva:** Tiempo de enfermedad, funciones vitales y relato cronológico de síntomas.
- **Diagnóstico Multivariado CIE-10:** Selección asistida por autocompletado de uno o varios diagnósticos por consulta.
- **Prescripción Posológica Detallada:** Selección de fármacos por principio activo, presentación y concentración con campos individuales para dosis, vía de administración, frecuencia y duración.
- **Vista de Historial Clínico de Controles (`/admin/appointments/see/{id}`):**
  - Tabla reactiva Vue 3 con **paginación en cliente de 10 registros por página** (`useClientPagination.js`).
  - Modal especializado de recetas (`PrescriptionDetailModal.vue`) que despliega la relación completa de diagnósticos CIE-10 asignados y la posología farmacológica detallada sin abandonar la vista.
- **Impresión Dual en PDF:** Formato formal **A4** para el archivo clínico y formato **A5** (medio pliego) para recetario entregable al paciente, con membrete corporativo y firma digitalizada del médico especialista.

### 2.4 Exámenes Auxiliares e Imagenología (`ExamsController`)

- **Espirometría y Función Pulmonar:** Registro de curvas basales y post-broncodilatador con parámetros FEV1, FVC y ratio FEV1/FVC.
- **Paneles de Laboratorio y Fluidos:** Hemograma, bioquímica, perfil hepático, coagulación, reumatología, orina, esputo y análisis citoquímico de líquido pleural (ADA, PAP, cultivos bacteriológicos, BK directo).
- **Vista de Historial de Exámenes (`/admin/exams/see/{id}`):**
  - Tabla reactiva con **paginación de 10 registros por página**.
  - Modal interactivo de detalle de examen (`ExamDetailModal.vue`) para visualizar diagnósticos, medicación aplicada e imágenes diagnósticas adjuntas.
- **Almacenamiento y Visor de Imágenes:** Carga de radiografías, tomografías y curvas gráficas asociadas al examen, con previsualización en ventana modal.

### 2.5 Informes Médicos e Informes de Riesgo Quirúrgico

- **Informes Clínicos (`ReportsController`):** Documentos médico-legales certificados para instituciones laborales, aseguradoras o referencias hospitalarias.
  - Vista de historial (`/admin/reports/see/{id}`) con paginación de 10 registros por página y modal de vista previa rápida (`ReportPreviewModal.vue`).
- **Riesgo Quirúrgico (`RisksController`):** Valoración preoperatoria neumológica y cardiovascular con clasificación de riesgo anestésico y recomendaciones perioperatorias en PDF.
  - Vista de historial (`/admin/risks/see/{id}`) con paginación de 10 registros por página y modal de vista previa (`RiskPreviewModal.vue`).

### 2.6 Analítica Epidemiológica y Dashboard Interactivo (`StatisticsController`)

- **Reporte Anual de Producción:** Comparativo mensual de historias clínicas, exámenes auxiliares y citas de control mediante Highcharts.
- **Drill-Down Mensual Dinámico:** Al hacer clic sobre cualquiera de las barras del gráfico anual (ej. "Historias", "Exámenes" o "Controles" de un mes específico), el gráfico conmuta automáticamente a una vista mensual detallada de dicha categoría, con un botón armonizado y estilizado de "Volver al Reporte Anual".
- **Top 15 Epidemiológico:** Gráficos de barras horizontales con los 15 diagnósticos más comunes y los 15 medicamentos más recetados.

### 2.7 Componente Select2 Asíncrono y Filtros de Cabecera Estandarizados

- **Componente `SearchSelect.vue`:**
  - Selector Select2 reactivo para Vue 3 con autocompletado en vivo, debounce en escritura, resaltado de texto coincidente y navegación completa por teclado (flechas y Enter).
  - Admite variantes de tamaño (`size="sm"` de 31px de altura para cabeceras y `size="md"` para formularios generales).
- **Filtro de Especialidades (`/admin/specialties`):**
  - Sustitución de select estático por `SearchSelect.vue` conectado al endpoint asíncrono `/admin/specialties/search`.
  - **Limitación estricta a 5 resultados (`->limit(5)`):** Evita la sobrecarga de consultas y memoria en el servidor al filtrar grandes volúmenes de especialidades.
  - Filtrado instantáneo de la tabla por `specialty_id` y reseteo suave del estado.
- **Cabeceras de Filtros Unificadas:** Diseño estético y coherente entre el grupo de búsqueda y los selectores en todos los módulos administrativos (`SpecialtiesFilters`, `AppointmentsFilters`, etc.).

### 2.8 Inteligencia Artificial Aplicada a Contenidos (`SummaryService`)

- Motor de resumen inteligente con fallback automático entre las APIs de **DeepSeek** y **Kimi AI**, orientado a sintetizar contenidos científicos y artículos de divulgación respiratoria.


---

## 3. Arquitectura del Sistema

El sistema implementa una arquitectura híbrida que combina el patrón **Modelo-Vista-Controlador (MVC)** desacoplado en el backend (Laravel 10) con una capa frontend reactiva moderna basada en **Vue 3 (Composition API / `<script setup>`)**, **Pinia** y **Vite 5**:

```
[Navegador / Terminal Consultorio / Recepción / Portal Web]
                             │
                             ▼
                 [Servidor Web Nginx / Apache]
                             │
                             ▼
  [Pipeline de Middleware: Autenticación, Rate Limiting, Anti-Caché, Auditoría]
                             │
                             ▼
                   [Capa de Controladores]
      ┌──────────────────────┬──────────────────────┬──────────────────────┐
      │                      │                      │                      │
[Citas & Recepción]    [Módulo Clínico]      [Mantenimiento & CMS]    [Seguridad & RBAC]
CitasController        HistoriesController   DrugsController          UsersController
HomeController         AppointmentsController CategoriesController    RolesController
                       ExamsController       DiagnosticsController    PermissionController
                       ReportsController     OccupationsController    SpecialtiesController
                       RisksController       EnterpriseController     AuthController
                       StatisticsController  PostsController          HomePageController
      └──────────────────────┼──────────────────────┴──────────────────────┘
                             │
                             ▼
           [Capa de Presentación y Frontend Reactivo]
      ├─► Vistas Blade maestras (AdminLTE 3 / Bootstrap 4)
      ├─► Componentes Reactivos Vue 3 (.vue) montados por módulo (Vite 5)
      ├─► Gestión de Estado con Pinia (30 Stores reactivos independientes)
      ├─► Componente Select2 Asíncrono (SearchSelect.vue con limitación de 5 resultados)
      ├─► Composables especializados: useClientPagination (10 registros/pág), useHighcharts
      └─► Modales clínicos integrados: PrescriptionDetailModal, ExamDetailModal, QuickViewModal
                             │
                             ▼
                 [Capa de Servicios y Negocio]
      ├─► [SummaryService: Integración IA DeepSeek / Kimi]
      ├─► [TableViewService: Renderizado Dinámico de Tablas]
      ├─► [DomPDF Engine: Emisión de Recetas A4 / A5 con Firma Digital]
      ├─► [RENIEC API: Decolecta REST Client sincrónico]
      └─► [Mail & WhatsApp API: Notificaciones Transaccionales]
                             │
                             ▼
            [Capa de Persistencia Eloquent: 36 Modelos]
                             │
                             ▼
       [Base de Datos Relacional: MySQL 8.0+ / MariaDB 10.5+]
      ├─► 18 Procedimientos Almacenados (PA_*)
      ├─► 7 Vistas SQL Optimizadas (view_*, vista_*)
      └─► Auditoría Transaccional Automática (audit_logs)
```

---

## 4. Estructura de Controladores y Módulos

El código fuente de la aplicación se organiza bajo una estructura modular clara:

```
app/Http/Controllers/
├── ApplicationController.php              # Controlador base de aplicación
├── Controller.php                         # Clase abstracta base de Laravel
├── HomeController.php                     # Dashboard inicial y métricas generales
├── CitasController.php                    # Control de citas, turnos FIFO, tipos de atención y sala de espera
├── auth/
│   └── AuthController.php                 # Autenticación, Rate Limiting, sesión y prevención de caché
├── business/
│   ├── EnterpriseController.php           # Configuración institucional, geolocalización y 4 activos gráficos
│   └── PostsController.php                # CMS de divulgación respiratoria y artículos médicos
├── hcl/
│   ├── AppointmentsController.php         # Consultas de control, recetas médicas y PDFs A4/A5
│   ├── ExamsController.php                # Pruebas de función pulmonar, espirometría e imágenes médicas
│   ├── HistoriesController.php            # Historias clínicas, RENIEC DNI, ubigeos y modal quickView
│   ├── ReportsController.php              # Emisión y descarga de informes clínicos oficiales en PDF
│   ├── RisksController.php                # Evaluaciones de riesgo quirúrgico neumológico en PDF
│   └── StatisticsController.php           # Analítica epidemiológica y gráficos Highcharts
├── maintenance/
│   ├── CategoriesController.php           # Categorías y familias farmacológicas
│   ├── DiagnosticsController.php          # Catálogo maestro de patologías CIE-10
│   ├── DrugsController.php                # Vademécum activo de fármacos y principios activos
│   ├── OccupationsController.php          # Catálogo de ocupaciones y oficios CIUO
│   └── PresentationsController.php        # Formas y presentaciones farmacéuticas
├── security/
│   ├── ConfigController.php               # Configuración global del sistema
│   ├── PermissionController.php           # Catálogo de permisos del sistema
│   ├── RolesController.php                # Roles institucionales y sincronización dual-panel de permisos
│   ├── SpecialtiesController.php          # Especialidades médicas de los profesionales
│   └── UsersController.php                # Cuentas de usuario, reseteo de claves y perfil con CMP/RNE/firma
└── web/
    └── HomePageController.php             # Portal web público, artículos, contacto y reservas online
```

---

## 5. Flujo Operativo y Ciclo de Vida del Paciente

```mermaid
sequenceDiagram
    autonumber
    actor Paciente as Paciente
    actor Recepcion as Asistente / Recepción
    actor Medico as Médico Especialista
    participant Sistema as Plataforma Neumotar
    participant Reniec as API RENIEC
    participant BD as Base de Datos MySQL
    participant PDF as Motor DomPDF

    Note over Paciente,Recepcion: 1. Admisión o Llegada a Consulta
    Paciente->>Recepcion: Acude a consulta o solicita turno
    alt Paciente Nuevo
        Recepcion->>Sistema: Ingresa DNI en Registro Rápido (quickPatient)
        Sistema->>Reniec: Valida DNI de forma sincrónica
        Reniec-->>Sistema: Retorna Nombres y Apellidos oficiales
        Recepcion->>Sistema: Asigna Tipo de Atención: Nuevo (Badge Verde)
        Sistema->>BD: Crea Historia y Cita con Turno Secuencial #X (FIFO)
    else Paciente Existente
        Recepcion->>Sistema: Busca por DNI o Nombres en módulo Citas
        Recepcion->>Sistema: Asigna Tipo de Atención: Control o Continuador
        Sistema->>BD: Agenda Cita con Turno Secuencial #X para la fecha
    end

    Note over Recepcion,Medico: 2. Espera y Llamado en Sala
    Recepcion->>Sistema: Marca estado "En Espera"
    Medico->>Sistema: Observa tablero de Citas del Día ordenado por turno
    Medico->>Sistema: Inicia atención y actualiza estado a "Atendido"

    Note over Medico,PDF: 3. Acto Médico y Prescripción
    Medico->>Sistema: Registra Anamnesis, Funciones Vitales y Síntomas
    Medico->>Sistema: Asocia Diagnósticos CIE-10
    Medico->>Sistema: Prescribe Medicamentos con Posología
    Sistema->>BD: Guarda Consulta de forma transaccional (controles)
    Medico->>Sistema: Solicita impresión de receta (Formato A4 o A5)
    Sistema->>PDF: Renderiza documento con membrete y firma digitalizada
    PDF-->>Medico: Retorna Receta Oficial en PDF
    Medico->>Paciente: Entrega receta y plan de tratamiento
```

---

## 6. Seguridad, RBAC, Credenciales Médicas y Auditoría

### 6.1 Pipeline de Seguridad y Control de Caché

- **Protección contra Fuerza Bruta:** El método `AuthController@login` restringe el acceso tras 5 intentos fallidos consecutivos mediante hash transliterado de correo e IP (`Str::transliterate(Str::lower($email).'|'.$ip)`).
- **Protección de Privacidad Post-Logout (`PreventBackHistory` y `PreventBrowserCacheAfterLogout`):** Inyección obligatoria de cabeceras HTTP que impiden la visualización de datos médicos sensibles al utilizar el botón "Atrás" del navegador:
  ```http
  Cache-Control: no-cache, no-store, max-age=0, must-revalidate
  Pragma: no-cache
  Expires: Fri, 01 Jan 1990 00:00:00 GMT
  ```
- **Control Granular con Spatie Permission (`CheckPermission`):** Soporte de operadores lógicos OR (`|`) y listas delimitadas de permisos evaluadas directamente en rutas y controladores.

### 6.2 Roles Institucionales Preconfigurados

1. **Administrador (`administrador`):** Control total de seguridad, usuarios, roles, sincronización de permisos, auditoría, catálogos maestros y copias de seguridad.
2. **Especialista (`especialista`):** Perfil facultativo con acceso al circuito clínico completo: historias clínicas, atención de citas, emisión de recetas A4/A5, órdenes de exámenes auxiliares, informes de riesgo quirúrgico y dashboard epidemiológico.
3. **Asistente (`asistente`):** Perfil de recepción y triaje para admisión de pacientes, gestión de la cola de turnos, confirmación de citas y consulta de historias clínicas.

### 6.3 Perfil del Facultativo y Firma Digitalizada

A través de la ruta `/admin/profile` (`UsersController@profile` y `updateProfile`), cada especialista gestiona:

- Fotografía o avatar institucional.
- **Colegiatura Médica (CMP):** Número oficial del Colegio Médico del Perú.
- **Registro Nacional de Especialista (RNE):** Acreditación como especialista en Neumología o Medicina Respiratoria.
- **Firma Digitalizada (`firma_digital`):** Archivo gráfico de alta resolución almacenado en disco seguro (`storage/app/public/signatures/`) que se incrusta automáticamente en el pie de página de las recetas médicas, informes clínicos y evaluaciones de riesgo quirúrgico.

### 6.4 Trazabilidad y Auditoría Transaccional (`AuditLogTrait`)

El trait `AuditLogTrait` intercepta los eventos del ciclo de vida de los modelos (`created`, `updated`, `deleted`) y registra en la tabla `audit_logs`:

- Identificador del usuario responsable.
- Módulo y clave primaria afectada.
- Dirección IP y User-Agent del navegador.
- Instantánea en formato JSON del estado anterior (`old_data`) y del estado modificado (`new_data`), garantizando respaldo legal y auditoría inmutable.

---

## 7. Base de Datos: Modelos, Procedimientos Almacenados y Vistas

### 7.1 Modelos Eloquent del Sistema (36 Entidades)

| # | Modelo                    | Tabla                   | Relaciones y Propósito Principal                                                                          |
| :-: | :------------------------ | :---------------------- | :--------------------------------------------------------------------------------------------------------- |
| 1 | `Appointment`           | `controles`           | Consultas médicas de control, funciones vitales y anamnesis evolutiva.                                    |
| 2 | `AppointmentStatus`     | `estado_cita`         | Catálogo de estados de citas (`Pendiente`, `Atendido`, `Cancelado`, `En Espera`, `Reagendado`). |
| 3 | `AuditLog`              | `audit_logs`          | Registro inmutable de eventos de auditoría y trazabilidad transaccional.                                  |
| 4 | `BloodGroups`           | `grupo_sanguineo`     | Grupos sanguíneos y factor Rh.                                                                            |
| 5 | `Category`              | `droga_categoria`     | Categorías y familias farmacológicas.                                                                    |
| 6 | `Cita`                  | `citas`               | Turnos del día, cola de atención FIFO, fecha, hora, estado y tipo de atención.                          |
| 7 | `DegreesInstruction`    | `grado_instruccion`   | Niveles de instrucción académica.                                                                        |
| 8 | `Diagnostic`            | `diagnosticos`        | Catálogo internacional de enfermedades CIE-10.                                                            |
| 9 | `DiagnosticAppointment` | `control_diagnostico` | Diagnósticos asociados a una consulta médica de control.                                                 |
| 10 | `DiagnosticExam`        | `examen_diagnostico`  | Diagnósticos asociados a un examen auxiliar o espirometría.                                              |
| 11 | `DiagnosticReport`      | `informe_diagnostico` | Diagnósticos vinculados a un informe médico formal.                                                      |
| 12 | `DocumentType`          | `tipo_documento`      | Documentos de identidad oficiales (DNI, Carné de Extranjería, Pasaporte).                                |
| 13 | `Drug`                  | `drogas`              | Vademécum maestro de fármacos y principios activos.                                                      |
| 14 | `Enterprise`            | `empresa`             | Datos fiscales de Medytarq SAC, geolocalización y 4 recursos gráficos.                                   |
| 15 | `Exam`                  | `examenes`            | Pruebas diagnósticas, espirometrías, signos vitales y fluidos pleurales.                                 |
| 16 | `ExamType`              | `tipo_examen`         | Clasificación de exámenes (Espirometría, TAC, Radiografía, etc.).                                      |
| 17 | `History`               | `historias`           | Expediente clínico del paciente, factores ambientales, IPA y filiación.                                  |
| 18 | `Imagen`                | `imagenes`            | Archivos gráficos de exámenes (radiografías, trazados de espirometría, TAC).                           |
| 19 | `MaritalStatus`         | `estado_civil`        | Estados civiles del paciente.                                                                              |
| 20 | `MedicationAppointment` | `control_medicacion`  | Fármacos y posología prescritos en una consulta médica.                                                 |
| 21 | `MedicationExam`        | `examen_medicacion`   | Medicación administrada durante o después de un examen auxiliar.                                         |
| 22 | `Occupation`            | `ocupaciones`         | Clasificación de ocupaciones CIUO para riesgo respiratorio laboral.                                       |
| 23 | `Post`                  | `post`                | Artículos divulgativos y noticias en el blog del portal público.                                         |
| 24 | `PostType`              | `post_type`           | Categorías temáticas de publicaciones.                                                                   |
| 25 | `Presentation`          | `droga_presentacion`  | Formas farmacéuticas (Inhalador MDI, Polvo seco DPI, Tabletas, Jarabes).                                  |
| 26 | `Profile`               | `perfiles`            | Perfiles de configuración de usuario.                                                                     |
| 27 | `Report`                | `historia_informe`    | Informes médicos oficiales de neumología.                                                                |
| 28 | `Reservation`           | `reservations`        | Reservas de citas solicitadas desde la página web pública.                                               |
| 29 | `Risk`                  | `historia_riesgo`     | Evaluaciones de riesgo quirúrgico neumológico y cardiovascular.                                          |
| 30 | `Sex`                   | `sexo`                | Sexo biológico del paciente.                                                                              |
| 31 | `Smoking`               | `tabaquismo`          | Categorías de consumo de tabaco (`No fumador`, `Exfumador`, `Fumador`).                             |
| 32 | `Specialty`             | `especialidades`      | Especialidades médicas de los profesionales.                                                              |
| 33 | `TipoAtencion`          | `tipos_atencion`      | Catálogo de tipos de atención (`Nuevo`, `Control`, `Continuador`).                                 |
| 34 | `UbigeoDistrict`        | `ubigeo_distrito`     | Catálogo de distritos del Perú (Ubigeo nacional de 6 dígitos).                                          |
| 35 | `User`                  | `users`               | Cuentas de usuario con roles, credenciales médicas CMP/RNE y firma digital.                               |
| 36 | `UserLastLogin`         | `user_last_login`     | Registro del último acceso al sistema con fecha, hora y dirección IP.                                    |

### 7.2 Procedimientos Almacenados (18 PA_*)

- `PA_getAppointmentsByDni`: Consulta rápida de controles médicos por DNI.
- `PA_getAppointmentsByMedicalHistory`: Historial de consultas médicas por ID de historia.
- `PA_getDiagnosticByAppointment`: Diagnósticos CIE-10 asignados a una consulta médica.
- `PA_getDiagnosticByExam`: Diagnósticos asociados a un examen auxiliar.
- `PA_getDiagnosticByReport`: Diagnósticos incluidos en un informe médico formal.
- `PA_getExamsByDni` / `PA_getExamsByMedicalHistory`: Historial de exámenes auxiliares por DNI o ID de historia.
- `PA_getImgByExam`: Imágenes y trazados radiológicos vinculados a un examen.
- `PA_getMedicalHistoryByAppointment`: Datos de filiación del paciente a partir de la consulta médica.
- `PA_getMedicalHistoryByExam` / `PA_getMedicalHistoryByReport` / `PA_getMedicalHistoryByRisk`: Filiación para membretes de documentos clínicos oficiales.
- `PA_getMedicationByAppointment`: Fármacos y posología prescritos en una cita médica.
- `PA_getMedicationByExam`: Fármacos vinculados a un procedimiento diagnóstico.
- `PA_getReportsByDni` / `PA_getReportsByMedicalHistory`: Historial de informes clínicos emitidos.
- `PA_getRisksByDni` / `PA_getRisksByMedicalHistory`: Historial de evaluaciones de riesgo quirúrgico.

### 7.3 Vistas SQL Optimizadas (7 Vistas)

- `view_active_drugs`: Vademécum activo uniendo categoría, descripción y presentación farmacéutica.
- `view_active_histories`: Pacientes vigentes con cálculo de edad cronológica exacta a la fecha.
- `view_active_users`: Usuarios activos y la fecha de su último acceso al sistema.
- `view_user_roles_last_login`: Usuarios, roles asignados y último acceso registrado.
- `vista_estado_civil` / `vista_grupo_sanguineo` / `vista_tabaquismo`: Métricas demográficas agregadas para gráficos Highcharts.

---

## 8. Servicios Especializados e Integraciones Externas

### 8.1 Validación de Identidad RENIEC en Tiempo Real

- Conexión vía cliente HTTP con la API REST de **Decolecta** (`https://api.decolecta.com/v1/reniec/dni?numero={dni}`).
- Recupera en milisegundos nombres, apellidos paterno y materno oficiales, garantizando cero errores de transcripción en expedientes clínicos.

### 8.2 Motor de Notificaciones WhatsApp Cloud API

- Implementado a través del paquete `netflie/whatsapp-cloud-api`.
- Vinculado a la API oficial de Meta para confirmación automática de citas solicitadas por el portal público.

### 8.3 Resumen Inteligente con IA (DeepSeek / Kimi AI)

- Servicio `SummaryService` con arquitectura de tolerancia a fallos:
  1. Intenta consulta a **DeepSeek API** con timeout estricto de 15 segundos.
  2. En caso de fallo o timeout, conmuta automáticamente a **Kimi AI**.
  3. Si ambas fallan, retorna `null` de forma controlada sin interrumpir el flujo.

### 8.4 Motor de Renderizado de Recetas PDF A4 y A5

- Implementado mediante `barryvdh/laravel-dompdf`.
- Emisión dual adaptada a estándares de archivo clínico (A4) o recetario de mano (A5) con membrete institucional, logotipo, fecha, datos del paciente, prescripción posológica y firma digitalizada del médico tratante.

---

## 9. Requisitos del Entorno

- **PHP:** Versión `8.1` o superior (probado y compatible con PHP `8.3.6`).
- **Extensiones PHP obligatorias:** `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`, `fileinfo`, `gd`.
- **Node.js & NPM:** Node.js `18.x` o `20.x` LTS y NPM `9.x+` (para compilación de componentes Vue 3 y bundles con Vite).
- **Servidor de Base de Datos:** MySQL `8.0+` o MariaDB `10.5+`.
- **Gestor de Paquetes PHP:** Composer `2.2+`.
- **Servidor Web:** Nginx o Apache con módulo `mod_rewrite` habilitado.
- **Almacenamiento:** Permisos de lectura/escritura en directorios `storage/` y `bootstrap/cache/`.

---

## 10. Guía de Instalación y Puesta en Marcha

### Paso 1: Clonación del Repositorio

```bash
git clone <url-del-repositorio> neumo-lav
cd neumo-lav
```

### Paso 2: Instalación de Dependencias PHP y JavaScript

```bash
# Dependencias backend (Composer)
composer install --no-interaction --prefer-dist --optimize-autoloader

# Dependencias frontend (Vue 3, Pinia, Vite)
npm install
```

### Paso 3: Compilación de Activos Frontend (Vite)

```bash
# Compilación para entorno de producción
npm run build

# O en entorno de desarrollo activo con Hot Module Replacement (HMR):
# npm run dev
```

### Paso 4: Configuración del Archivo de Entorno

```bash
cp .env.example .env
php artisan key:generate
```

Edite el archivo `.env` y configure las credenciales de base de datos y servicios:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=neumotar_db
DB_USERNAME=root
DB_PASSWORD=su_clave_segura

# Correo Transaccional
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=su_correo@gmail.com
MAIL_PASSWORD=su_clave_aplicacion
MAIL_ENCRYPTION=tls

# APIs de Inteligencia Artificial (Opcional)
DEEPSEEK_API_KEY=sk-...
KIMI_API_KEY=...
```

### Paso 5: Ejecución de Migraciones y Poblado de Datos

El proyecto incluye migraciones para tablas, procedimientos almacenados y vistas SQL:

```bash
# Crear estructura de tablas, procedimientos almacenados y vistas
php artisan migrate

# Poblar catálogos maestros, ubigeos nacionales, roles y empresa
php artisan db:seed
```

### Paso 6: Enlace Simbólico del Almacenamiento

Para habilitar el acceso a imágenes de exámenes, firmas digitales y logotipos:

```bash
php artisan storage:link
```

### Paso 7: Optimización y Limpieza de Caché

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Paso 8: Inicio del Servidor de Desarrollo

```bash
php artisan serve
```

Acceda a la aplicación mediante su navegador en `http://127.0.0.1:8000`.

---

## 11. Usuarios y Roles Preconfigurados

Tras ejecutar los seeders del sistema (`DatabaseSeeder`), se habilitan los siguientes perfiles de acceso:

| Rol Asignado            | Correo Electrónico        | Contraseña Predeterminada | Alcance                                                                            |
| :---------------------- | :------------------------- | :------------------------- | :--------------------------------------------------------------------------------- |
| **Administrador** | `admin@neumotar.com`     | `password`               | Configuración, seguridad, roles, usuarios, catálogos y módulos clínicos.       |
| **Especialista**  | `medico@neumotar.com`    | `password`               | Consultas médicas, recetas A4/A5, exámenes, informes, firma digital y dashboard. |
| **Asistente**     | `asistente@neumotar.com` | `password`               | Admisión de pacientes, asignación de turnos, registro rápido y sala de espera.  |

> **Nota de Seguridad:** Se recomienda cambiar inmediatamente las contraseñas predeterminadas tras la puesta en producción desde el módulo de seguridad o desde la vista de perfil `/admin/profile`.

---

## 12. Copias de Seguridad y Tareas Programadas

El sistema integra `spatie/laravel-backup` para el resguardo periódico de la base de datos y de las imágenes médicas:

```bash
# Ejecutar respaldo de la base de datos exclusivamente
php artisan backup:run --only-db

# Ejecutar respaldo completo (Base de Datos + Archivos de Storage)
php artisan backup:run

# Monitorear estado de los respaldos
php artisan backup:monitor

# Limpiar respaldos antiguos según política de retención
php artisan backup:clean
```

Para automatizar las copias nocturnas, añada la siguiente entrada en el `crontab` de su servidor Linux:

```cron
0 2 * * * cd /ruta/al/proyecto && php artisan backup:run --only-db >> /dev/null 2>&1
```

---

## 13. Documento de Definición de Producto (PDR)

Para consultar la especificación técnica pormenorizada, el diccionario completo de las 36 entidades de datos, la matriz exhaustiva de las más de 85 rutas del sistema, la lógica detallada de cada controlador y los diagramas de secuencia clínica, revise el documento:

- **[Documento de Definición de Producto y Reporte de Arquitectura (PDR)](docs/PDR.md)** (Versión canónica en directorio `docs/`)
- También disponible en la raíz del repositorio como [`PDR.md`](PDR.md).

---

## 14. Historial de Versiones y Release Notes (v3.3.0)

El proyecto se gestiona bajo el estándar **Semantic Versioning 2.0.0** (`MAJOR.MINOR.PATCH`).

### Versión 3.3.0 (2026-10-20) — Release Asistencial y Frontend Reactivo Avanzado

Partiendo de la versión base `3.2.0`, esta entrega introduce nuevas capacidades funcionales y asistenciales 100% compatibles hacia atrás, constituyendo un incremento de versión **MINOR**:

1. **Componente Select2 Asíncrono con Límite de Servidor (`SearchSelect.vue`):**
   - Selector reactivo para Vue 3 con búsqueda en vivo, debounce en escritura, resaltado dinámico de coincidencias y navegación completa por teclado (flechas y Enter).
   - Soporte para tamaños `sm` (31px de altura ajustado al diseño de cabeceras) y `md` (formularios estándar).
   - Endpoint `/admin/specialties/search` limitado estrictamente a 5 registros (`->limit(5)`) para optimizar el consumo de CPU y memoria en el servidor.
   - Integrado en el filtro de la tabla de especialidades (`/admin/specialties`) con filtrado dinámico por `specialty_id`.
2. **Paginación en Cliente (10 registros por página):**
   - Composable `useClientPagination.js` integrado en las vistas de historial clínico:
     - `/admin/appointments/see/{id}` (Controles médicos)
     - `/admin/exams/see/{id}` (Exámenes auxiliares)
     - `/admin/reports/see/{id}` (Informes médicos)
     - `/admin/risks/see/{id}` (Informes de riesgo quirúrgico)
   - Controles de navegación responsivos, conteo dinámico de elementos y cambio fluido de páginas sin recargar el navegador.
3. **Modales Clínicos de Detalle y Previsualización Rápida:**
   - `PrescriptionDetailModal.vue`: Modal para visualizar diagnósticos y prescripciones farmacológicas detalladas de controles previos.
   - `ExamDetailModal.vue`: Modal para consultar diagnósticos, medicación aplicada e imágenes adjuntas del examen auxiliar.
   - `ReportPreviewModal.vue`: Modal de previsualización de informes médicos certificados.
   - `RiskPreviewModal.vue`: Modal de previsualización de informes de riesgo quirúrgico preoperatorio.
   - `QuickViewModal.vue`: Corrección funcional de los botones de "Receta" (controles) y "Ver" (exámenes) en `/admin/home`, con rediseño visual armónico en contraste con el botón de impresión.
4. **Dashboard y Reporte de Historias Clínicas Interactivo con Drill-Down Mensual:**
   - Gráfico de producción anual interactivo (`AnnualChart.vue`).
   - Al hacer clic sobre cualquier barra mensual (Historias, Exámenes o Controles), el gráfico se conmuta automáticamente a una vista de desglose mensual de la categoría seleccionada.
   - Botón alargado, estrecho y estilizado de "Volver al Reporte Anual" integrado armoniosamente con el diseño.
5. **Estandarización Estética de Filtros de Búsqueda:**
   - Rediseño homogéneo en dos columnas equilibradas de los grupos de filtro y búsqueda en los módulos principales de la plataforma.
6. **Actualización de Versión en Plantilla Principal:**
   - Indicador visual actualizado a `v3.3.0 • SPA` en el pie de página (`resources/views/partials/footer.blade.php`).

---

**Desarrollado para:** Medytarq SAC / Neumotar  
**Tarapoto, San Martín, Perú**

