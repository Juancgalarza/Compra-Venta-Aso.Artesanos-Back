<?php

require_once 'app/error.php';

class ConfiguracionesAccion
{
    public function index($metodo_http, $ruta, $params = null)
    {
        switch ($metodo_http) {
            case 'get':
                if ($ruta == '/configuraciones/listarConfiguracionesxId' && $params) {
                    Route::get('/configuraciones/listarConfiguracionesxId/:id', 'configuracionesController@listarConfiguracionesxId',$params);
                }else {
                    ErrorClass::e(404, "La ruta no existe");
                }
            break;

            case 'post':
                
            break;
        }
    }
}