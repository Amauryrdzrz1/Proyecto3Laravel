<?php

namespace App\Http\Controllers\ApiMail;

use App\Http\Controllers\Controller;
use App\Mail\comentarioBorrado;
use App\Mail\comentarioEditado;
use App\Mail\comentarioSalvado;
use App\Mail\CorreoRegistro;
use App\Mail\notificaUsuarios;
use App\Mail\PermisoOtorgado;
use App\mail\pedirPermiso;
use App\Mail\usuarioComento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class mailController extends Controller
{
    public function enviaCorreo(Request $request){
        
        // Mail::to($request->user())->send(new CorreoRegistro());
        Mail::to($request->to)->send(new CorreoRegistro($request->to));

        return response()->json(["to"=>$request->to], 200);
    }

    public function permisoOtorgado(Request $request){
        
        // Mail::to($request->user())->send(new CorreoRegistro());
        Mail::to($request->email)->send(new PermisoOtorgado($request->email));
        return response()->json(["to"=>$request->email], 200);
    }

    public function pedirPermiso(Request $request){
        
        // Mail::to($request->user())->send(new CorreoRegistro());
        Mail::to('amauryrdzrz@gmail.com')->send(new pedirPermiso($request->email));
        return response()->json(["to"=>$request->email], 200);
    }

    public function comentarioSalvado(Request $request){
        
        // Mail::to($request->user())->send(new CorreoRegistro());
        Mail::to($request->email)->send(new comentarioSalvado($request->email));
        return response()->json(["to"=>$request->email], 200);
    }

    public function usuarioComento(Request $request){
        
        // Mail::to($request->user())->send(new CorreoRegistro());
        Mail::to('amauryrdzrz@gmail.com')->send(new usuarioComento($request->email));
        return response()->json(["to"=>$request->email], 200);
    }

    public function notificaUsuarios(Request $request){
        
        // Mail::to($request->user())->send(new CorreoRegistro());
        Mail::bcc($request->email)->send(new notificaUsuarios($request->email,$request->producto, $request->comentario));
        return response()->json(["to"=>$request->email], 200);
    }

    public function comentarioEditado(Request $request){
        
        // Mail::to($request->user())->send(new CorreoRegistro());
        Mail::to($request->email)->send(new comentarioEditado($request->email));
        return response()->json(["to"=>$request->email], 200);
    }
    public function comentarioBorrado(Request $request){
        
        // Mail::to($request->user())->send(new CorreoRegistro());
        Mail::to($request->email)->send(new comentarioBorrado($request->email));
        return response()->json(["to"=>$request->email], 200);
    }
}
