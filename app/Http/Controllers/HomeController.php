<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Exam;
use App\Models\History;
use App\Models\User;
use Illuminate\Contracts\View\View;

class HomeController extends Controller {
    
    public function __construct() {
        $this->middleware('auth');
    }

    public function index(): View {
        $historias  = History::whereNull('deleted_at')->count();
        $examenes   = Exam::whereNull('deleted_at')->count();
        $controles  = Appointment::whereNull('deleted_at')->count();
        $users      = User::whereNull('deleted_at')->count();
        return view('home.index', compact('historias', 'examenes', 'controles', 'users'));
    }
}
