<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Index usuarios
Route::middleware('auth:sanctum')->get('/user', 'ApiAuth\AuthController@index');

//Logout
Route::middleware('auth:sanctum')->delete('/logout', 'ApiAuth\AuthController@logOut');
Route::middleware('auth:sanctum')->delete('/eliminartokens', 'ApiAuth\AuthController@eliminarTokens');

//Rutas comentarios
Route::middleware('auth:sanctum')->get('/indexc', 'ApiAuth\AuthController@indexcomments');
Route::middleware('auth:sanctum')->get("/indexc/{id}", "ApiAuth\AuthController@indexcomments")->where("id","[0-9]+");
Route::middleware('auth:sanctum')->get("/usuarios/{id}/comentarios", "ApiAuth\AuthController@comentariopersona");
Route::middleware('auth:sanctum')->post('/escribir', 'ApiAuth\AuthController@guardarcomentario');
Route::middleware('auth:sanctum')->put('/escribir/{id}', 'ApiAuth\AuthController@editarComentario');
Route::middleware('auth:sanctum')->delete('/borrarcom/{id}', 'ApiAuth\AuthController@destruirComentario');

//Rutas productos
Route::middleware('auth:sanctum')->get('/indexp', 'ApiAuth\AuthController@indexProductos');
Route::middleware('auth:sanctum')->get("/indexp/{id}", "ApiAuth\AuthController@indexProductos")->where("id","[0-9]+");
Route::middleware('auth:sanctum')->post('/guardarprod', 'ApiAuth\AuthController@guardarProducto');
Route::middleware('auth:sanctum')->put('/editarprod/{id}', 'ApiAuth\AuthController@editarProducto');
Route::middleware('auth:sanctum')->delete('/borrarprod/{id}', 'ApiAuth\AuthController@destruirProducto');

//Permisos
Route::middleware('auth:sanctum')->post('/permisoe', 'ApiAuth\AuthController@otorgarPermisosEscritura');

//Rutas Usuarios
Route::post('/registro', 'ApiAuth\AuthController@registro');
Route::post('/login', 'ApiAuth\AuthController@logIn')->middleware('login');
Route::middleware('auth:sanctum')->post('/perfil', 'ApiAuth\AuthController@editarPerfil');

//Rutas archivos
Route::middleware('auth:sanctum')->post('/archivo','ApiFile\FileController@guardarArchivo');

//Rutas Correos
Route::post('/correoinicio','ApiMail\mailController@enviaCorreo');
Route::post('/correopermiso','ApiMail\mailController@PermisoOtorgado');
Route::post('/permisoupdate','ApiMail\mailController@pedirPermiso');
Route::post('/comentariosalvado','ApiMail\mailController@comentarioSalvado');
Route::post('/usuariocomento','ApiMail\mailController@usuarioComento');
Route::post('/notificausuario','ApiMail\mailController@notificaUsuarios');
Route::post('/usuarioeditocomentario','ApiMail\mailController@comentarioEditado');
Route::post('/usuarioeliminocomentario','ApiMail\mailController@comentarioBorrado');
Route::get('/verificacion/{email}','ApiAuth\AuthController@Verificacion');