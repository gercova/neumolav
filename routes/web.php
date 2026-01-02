<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\hcl\AppointmentsController;
use App\Http\Controllers\business\EnterpriseController;
use App\Http\Controllers\business\PostsController;
use App\Http\Controllers\hcl\ExamsController;
use App\Http\Controllers\maintenance\DrugsController;
use App\Http\Controllers\maintenance\CategoriesController;
use App\Http\Controllers\maintenance\PresentationsController as DPController;
use App\Http\Controllers\hcl\HistoriesController;
use App\Http\Controllers\hcl\ReportsController;
use App\Http\Controllers\hcl\RisksController;
use App\Http\Controllers\hcl\StatisticsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\maintenance\DiagnosticsController;
use App\Http\Controllers\maintenance\OccupationsController;
use App\Http\Controllers\security\ModulesController;
use App\Http\Controllers\security\PermissionController;
use App\Http\Controllers\security\SpecialtiesController;
use App\Http\Controllers\security\UsersController;
use App\Http\Controllers\web\HomePageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/',                                             [HomePageController::class, 'index']);
Route::get('/post/{url}',                                   [HomePageController::class, 'show'])->name('post.show');
Route::get('/posts/{tags?}',                                [HomePageController::class, 'posts'])->name('posts');
Route::get('/nosotros',                                     [HomePageController::class, 'aboutus'])->name('nosotros');
Route::get('/contacto',                                     [HomePageController::class, 'contact'])->name('contact');
Route::post('/contacto',                                    [HomePageController::class, 'sendContact'])->name('send-contact');
Route::post('/sendmail',                                    [HomePageController::class, 'storeMail'])->name('sendmail');

/**
 * LOGIN
 */
Route::get('/login',                                        [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login',                                       [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout',                                      [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/**
 * RUTAS PROTEGIDAS
 */
Route::middleware(['auth', 'prevent.back'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/home',                                     [HomeController::class, 'index'])->name('home');
        Route::get('/entreprise/dataEnterprise',                [EnterpriseController::class, 'getEnterprise']);
        Route::get('/enterprise/images',                        [EnterpriseController::class, 'getImages']);
        Route::get('/histories/quotes/{id}',                    [HistoriesController::class, 'addQuotes']);
        Route::get('/qoutes',                                   [HistoriesController::class, 'getQuotes']);
        Route::get('/checkStatusPatient/{id}',                  [HistoriesController::class, 'checkStatusPatient']);
        Route::middleware(['role:administrador', 'permission:seguridad'])->group(function(){
            /**
             * MÓDULOS DEL SISTEMA
             */
            Route::middleware(['permission:modulos'])->group(function(){
                Route::get('/modules',                          [ModulesController::class, 'index'])->name('security.modules');
                Route::get('/modules/list',                     [ModulesController::class, 'list']);
                Route::post('/modules/storeModule',             [ModulesController::class, 'storeModule']);
                Route::post('/modules/storeSubmodule',          [ModulesController::class, 'storeSubmodule']);
                Route::get('/modules/module/{id}',              [ModulesController::class, 'showModule']);
                Route::get('/modules/submodule/{id}',           [ModulesController::class, 'showSubmodule']);
                Route::delete('/modules/delete/{id}',           [ModulesController::class, 'destroyModule']);
                Route::delete('/submodules/delete/{id}',        [ModulesController::class, 'destroySubmodule']);
            });
            /**
             * PERMISOS
             */
            Route::middleware(['permission:permisos'])->group(function(){
                Route::get('/permissions',                      [PermissionController::class, 'index'])->name('security.permissions');
                Route::get('/permissions/list',                 [PermissionController::class, 'list']);
                Route::get('/permissions/{id}',                 [PermissionController::class, 'show']);
                Route::post('/permissions/store',               [PermissionController::class, 'store']);
                Route::post('/permissions/search',              [PermissionController::class, 'search']);
                Route::delete('/permissions/delete/{id}',       [PermissionController::class, 'destroy']);
            });
            /**
             * ESPECIALIDADES
             */
            Route::middleware(['permission:especialidades'])->group(function(){
                Route::get('/specialties',                      [SpecialtiesController::class, 'index'])->name('security.specialties');
                Route::get('/specialties/list',                 [SpecialtiesController::class, 'list']);
                Route::get('/specialties/{id}',                 [SpecialtiesController::class, 'show']);
                Route::post('/specialties/store',               [SpecialtiesController::class, 'store']);
                Route::delete('/specialties/delete/{id}',       [SpecialtiesController::class, 'destroy']);
            });
            /**
             * USUARIOS
             */
            Route::middleware(['permission:usuarios'])->group(function(){
                Route::get('/users/home',                       [UsersController::class, 'index'])->name('security.users.home');
                Route::get('/users/add',                        [UsersController::class, 'add'])->name('security.users.add');
                Route::get('/users/edit/{id}',                  [UsersController::class, 'edit'])->name('security.users.edit');
                Route::get('/users/role/{id}',                  [UsersController::class, 'permissions'])->name('security.users.role');
                Route::get('/users/list',                       [UsersController::class, 'list']);
                Route::get('/users/{id}',                       [UsersController::class, 'show']);
                Route::post('/users/store',                     [UsersController::class, 'store']);
                Route::post('/users/storePassword/{user}',      [UsersController::class, 'storePassword']);
                Route::post('/users/storePermission/{user}',    [UsersController::class, 'storePermission'])->name('securty.users.storePermission');
                Route::delete('/users/delete/{id}',             [UsersController::class, 'destroy']);
            });
        });
        /**
         * HISTORIAS
         */
        Route::middleware(['permission:historias'])->group(function(){
            Route::get('/histories/home',           [HistoriesController::class, 'index'])->name('hcl.histories.home');
            Route::get('/histories/add',            [HistoriesController::class, 'add'])->name('hcl.histories.add');
            Route::get('/histories/edit/{history}', [HistoriesController::class, 'edit'])->name('hcl.histories.edit');
            Route::post('/histories/location',      [HistoriesController::class, 'searchLocation']);
            Route::post('/histories/occupation',    [HistoriesController::class, 'searchOccupation']);
            Route::post('/histories/dni',           [HistoriesController::class, 'searchDni']);
            Route::post('/histories/store',         [HistoriesController::class, 'store']);
            Route::post('/histories/list',          [HistoriesController::class, 'list']);
            Route::delete('/histories/{hc}',        [HistoriesController::class, 'destroy']);
        });
        /**
         * EXÁMENES
         */
        Route::middleware(['permission:examenes'])->group(function(){
            Route::get('/exams/home',                   [ExamsController::class, 'index'])->name('hcl.exams.home');
            Route::get('/exams/add/{hc}',               [ExamsController::class, 'add'])->name('hcl.exams.add');
            Route::get('/exams/edit/{ex}',              [ExamsController::class, 'edit'])->name('hcl.exams.edit');
            Route::get('/exams/see/{hc}',               [ExamsController::class, 'see'])->name('hcl.exams.see');
            Route::post('/exams/store',                 [ExamsController::class, 'store']);
            Route::get('/exams/viewDetail/{ex}',        [ExamsController::class, 'viewDetail']);
            Route::get('/exams/viewImg/{image}',        [ExamsController::class, 'viewExamImage']);
            Route::get('/exams/list/{id}',              [ExamsController::class, 'listExams']);
            Route::get('/exams/listDiagnostic/{exam}',  [ExamsController::class, 'listOfDiagnosticsByExamId']);
            Route::get('/exams/listMedication/{exam}',  [ExamsController::class, 'listOfMedicationByExamId']);
            Route::get('/exams/listImg/{id}',           [ExamsController::class, 'listOfImagesByExamId']);
            Route::get('/exams/print/{ex}/{format}',    [ExamsController::class, 'printPrescriptionId'])->name('hcl.exams.print');
            Route::delete('/exams/delete/{ex}',         [ExamsController::class, 'destroy']);
            Route::delete('/ex-dx/delete/{dx}',         [ExamsController::class, 'destroyExamDiagnostics']);
            Route::delete('/ex-mx/delete/{mx}',         [ExamsController::class, 'destroyPrescriptionDrug']);
            Route::delete('/ex-img/delete/{ix}',        [ExamsController::class, 'destroyExamImage']);
        });
        /**
         * CONTROLES
         */
        Route::middleware(['permission:controles'])->group(function(){
            Route::get('/appointments/home',                    [AppointmentsController::class, 'index'])->name('hcl.appointments.home');
            Route::get('/appointments/add/{hc}',                [AppointmentsController::class, 'add'])->name('hcl.appointments.add');
            Route::get('/appointments/edit/{ap}',               [AppointmentsController::class, 'edit'])->name('hcl.appointments.edit');
            Route::get('/appointments/see/{hc}',                [AppointmentsController::class, 'see'])->name('hcl.appointments.see');
            Route::get('/appointments/viewDetail/{ap}',         [AppointmentsController::class, 'viewDetail']);
            Route::get('/appointments/list/{hc}',               [AppointmentsController::class, 'listAppointments']);
            Route::get('/appointments/listAppointments/{ap}',   [AppointmentsController::class, 'listAppointmentsByHC']);
            Route::get('/appointments/listDiagnostic/{ap}',     [AppointmentsController::class, 'listOfDiagnosticsByApp']);
            Route::get('/appointments/listMedication/{ap}',     [AppointmentsController::class, 'listOfMedicationByApp']);
            Route::post('/appointments/view-table',             [AppointmentsController::class, 'viewTable']);
            Route::get('/appointments/print/{ap}/{format}',     [AppointmentsController::class, 'printPrescription'])->name('hcl.appointments.print');
            Route::post('/appointments/store',                  [AppointmentsController::class, 'store']);
            Route::delete('/appointments/delete/{ap}',          [AppointmentsController::class, 'destroy']);
            Route::delete('/ap-dx/delete/{dx}',                 [AppointmentsController::class, 'destroyDiagnosticAppointment']);
            Route::delete('/ap-mx/delete/{ap}',                 [AppointmentsController::class, 'destroyMedicationAppointment']);
        });
        /**
         * INFORMES CLÍNICOS
         */
        Route::middleware(['permission:informes'])->group(function(){
            Route::get('/reports/home',                         [ReportsController::class, 'index'])->name('hcl.reports.home');
            Route::get('/reports/add/{id}',                     [ReportsController::class, 'add'])->name('hcl.reports.add');
            Route::get('/reports/edit/{id}',                    [ReportsController::class, 'edit'])->name('hcl.reports.edit');
            Route::get('/reports/see/{id}',                     [ReportsController::class, 'seeReports'])->name('hcl.reports.see');
            Route::post('/reports/store',                       [ReportsController::class, 'store']);
            Route::get('/reports/viewDetail/{id}',              [ReportsController::class, 'viewReportDetail']);
            Route::get('/reports/list/{id}',                    [ReportsController::class, 'listReports']);
            Route::get('/reports/listReports/{id}',             [ReportsController::class, 'listReportsByDNI']);
            Route::get('/reports/listDiagnostic/{id}',          [ReportsController::class, 'listOfDiagnosticsByReportId']);
            Route::get('/reports/print/{id}',                   [ReportsController::class, 'printReportId'])->name('hcl.reports.print');
            Route::delete('/reports/delete/{id}',               [ReportsController::class, 'destroy']);
            Route::delete('/rp-dx/delete/{id}',                 [ReportsController::class, 'destroyDiagnosticReport']);
        });
        /**
         * INFORMES DE RIESGO CLÍNICO
         */
        Route::middleware(['permission:riesgos'])->group(function(){
            Route::get('/risks/home',                           [RisksController::class, 'index'])->name('hcl.risks.home');
            Route::get('/risks/add/{id}',                       [RisksController::class, 'add'])->name('hcl.risks.add');
            Route::get('/risks/edit/{id}',                      [RisksController::class, 'edit'])->name('hcl.risks.edit');
            Route::get('/risks/see/{id}',                       [RisksController::class, 'seeRisks'])->name('hcl.risks.see');
            Route::get('/risks/viewDetail/{id}',                [RisksController::class, 'viewRiskDetail']);
            Route::get('/risks/list/{id}',                      [RisksController::class, 'listRisks']);
            Route::get('/risks/listRisks/{id}',                 [RisksController::class, 'listRisksByDNI']);
            Route::get('/risks/print/{id}',                     [RisksController::class, 'printRiskReportId'])->name('hcl.risks.print');
            Route::post('/risks/store',                         [RisksController::class, 'store']);
            Route::delete('/risks/delete/{id}',                 [RisksController::class, 'destroy']);
        });
        /**
         * CATEGORÍA DROGA
         */
        Route::middleware(['permission:categorias'])->group(function(){
            Route::get('/categories',                           [CategoriesController::class, 'index'])->name('maintenance.categories');
            Route::get('/categories/list',                      [CategoriesController::class, 'list']);
            Route::get('/categories/{cat}',                      [CategoriesController::class, 'show']);
            Route::post('/categories/store',                    [CategoriesController::class, 'store']);
            Route::delete('/categories/delete/{cat}',            [CategoriesController::class, 'destroy']);
        });
        /**
         * PRESENTACIÓN DROGA
         */
        Route::middleware(['permission:presentaciones'])->group(function(){
            Route::get('/presentations',                        [DPController::class, 'index'])->name('maintenance.presentations');
            Route::get('/presentations/list',                   [DPController::class, 'list']);
            Route::get('/presentations/{pre}',                  [DPController::class, 'show']);
            Route::post('/presentations/store',                 [DPController::class, 'store']);
            Route::delete('/presentations/delete/{pre}',        [DPController::class, 'destroy']);
        });
        /**
         * DROGA
         */
        Route::middleware(['permission:farmacos'])->group(function(){
            Route::get('/drugs',                    [DrugsController::class, 'index'])->name('maintenance.drugs');
            Route::get('/drugs/list',               [DrugsController::class, 'list']);
            Route::get('/drugs/{drug}',             [DrugsController::class, 'show']);
            Route::post('/drugs/store',             [DrugsController::class, 'store']);
            Route::post('/drugs/search',            [DrugsController::class, 'search']);
            Route::delete('/drugs/delete/{drug}',   [DrugsController::class, 'destroy']);
        });
        /**
         * DIAGNOSTÍCO
         */
        Route::middleware(['permission:diagnosticos'])->group(function(){
            Route::get('/diagnostics',                          [DiagnosticsController::class, 'index'])->name('maintenance.diagnostics');
            Route::get('/diagnostics/list',                     [DiagnosticsController::class, 'list']);
            Route::get('/diagnostics/{diagnostic}',             [DiagnosticsController::class, 'show']);
            Route::get('/diagnostics/autocomplete',             [DiagnosticsController::class, 'advancedSearch']);
            Route::post('/diagnostics/store',                   [DiagnosticsController::class, 'store']);
            Route::post('/diagnostics/search',                  [DiagnosticsController::class, 'search']);
            Route::delete('/diagnostics/delete/{diagnostic}',   [DiagnosticsController::class, 'destroy']);
        });
        /**
         * OCUPACIONES
         */
        Route::middleware(['permission:ocupaciones'])->group(function(){
            Route::get('/occupations',                          [OccupationsController::class, 'index'])->name('maintenance.occupations');
            Route::get('/occupations/list',                     [OccupationsController::class, 'list']);
            Route::get('/occupations/{oc}',                     [OccupationsController::class, 'show']);
            Route::post('/occupations/store',                   [OccupationsController::class, 'store']);
            Route::post('/occupations/search',                  [OccupationsController::class, 'search']);
            Route::delete('/occupations/delete/{oc}',           [OccupationsController::class, 'destroy']);
        });
        /**
         * ENTERPRISE
         */
        Route::middleware(['permission:empresa'])->group(function(){
            Route::post('/enterprise/store',                        [EnterpriseController::class, 'store']);
            Route::get('/enterprise',                               [EnterpriseController::class, 'index'])->name('business.enterprise');
        });
        /**
         * PUBLICACIONES
         */
        Route::middleware(['permission:posts'])->group(function(){
            Route::get('/publications',                             [PostsController::class, 'index'])->name('business.posts');
            Route::get('/publications/add/',                        [PostsController::class, 'add'])->name('business.posts.add');
            Route::get('/publications/edit/{id}',                   [PostsController::class, 'edit'])->name('business.posts.edit');
            Route::get('/publications/list',                        [PostsController::class, 'list']);
            Route::get('/publications/{id}',                        [PostsController::class, 'show']);
            Route::post('/publications/store',                      [PostsController::class, 'store']);
            Route::delete('/publications/delete/{id}',              [PostsController::class, 'destroy']);
        });
        /**
         * REPORTES GRAFICOS
         */
        Route::middleware(['permission:reportes'])->group(function(){
            Route::middleware(['permission:dashboard'])->group(function(){
                Route::get('/dashboard',                            [StatisticsController::class, 'index'])->name('dashboard');
            });
            Route::get('/dashboard/hcCount',                        [StatisticsController::class, 'getCountRows']);
            Route::get('/dashboard/hcByMonth',                      [StatisticsController::class, 'HCByMonth']);
            Route::get('/dashboard/histories/{year}',               [StatisticsController::class, 'getAnnualData']);
            // Route::get('/dashboard/exams/{year}',                   [StatisticsController::class, 'getExamsByYear']);
            // Route::get('/dashboard/appointments/{year}',            [StatisticsController::class, 'getAppointmentsByYear']);
            Route::get('/dashboard/diagnosticsByExam',              [StatisticsController::class, 'getDiagnosticsByExam']);
            Route::get('/dashboard/drugsByExam',                    [StatisticsController::class, 'getDrugsByExam']);
            Route::get('/dashboard/historiesBySex',                 [StatisticsController::class, 'getHistoriesBySex']);
            Route::get('/dashboard/historiesBySmoking',             [StatisticsController::class, 'getHistoriesBySmoking']);
            Route::get('/dashboard/historiesByBloodingGroup',       [StatisticsController::class, 'getHistoriesByBloodingGroup']);
            Route::get('/dashboard/historiesByDegreeIntruction',    [StatisticsController::class, 'getHistoriesByDegreeIntruction']);
            Route::get('/dashboard/historiesByMaritalStatus',       [StatisticsController::class, 'getHistoriesByMaritalStatus']);
        });
    });
});
