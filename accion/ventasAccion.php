<?php
require_once 'app/error.php';

class VentasAccion
{
    public function index($metodo_http, $ruta, $params = null)
    {
        switch ($metodo_http) {
            case 'get':
                if ($ruta == '/ventas/listarxId' && $params) {
                    Route::get('/ventas/listarxId/:id', 'ventasController@listarxId',$params);
                }else
                if ($ruta == '/ventas/mostrarCodigo' && $params) {
                    Route::get('/ventas/mostrarCodigo/:tipo', 'ventasController@mostrarCodigo',$params);
                }else
                if ($ruta == '/ventas/mostrarCodigoVentaCliente' && $params) {
                    Route::get('/ventas/mostrarCodigoVentaCliente/:tipo', 'ventasController@mostrarCodigoVentaCliente',$params);
                }else
                if ($ruta == '/ventas/listar') {
                    Route::get('/ventas/listar', 'ventasController@listar');
                }else
                if ($ruta == '/ventas/listarDataTable') {
                    Route::get('/ventas/listarDataTable', 'ventasController@listarDataTable');
                }else
                if ($ruta == '/ventas/ventasmensuales' && $params) { 
                    Route::get('/ventas/ventasmensuales/:fecha_inicio/:fecha_fin', 'ventasController@reporteVentasMensuales',$params); 
                }else
                if ($ruta == '/ventas/ventasdiarias' && $params) { 
                    Route::get('/ventas/ventasdiarias/:fecha_inicio/:fecha_fin', 'ventasController@reporteVentasDiarias',$params); 
                }else  
                if ($ruta == '/ventas/totales') {
                    Route::get('/ventas/totales', 'ventasController@ventastotales2');
                }else
                if ($ruta == '/ventas/comprobantesVenta' && $params) { 
                    Route::get('/ventas/comprobantesVenta/:fecha_inicio/:fecha_fin', 'ventasController@comprobantesVenta',$params); 
                }else
                if ($ruta == '/ventas/graficatotalxmes') {
                    Route::get('/ventas/graficatotalxmes', 'ventasController@graficatotalxmesGeneralVentas'); 
                }else
                if ($ruta == '/ventas/graficaDiarias') {
                    Route::get('/ventas/graficaDiarias', 'ventasController@graficaVentasDiarias'); 
                }else
                if($ruta == '/ventas/masVendidos' && $params){
                    Route::get('/ventas/masVendidos/:fecha_inicio/:fecha_fin/:limite', 'ventasController@ventasFrecuentes',$params);  
                }else {
                    ErrorClass::e(404, "La ruta no existe");
                }
            break;

            case 'post':
                if ($ruta == '/ventas/guardarVenta') {
                    Route::post('/ventas/guardarVenta', 'ventasController@guardar');
                }else
                if($ruta == '/ventas/guardarCodigo'){
                    Route::post('/ventas/guardarCodigo', 'ventasController@guardarCodigo');
                }else
                if($ruta == '/ventas/guardarCodigoVentaCliente'){
                    Route::post('/ventas/guardarCodigoVentaCliente', 'ventasController@guardarCodigoVentaCliente');
                }else {
                    ErrorClass::e(404, "La ruta no existe");
                }    
            break;
        }
    }
}
