# Neumo-LAV / Neumotar

> **Sistema Integral de Gestión de Historias Clínicas Electrónicas (HCL), Diagnósticos Neumológicos Especializados, Salud Ocupacional, Prescripción Farmacológica y Analítica Epidemiológica.**

[![Laravel 10](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8.1+](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL 8](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)
[![AdminLTE 3](https://img.shields.io/badge/AdminLTE-3.2-1E88E5?style=for-the-badge)](https://adminlte.io)
[![Bootstrap 4](https://img.shields.io/badge/Bootstrap-4.6-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-Proprietary-blue?style=for-the-badge)]()

---

## Tabla de Contenidos

- [1. Descripción del Proyecto](#1-descripción-del-proyecto)
- [2. Capacidades y Características Clínicas](#2-capacidades-y-características-clínicas)
- [3. Arquitectura del Sistema](#3-arquitectura-del-sistema)
- [4. Estructura de Módulos](#4-estructura-de-módulos)
- [5. Capa de Seguridad, RBAC y Auditoría](#5-capa-de-seguridad-rbac-y-auditoría)
- [6. Base de Datos: Procedimientos Almacenados y Vistas](#6-base-de-datos-procedimientos-almacenados-y-vistas)
- [7. Requisitos del Entorno](#7-requisitos-del-entorno)
- [8. Guía de Instalación y Puesta en Marcha](#8-guía-de-instalación-y-puesta-en-marcha)
- [9. Usuarios y Roles Preconfigurados](#9-usuarios-y-roles-preconfigurados)
- [10. Copias de Seguridad y Tareas Programadas](#10-copias-de-seguridad-y-tareas-programadas)
- [11. Documento de Definición de Producto (PDR)](#11-documento-de-definición-de-producto-pdr)

---

## 1. Descripción del Proyecto

**Neumo-LAV** (denominado comercialmente en la atención de pacientes como **Neumotar**) es una plataforma médica desarrollada para digitalizar y optimizar la gestión asistencial de centros especializados en **Neumología, Medicina Respiratoria y Salud Ocupacional**. Diseñado para la empresa **Medytarq SAC** en Tarapoto (San Martín, Perú), el sistema sustituye el papel por un entorno clínico digitalizado, auditable y conforme a la normativa de salud.

La plataforma resuelve el ciclo médico de manera integral:
1. **Filiación Demográfica Asistida:** Verificación sincrónica de identidad mediante la API de RENIEC y codificación territorial normalizada según el estándar de Ubigeo nacional (Departamento, Provincia, Distrito).
2. **Evaluación de Riesgo Pulmonar Especializado:** Registro sistemático de factores ambientales y ocupacionales propios de la región (humo de biomasa/leña, antecedentes de tuberculosis - TBC, polvos orgánicos/inorgánicos) y cálculo automático del **Índice Paquete Año (IPA)** para tabaquismo.
3. **Controles y Consultas Médicas:** Registro estructurado de la anamnesis, funciones vitales, signos respiratorios, diagnóstico multivariado **CIE-10** y prescripción farmacológica con posología detallada.
4. **Prescripción Médica Multiformato:** Generación y descarga instantánea de recetas médicas en formatos estándar **A4** y medio pliego **A5** mediante `Barryvdh\DomPDF`, con membrete, logotipo y código de control.
5. **Apoyo al Diagnóstico e Imagenología:** Gestión de exámenes auxiliares (Espirometrías basales y post-broncodilatador, Tomografías, Radiografías de Tórax) con visor de imágenes y trazados diagnósticos.
6. **Informes Médicos y Riesgo Quirúrgico:** Emisión de dictámenes clínicos e informes preoperatorios de riesgo neumológico y cardiovascular.
7. **Analítica Epidemiológica en Tiempo Real:** Tableros estadísticos interactivos desarrollados con Highcharts que visualizan morbilidad respiratoria, perfiles de prescripción y métricas demográficas.
8. **Portal Web y Reservas Omnicanal:** Portal web público con artículos de salud respiratoria y motor de solicitud de citas en línea con confirmación vía **WhatsApp Cloud API** y correo electrónico.

---

## 2. Capacidades y Características Clínicas

### 2.1 Historia Clínica Neumológica (HCL)
- **Consulta RENIEC en Vivo:** Autocompletado de nombres y apellidos a partir del número de DNI utilizando la API REST de Decolecta.
- **Cálculo Automatizado de Carga Tabáquica (IPA):**
  $$\text{IPA} = \frac{\text{Cigarrillos al día} \times \text{Años de consumo}}{20}$$
- **Registro de Comorbilidades Respiratorias:** Control explícito de asma bronquial, EPOC, EPID, tuberculosis pulmonar, cáncer de pulmón, efusión pleural y neumonías previas.
- **Encolamiento Automático de Citas:** Al admitir a un paciente nuevo, el sistema crea en tiempo real su turno en la cola de atención médica del día.

### 2.2 Prescripción Farmacológica y Recetas
- **Vademécum Específico:** Maestro de medicamentos organizado por familias farmacológicas y presentaciones (aerosoles MDI, polvos secos DPI, soluciones para nebulizar, tabletas, jarabes).
- **Emisión Dual en PDF:** Formato formal A4 para archivo clínico o formato A5 para recetario de bolsillo, cumpliendo con los estándares de dispensación en farmacia.

### 2.3 Exámenes Auxiliares e Imagenología
- Carga de archivos gráficos (JPG, PNG, PDF) vinculados a la historia del paciente (curvas espirométricas, radiografías de tórax, tomografías computarizadas).
- Asociación de diagnósticos específicos y medicación previa al procedimiento.

### 2.4 Informes Especializados
- **Informes Clínicos:** Informes para empleadores, peritajes o trámites de salud pública con codificación CIE-10.
- **Informes de Riesgo Quirúrgico:** Valoración del riesgo neumológico preoperatorio con recomendaciones anestésicas perioperatorias.

### 2.5 Resúmenes Asistidos por Inteligencia Artificial
- Servicio `SummaryService` con integración a las APIs de **DeepSeek** y **Kimi AI** con tolerancia a fallos para generar resúmenes automatizados de antecedentes médicos complejos y artículos científicos.

---

## 3. Arquitectura del Sistema

El software sigue el patrón arquitectónico **Modelo-Vista-Controlador (MVC)** sobre Laravel 10:

```
[Navegador Web / Terminal del Consultorio]
                    │
                    ▼
[Servidor Web Nginx / Apache]
                    │
                    ▼
[Capa de Middleware: Autenticación, Anti-Fuerza Bruta, Control de Caché, Auditoría]
                    │
                    ▼
[Capa de Controladores: HCL, Mantenimiento, Seguridad, Corporativo, Web]
                    │
                    ├─► [Servicios: SummaryService (IA), TableViewService, DomPDF Engine]
                    ├─► [Integraciones: API RENIEC, WhatsApp Cloud API, Mail Transaccional]
                    │
                    ▼
[Capa de Persistencia: 38 Modelos Eloquent + AuditLogTrait]
                    │
                    ▼
[Motor de Base de Datos: MySQL 8.0 / MariaDB]
                    ├─► 18 Procedimientos Almacenados (PA_*)
                    └─► 7 Vistas SQL Optimizadas (view_*)
```

---

## 4. Estructura de Módulos

```
app/Http/Controllers/
├── ApplicationController.php          # Controlador base de la aplicación
├── Controller.php                     # Clase base abstracta de controladores
├── HomeController.php                 # Panel principal tras la autenticación
├── auth/
│   └── AuthController.php             # Login con Rate Limiting, sesión y logout
├── business/
│   ├── EnterpriseController.php       # Datos institucionales, logos y ubicación
│   └── PostsController.php            # Publicaciones y blog de salud respiratoria
├── hcl/
│   ├── AppointmentsController.php     # Consultas médicas, recetas y PDFs A4/A5
│   ├── ExamsController.php            # Exámenes auxiliares, espirometría e imágenes
│   ├── HistoriesController.php        # Historias clínicas, RENIEC DNI y cola de citas
│   ├── ReportsController.php          # Informes clínicos oficiales en PDF
│   ├── RisksController.php            # Informes de riesgo quirúrgico neumológico
│   └── StatisticsController.php       # Tablero analítico y epidemiológico Highcharts
├── maintenance/
│   ├── CategoriesController.php       # Categorías farmacológicas
│   ├── DiagnosticsController.php      # Catálogo de diagnósticos CIE-10
│   ├── DrugsController.php            # Catálogo maestro de fármacos
│   ├── OccupationsController.php      # Catálogo de ocupaciones CIUO
│   └── PresentationsController.php    # Presentaciones farmacéuticas
├── security/
│   ├── ConfigController.php           # Configuración del sistema
│   ├── ModulesController.php          # Estructura dinámica de menús y submódulos
│   ├── PermissionController.php       # Gestión de permisos Spatie
│   ├── SpecialtiesController.php      # Especialidades de los facultativos
│   └── UsersController.php            # Cuentas de usuario, roles y claves
└── web/
    └── HomePageController.php         # Portal público, contacto y reservas online
```

---

## 5. Capa de Seguridad, RBAC y Auditoría

### 5.1 Pipeline de Seguridad
- **Rate Limiting:** Máximo 5 intentos fallidos en `AuthController@login` calculados mediante hash transliterado de correo e IP.
- **Protección de Navegación (`PreventBackHistory` & `PreventBrowserCacheAfterLogout`):** Inyección de cabeceras HTTP `Cache-Control: no-cache, no-store, must-revalidate` que impiden la visualización de historias clínicas tras el cierre de sesión mediante el botón "Atrás" del explorador.
- **Control de Permisos (`CheckPermission`):** Soporte para operadores lógicos or (`|`) y listas delimitadas de permisos para evaluación flexible en rutas.

### 5.2 Roles Preconfigurados (Spatie Permission)
1. **Administrador (`administrador`):** Control total de seguridad, usuarios, catálogos, historias clínicas, módulos dinámicos y respaldos.
2. **Especialista (`especialista`):** Acceso clínico para médicos neumólogos: apertura de historias, consultas, exámenes auxiliares, emisión de recetas A4/A5, informes de riesgo y estadísticas.
3. **Asistente (`asistente`):** Perfil de recepción: registro de pacientes, consulta y creación de citas.

### 5.3 Auditoría Médico-Legal (`AuditLogTrait` y `LogUserActivity`)
- Registro de accesos por usuario con IP y User-Agent en `audit_logs`.
- Intercepción de eventos del ciclo de vida de modelos (`created`, `updated`, `deleted`) almacenando el estado anterior y el nuevo estado en formato JSON (`old_data`, `new_data`).

---

## 6. Base de Datos: Procedimientos Almacenados y Vistas

### 6.1 Procedimientos Almacenados (18 SPs)
Diseñados para optimizar el tiempo de respuesta y desacoplar consultas complejas:
- `PA_getAppointmentsByDni`: Obtención de citas mediante DNI.
- `PA_getAppointmentsByMedicalHistory`: Citas históricas del paciente.
- `PA_getDiagnosticByAppointment`: Diagnósticos CIE-10 asignados a una consulta.
- `PA_getDiagnosticByExam`: Diagnósticos derivados de exámenes auxiliares.
- `PA_getDiagnosticByReport`: Diagnósticos incluidos en informes médicos.
- `PA_getExamsByDni` / `PA_getExamsByMedicalHistory`: Consulta de exámenes auxiliares.
- `PA_getImgByExam`: Imágenes y trazados radiológicos asociados a un examen.
- `PA_getMedicalHistoryByAppointment`: Datos de filiación requeridos para el membrete de recetas.
- `PA_getMedicalHistoryByExam` / `PA_getMedicalHistoryByReport` / `PA_getMedicalHistoryByRisk`: Filiación para documentos oficiales.
- `PA_getMedicationByAppointment`: Fármacos y posología prescritos en una cita.
- `PA_getMedicationByExam`: Fármacos vinculados a un examen auxiliar.
- `PA_getReportsByDni` / `PA_getReportsByMedicalHistory`: Historial de informes médicos emitidos.
- `PA_getRisksByDni` / `PA_getRisksByMedicalHistory`: Historial de evaluaciones de riesgo quirúrgico.

### 6.2 Vistas SQL (7 Vistas)
- `view_active_drugs`: Vademécum activo uniendo categoría, descripción y presentación.
- `view_active_histories`: Pacientes vigentes con cálculo de edad cronológica exacta a la fecha.
- `view_active_users`: Usuarios activos y su último inicio de sesión.
- `view_user_roles_last_login`: Usuarios, roles asignados y último acceso registrado.
- `vista_estado_civil` / `vista_grupo_sanguineo` / `vista_tabaquismo`: Métricas agregadas para Highcharts.

---

## 7. Requisitos del Entorno

- **PHP:** Versión `8.1` o superior.
- **Extensiones PHP obligatorias:** `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`, `fileinfo`, `gd`.
- **Servidor de Base de Datos:** MySQL `8.0+` o MariaDB `10.5+`.
- **Gestor de Paquetes:** Composer `2.2+`.
- **Servidor Web:** Nginx o Apache con módulo `mod_rewrite` habilitado.
- **Almacenamiento:** Permisos de escritura en `storage/` y `bootstrap/cache/`.

---

## 8. Guía de Instalación y Puesta en Marcha

### Paso 1: Clonación del Repositorio
```bash
git clone <url-del-repositorio> neumo-lav
cd neumo-lav
```

### Paso 2: Instalación de Dependencias PHP
```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
```

### Paso 3: Configuración del Archivo de Entorno
```bash
cp .env.example .env
php artisan key:generate
```

Edite el archivo `.env` y configure las credenciales de conexión a la base de datos:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=neumotar_db
DB_USERNAME=root
DB_PASSWORD=su_clave_segura
```

### Paso 4: Ejecución de Migraciones y Poblado de Datos
El proyecto incluye migraciones para tablas, procedimientos almacenados y vistas SQL:
```bash
# Crear estructura de tablas, procedimientos almacenados y vistas
php artisan migrate

# Poblar catalogos maestros, ubigeos nacionales, roles y empresa
php artisan db:seed
```

### Paso 5: Enlace Simbólico del Almacenamiento
Para habilitar el acceso a imágenes de exámenes y logotipos institucionales:
```bash
php artisan storage:link
```

### Paso 6: Optimización y Limpieza de Caché
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Paso 7: Inicio del Servidor de Desarrollo
```bash
php artisan serve
```
Acceda a la aplicación mediante su navegador en `http://127.0.0.1:8000`.

---

## 9. Usuarios y Roles Preconfigurados

Tras ejecutar los seeders del sistema (`DatabaseSeeder`), se habilitan los siguientes perfiles de acceso:

| Rol Asignado | Correo Electrónico | Contraseña Predeterminada | Alcance |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin@neumotar.com` | `password` | Configuración, seguridad, RBAC y módulos clínicos. |
| **Especialista** | `medico@neumotar.com` | `password` | Consulta médica, recetas A4/A5, exámenes, informes y dashboard. |
| **Asistente** | `asistente@neumotar.com` | `password` | Admisión de pacientes y gestión de cola de citas. |

> **Nota de Seguridad:** Se recomienda cambiar inmediatamente las contraseñas predeterminadas tras la puesta en producción.

---

## 10. Copias de Seguridad y Tareas Programadas

El sistema integra `spatie/laravel-backup` para el resguardo periódico de la base de datos y de las imágenes médicas:

```bash
# Ejecutar respaldo de la base de datos exclusivamente
php artisan backup:run --only-db

# Ejecutar respaldo completo (Base de Datos + Archivos Multimedia de Exámenes)
php artisan backup:run

# Monitorear estado de los respaldos
php artisan backup:monitor
```

Para automatizar las copias nocturnas, añada la siguiente entrada en el `crontab` de su servidor Linux:
```cron
0 2 * * * cd /ruta/al/proyecto && php artisan backup:run --only-db >> /dev/null 2>&1
```

---

## 11. Documento de Definición de Producto (PDR)

Para consultar la especificación técnica pormenorizada, el diccionario completo de las 38 entidades de datos, la matriz de las más de 70 rutas y la lógica detallada de cada controlador, revise el documento:

- **[Documento de Definición de Producto y Reporte de Arquitectura (PDR)](docs/PDR.md)**

---

**Desarrollado para:** Medytarq SAC / Neumotar  
**Tarapoto, San Martín, Perú**
