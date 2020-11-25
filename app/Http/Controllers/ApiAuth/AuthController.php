<?php

namespace App\Http\Controllers\ApiAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Comentarios;
use App\Productos;
use app\Http\Middleware\inicioSesion;
use ComentariosTable;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiFile\FileController;
use App\Http\Controllers\ApiMail\mailController;
use DateTime;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function editarPerfil(Request $request)
    {
        $id = Auth::id();
        $response = new Response();
        if ($request->hasfile('archivo')) {
            $request->validate([
                'archivo'=>'required|mimes:jpeg,jpg,png,bmp,tiff,gif',
            ]);
            $response = app('App\Http\Controllers\ApiFile\FileController')->guardarArchivo($request)->getOriginalContent();
        }

        if ($request->user()->tokenCan('user:info') || $request->user()->tokenCan('admin:admin')) {
            $actualizar = new User();
            $actualizar = User::find($id);
            if ($response['path'] != null) {
                $actualizar->avatar = $response['path'];
            }
            $actualizar->save();
            if ($actualizar->save())
                return response()->json($actualizar, 200);


            return abort(422, "fallo al actualizar");
        }
    }

    public function indexcomments(Request $request, $id = null)
    {
        if ($request->user()->tokenCan('user:info') || $request->user()->tokenCan('admin:admin')) {
            if ($id)
                return response()->json(["comentario" => Comentarios::find($id)], 200);
            return response()->json(["comentarios" => Comentarios::all()], 200);
        }
        abort(401, "Scope invalido");
    }

    public function indexProductos(Request $request, $id = null)
    {
        if ($request->user()->tokenCan('user:info'))
            if ($id)
                return response()->json(["producto" => Productos::find($id)], 200);
        return response()->json(["Productos" => Productos::all()], 200);
    }

    public function comentariopersona(Request $request, $id)
    {
        if ($request->user()->tokenCan('admin:admin')){
            $comment = Comentarios::where('users_id', $id)->get();
            return response()->json($comment);
        }
        abort(401, "Scope invalido");
    }

    public function index(Request $request)
    {
        if ($request->user()->tokenCan('user:info') && $request->user()->tokenCan('admin:admin')){
            return response()->json(["users" => User::all()], 200);
        }
        if ($request->user()->tokenCan('user:info')){
            return response()->json(["perfil" => $request->user()], 200);
        }
        abort(401, "Scope invalido");
    }

    public function logOut(Request $request)
    {
        return response()->json(["afectados" => $request->user()->tokens()->delete()], 200);
    }

    public function logIn(Request $request)
    {           
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $verificar = DB::table('users')->where('email','=',$request->email)->first();
        if($verificar->email_verified_at != null){
        $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email|password' => ['Credenciales incorrectas...'],
                ]);
            }
        $token = $user->createToken($request->email, [$request->role])->plainTextToken;
        return response()->json(["token" => $token], 201);
        }
        return response()->json(["Aun no has verificado tu correo!", 404]);
    }

    public function eliminarTokens(Request $request)
    {
        if ($request->user()->tokenCan('admin:admin'))
            $userReq = new User();
        $userReq = $userReq->find($request->id);
        return response()->json(["afectados" => $userReq->tokens()->delete()], 200);
    }

    public function otorgarPermisosEscritura(Request $request)
    {
        if ($request->user()->tokenCan('admin:admin')){
            $request->validate([
                'email' => 'required|email'
            ]);
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => ['Usuario incorrecto...'],
                ]);
            }
        }
        $token = $user->createToken($request->email, ['user:post'])->plainTextToken;
        $mail = app('App\Http\Controllers\ApiMail\mailController')->PermisoOtorgado($request)->getOriginalContent();
        return response()->json(["token" => $token, "email" => $mail], 201);
    }

    public function guardarcomentario(Request $request)
    {
        $id = Auth::id();
        $user = new User();
        $user = User::find($id);
        if ($request->user()->tokenCan('user:info') || $request->user()->tokenCan('admin:admin'))
            $comm = new Comentarios();
        $comm->titulo = $request->titulo;
        $comm->comentario = $request->comentario;
        $comm->users_id = $id;
        $comm->producto_id = $request->producto;

        if ($comm->save()) {
            // envia correo usuario
            $request->merge(['email' => $user->email]);
            $mail = app('App\Http\Controllers\ApiMail\mailController')->comentarioSalvado($request)->getOriginalContent();
            // envia correo administrador
            $mail = app('App\Http\Controllers\ApiMail\mailController')->usuarioComento($request)->getOriginalContent();
            //$i = 0;
            //foreach($comments as $comment)
            //{
            //$user = User::find($comment->users_id);
            //$emails[$i] = $user->email; 
            //$i++;                                
            //}
            // envia email a todos los usuarios
            //$request->merge(['emails'=>$emails]);
            // return response()->json(["creador"=>$creador],201);
            // $user = User::find($creador->users_id);
            // envia comentario usuarios
            $creador =  DB::table('productos')->where('id', '=', $request->producto)->first();
            $userprod = DB::table('users')->where('id', '=', $creador->users_id)->first();
            $request->merge(['email' => $userprod->email]);
            $mail = app('App\Http\Controllers\ApiMail\mailController')->notificaUsuarios($request)->getOriginalContent();
            return response()->json(["comentario" => $comm], 201);
        }
        return response()->json(null, 400);
    }

    public function editarComentario(Request $request, $id)
    {
        $id = Auth::id();
        $user = new User();
        $user = User::find($id);
        if ($request->user()->tokenCan('user:info')) {
            $actualizar = new Comentarios();
            $actualizar = Comentarios::find($id);
            $actualizar->titulo = $request->get('titulo');
            $actualizar->comentario = $request->get('comentario');
            $actualizar->save();
            return response()->json(["comentario" => $actualizar], 201);
            $request->merge(['email' => $user->email]);
            $mail = app('App\Http\Controllers\ApiMail\mailController')->comentarioEditado($request)->getOriginalContent();
        }


        abort(401, "Scope invalido");
    }
    public function destruirComentario(Request $request, $id)
    {
        $id = Auth::id();
        $user = new User();
        $user = User::find($id);
        if ($request->user()->tokenCan('user:info')) {
            $borrar = new Comentarios();
            $borrar = Comentarios::find($id);
            $borrar->delete();
            $request->merge(['email' => $user->email]);
            $mail = app('App\Http\Controllers\ApiMail\mailController')->comentarioBorrado($request)->getOriginalContent();
        }
        return response()->json(["comentarios" => Comentarios::all()], 200);
    }
    public function guardarProducto(Request $request)
    {
        $id = Auth::id();
        $iduser = Auth::id();
        $user = new User();
        $user = user::find($iduser);
        if ($request->user()->tokenCan('user:post') || $request->user()->tokenCan('admin:admin')) {
            $prod = new Productos();
            $prod->nombre = $request->nombre;
            $prod->precio = $request->precio;
            $prod->usuario = $id;
            //$prod->usuario = 
            if ($prod->save()) {
                return response()->json(["producto" => $prod], 201);
            }
        }
        $request->merge(['email' => $user->email]);
        $mail = app('App\Http\Controllers\ApiMail\mailController')->pedirPermiso($request)->getOriginalContent();
        return response()->json(["Error, no cuentas con los permisos suficientes, se ha enviado un correo al administrador para que te sean otorgados", 404]);

        abort(401, "Scope invalido");
    }

    public function editarProducto(Request $request, $id)
    {
        $iduser = Auth::id();
        $user = new User();
        $user = user::find($iduser);
        if ($request->user()->tokenCan('user:post') || $request->user()->tokenCan('admin:admin')) {
            $actualizar = new Productos();
            $actualizar = Productos::find($id);
            $actualizar->nombre = $request->get('nombre');
            $actualizar->precio = $request->get('precio');
            $actualizar->save();
            return response()->json(["producto" => $actualizar], 201);
        }
        $request->merge(['email' => $user->email]);
        $mail = app('App\Http\Controllers\ApiMail\mailController')->pedirPermiso($request)->getOriginalContent();
        return response()->json(["Error, no cuentas con los permisos suficientes, se ha enviado un correo al administrador para que te sean otorgados", 404]);
    }
    public function destruirProducto(Request $request, $id)
    {
        if ($request->user()->tokenCan('admin:admin'))

            $borrar = new Productos();
        $borrar = Productos::find($id);
        $borrar->delete();
        return response()->json(["Productos" => Productos::all()], 200);
    }

    public function verificacion(Request $request, $email)
    {
        if ($email != null) {
            $user = new User();
            $verificar = DB::table('users')->where('email','=',$email)->first();
            if($verificar->email_verified_at == null){
                $dateTime =  new DateTime();
                $user = DB::table('users')->where('email', '=', $email)->update(['email_verified_at' => $dateTime]);
                return response()->json(['user' => $user], 200);
            }
            return response()->json(["tu correo ya ha sido verificado",202]);
        }
    }

    public function registro(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'required',
        ]);
        // $validatedData = $this->validate(
        //     $request,
        //     [
        //         'email' => 'required|email',
        //         'password' => 'required',
        //         'name' => 'required',
        //     ],
        //     [
        //         'required' => 'Please fill in the :attribute field'
        //     ]
        // );
        $user           = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        $user->avatar   = 0;
        $request->merge(['to' => $request->email]);

        if ($user->save())
            // enviar correo
            $mail = app('App\Http\Controllers\ApiMail\mailController')->enviaCorreo($request)->getOriginalContent();
        return response()->json(["user" => $user, "mail" => $mail], 200);

        return abort(422, "fallo al insertar");
    }
}
