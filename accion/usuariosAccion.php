<?php
require_once 'app/error.php';

class UsuariosAccion{
    
    public function index($metodo_http, $ruta, $params = null){
        switch($metodo_http){
            case 'get':
                if($ruta == '/usuarios/listar' && $params){
                    Route::get('/usuarios/listar/:id', 'usuariosController@listarUsuarioId',$params);
                }else
                if($ruta == '/usuarios/listarUsuarioDataTable'){
                    Route::get('/usuarios/listarUsuarioDataTable', 'usuariosController@listarDataTable');
                }else
                if($ruta == '/usuarios/contar'){
                    Route::get('/usuarios/contar', 'usuariosController@contar');
                }else {
                    ErrorClass::e('404', 'No se encuentra la url');
                }
            break;
            
            case 'post':
                if($ruta == '/usuarios/login'){
                    Route::post('/usuarios/login', 'usuariosController@login');
                }else 
                if($ruta == '/usuarios/guardarUsuario'){
                    Route::post('/usuarios/guardarUsuario', 'usuariosController@guardarUsuario');
                }else
                if ($ruta == '/usuarios/subirFoto') {
                    Route::post('/usuarios/subirFoto', 'usuariosController@subirFoto', true);
                }else
                if ($ruta == '/usuarios/eliminarUsuario') {
                    Route::post('/usuarios/eliminarUsuario', 'usuariosController@eliminarUsuario');
                }else
                if ($ruta == '/usuarios/editarUsuario') {
                    Route::post('/usuarios/editarUsuario', 'usuariosController@editarUsuario');
                }else {
                    ErrorClass::e('404', 'No se encuentra la url');
                }
            break;

        }

    }
}
