<?php

require_once 'app/error.php';

class RolesAccion
{
    public function index($metodo_http, $ruta, $params = null)
    {
        switch ($metodo_http) {
            case 'get':
                if ($ruta == '/roles/listar') {
                    Route::get('/roles/listar', 'rolesController@selectRol');
                }else if ($ruta == '/roles/listarNoCliente') {
                    Route::get('/roles/listarNoCliente', 'rolesController@selectRolSinCliente');
                }else {
                    ErrorClass::e(404, "La ruta no existe");
                }
            break;

            case 'post':
                
            break;
        }
    }
}