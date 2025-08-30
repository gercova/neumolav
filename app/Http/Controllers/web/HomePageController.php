<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationValidate;
use App\Mail\CitaSolicitada;
use App\Models\Enterprise;
use App\Models\Post;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
use Exception;

class HomePageController extends Controller {

    public function index(): View {
        $enterprise = Enterprise::where('id', 1)->get();
        $posts      = $this->getPosts(3);
        return view('homepage.index', compact('enterprise', 'posts'));
    }

    public function getPosts($quantity = null) {
        $query = Post::query();

        if ($quantity !== null) {
            $query->join('users', 'post.autor_post', '=', 'users.id')
                ->join('post_type as pt', 'post.type_id', '=', 'pt.id')
                ->join('especialidades', 'users.specialty', '=', 'especialidades.id')
                ->select('post.*', 'users.*', 'pt.description as tipo', 'especialidades.descripcion as especialidad')
                ->where('post.deleted_at', null)
                ->orderBy('post.id', 'asc')
                ->limit($quantity);
        }

        $posts = $query->get();
        return $posts;
    }

    public function recentPosts($quantity, $url){
        if ($url === null) {
            $posts = Post::query()
                ->orderBy('id', 'asc')
                ->where('type_id', 1)
                ->where('id','<>', 21)
                ->where('deleted_at', null)
                ->limit($quantity)
                ->get();
        } else {
            $posts = Post::query()
                ->orderBy('id', 'asc')
                ->where('type_id', 1)
                ->where('url', '<>', $url)
                ->where('id','<>', 21)
                ->where('deleted_at', null)
                ->limit($quantity)
                ->get();
        }

        return $posts;
    }

    /*public function storeAppoitment(Request $request): JsonResponse {
        // 1️⃣ Validar datos
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'date' => 'required|date',
        ]);

        // 2️⃣ Guardar en la base de datos
        $reservation = Reservation::create([
            'name'  => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'date'  => $request->date,
        ]);

        // 3️⃣ Enviar mensaje por WhatsApp
        $whatsapp = new WhatsAppCloudApi([
            'from_phone_number_id'  => env('WHATSAPP_FROM_PHONE_NUMBER_ID'),
            'access_token'          => env('WHATSAPP_ACCESS_TOKEN'),
        ]);

        $message = "📌 *Nueva Reserva Registrada* 📌\n" .
                "👤 *Nombre:* {$request->name}\n" .
                "📞 *Teléfono:* {$request->phone}\n" .
                "📅 *Fecha:* {$request->date}";

        $whatsapp->sendTextMessage(
            "51{$request->phone}",  // Código de país + número (Ej: 51987654321 para Perú)
            $message
        );

        // 4️⃣ Redirigir con mensaje de éxito
        return response()->json([
            'status' => true,
            'message' => '¡Reserva enviada y guardada!',
        ]);
    }*/

    public function storeMail(ReservationValidate $request): JsonResponse {
        try {
            // Guardar en base de datos
            $validated  = $request->validated();
            $cita       = Reservation::create($validated);
            // Enviar correo
            Mail::to('atencion-citas@neumotar.com')->send(new CitaSolicitada($cita));
            
            return response()->json([
                'status'    => true,
                'message'   => '¡En buena hora!',
                'text'      => 'En breve nos contactaremos con usted.',
                'type'      => 'success'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error al procesar la solicitud',
                'text'      => $e->getMessage(),
                'type'      => 'error'
            ], 500);
        }
    }

    public function posts($tags = null): View {
        if (!empty($tags)){
            $posts = Post::where('etiquetas', 'like', '%'.$tags.'%')
                ->join('post_type as pt', 'post.type_id', '=', 'pt.id')
                ->where('post.id','<>', 21)
                ->select('post.*', 'pt.description as tipo')
                ->get();
        } else {
            $posts = Post::join('post_type as pt', 'post.type_id', '=', 'pt.id')
                ->select('post.*', 'pt.description as tipo')
                ->where('post.id','<>', 21)
                ->get();
        }
        return view('homepage.posts', compact('posts'));
    }

    public function show($url): View {
        $recentPost = $this->recentPosts(2, $url);
        $post       = Post::join('users', 'post.autor_post', '=', 'users.id')
            ->join('post_type as pt', 'post.type_id', '=', 'pt.id')
            ->join('especialidades', 'users.specialty', '=', 'especialidades.id')
            ->select('post.*', 'users.*', 'pt.description as tipo', 'especialidades.descripcion as especialidad')
            ->where('url', $url)
            ->where('post.id','<>', 21)
            ->firstOrFail(); 
        return view('homepage.post', compact('recentPost', 'post')); 
    }

    public function aboutus(): View {
        $post = Post::where('id', 21)->firstOrFail();
        $recentPost = $this->recentPosts(2, null);
        return view('homepage.aboutus', compact('post', 'recentPost'));
    }
}
