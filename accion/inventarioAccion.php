<?php

require_once 'app/error.php';

class InventarioAccion
{
    public function index($metodo_http, $ruta, $params = null)
    {
        switch ($metodo_http) {
            case 'get':
                if ($ruta == '/inventario/verInventario' && $params) {
                    Route::get('/inventario/verInventario/:productos_id', 'inventarioController@verInventario', $params);
                }else {
                    ErrorClass::e(404, "La ruta no existe");
                }
            break;

            case 'post':
                
            break;
        }
    }
}