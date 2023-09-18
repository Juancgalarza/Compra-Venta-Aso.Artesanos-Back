<?php
require_once 'app/error.php';

class PermisosAccion{

    public function index($metodo_http, $ruta, $params = null){
        switch($metodo_http){
            case 'get':
                if($ruta == '/permisos/menu'){
                    Route::get('/permisos/menu', 'permisosController@menu');
                }else
                if ($ruta == '/permisos/rol' && $params) {
                    Route::get('/permisos/rol/:id', 'permisosController@permisos', $params);
                }else
                if ($ruta == '/permisos/listarPermiso'){
                    Route::get('/permisos/listarPermiso', 'permisosController@listarPermiso'); 
                }else
                if( $ruta == '/permisos/mostrarPermisoRol' && $params){
                    Route::get('/permisos/mostrarPermisoRol/:rol_id', 'permisosController@mostrarPermisoRol', $params);
                }else {
                    ErrorClass::e('404', 'No se encuentra la url');
                }
            break;
            
            case 'post':
                if( $ruta == '/permisos/otorgarPermiso'){
                    Route::post('/permisos/otorgarPermiso', 'permisosController@otorgarPermiso');
                }else {
                    ErrorClass::e('404', 'No se encuentra la url');
                }
                
            break;
        }

    }
}
