<?php
require_once 'app/error.php';

class ClientesAccion
{
    public function index($metodo_http, $ruta, $params = null)
    {
        switch ($metodo_http) {
            case 'get':
                if ($ruta == '/clientes/listarId' && $params) {
                    Route::get('/clientes/listarId/:id', 'clientesController@listarxId',$params);
                }else
                if ($ruta == '/clientes/listar') {
                    Route::get('/clientes/listar', 'clientesController@listar');
                }else
                if ($ruta == '/clientes/listarTable') {
                    Route::get('/clientes/listarTable', 'clientesController@listarTable');
                }else
                if ($ruta == '/clientes/listarClienteDataTableVenta') {
                    Route::get('/clientes/listarClienteDataTableVenta', 'clientesController@listarClienteDataTableVenta');
                }else
                if($ruta == '/clientes/contar'){
                    Route::get('/clientes/contar', 'clientesController@contar');
                }else {
                    ErrorClass::e(404, "La ruta no existe");
                }
            break;

            case 'post':
                if ($ruta == '/clientes/guardarCliente') {
                    Route::post('/clientes/guardarCliente', 'clientesController@guardar');
                }else
                if ($ruta == '/clientes/subirFoto') {
                    Route::post('/clientes/subirFoto', 'clientesController@subirFoto', true);
                }else
                if ($ruta == '/clientes/eliminarCliente') {
                    Route::post('/clientes/eliminarCliente', 'clientesController@eliminarCliente');
                }else
                if ($ruta == '/clientes/editarCliente') {
                    Route::post('/clientes/editarCliente', 'clientesController@editarCliente');
                }else {
                    ErrorClass::e('404', 'No se encuentra la url');
                }
                
            break;
        }
    }
}
