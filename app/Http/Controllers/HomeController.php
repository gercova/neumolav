<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\DocumentType;
use App\Models\Exam;
use App\Models\History;
use App\Models\Sex;
use App\Models\TipoAtencion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller {
    
    public function __construct() {
        $this->middleware(['auth', 'prevent.back']);
    }

    public function index(Request $request): View {
        $historias     = History::whereNull('deleted_at')->count();
        $examenes      = Exam::whereNull('deleted_at')->count();
        $controles     = Appointment::whereNull('deleted_at')->count();
        $users         = User::whereNull('deleted_at')->count();

        $selectedDate  = $request->query('date', Carbon::today()->format('Y-m-d'));
        $documentTypes = DocumentType::where('id', '!=', 2)->get();
        $sexes         = Sex::get();
        $statuses      = AppointmentStatus::all();
        $tiposAtencion = TipoAtencion::all();

        return view('home.index', compact(
            'historias',
            'examenes',
            'controles',
            'users',
            'selectedDate',
            'documentTypes',
            'sexes',
            'statuses',
            'tiposAtencion'
        ));
    }
}
