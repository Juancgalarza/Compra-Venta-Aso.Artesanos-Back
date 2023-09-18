<?php
require_once 'app/error.php';

class ProductosAccion
{
    public function index($metodo_http, $ruta, $params = null)
    {
        switch ($metodo_http) {
            case 'get':
                if ($ruta == '/productos/listarProductoId' && $params) {
                    Route::get('/productos/listarProductoId/:id', 'productosController@listarProductoxId',$params);
                }else
                if ($ruta == '/productos/mostrarCodigo' && $params) {
                    Route::get('/productos/mostrarCodigo/:tipo', 'productosController@mostrarCodigo',$params);
                }else
                if ($ruta == '/productos/listarProductoxIdMasCantidad' && $params) {
                    Route::get('/productos/listarProductoxIdMasCantidad/:id', 'productosController@listarProductoxIdMasCantidad',$params);
                }else
                if ($ruta == '/productos/listarProductoDataTable') {
                    Route::get('/productos/listarProductoDataTable', 'productosController@listarProductoDataTable');
                }else
                if ($ruta == '/productos/listarProducto') {
                    Route::get('/productos/listarProducto', 'productosController@listarProducto'); 
                }else//prueba
                if ($ruta == '/productos/listarProductoMayorStock') {
                    Route::get('/productos/listarProductoMayorStock', 'productosController@listarProductoMayorStock');
                }else//prueba2
                if ($ruta == '/productos/listarProductoMayorStockMasCantidad') {
                    Route::get('/productos/listarProductoMayorStockMasCantidad', 'productosController@listarProductoMayorStockMasCantidad');
                }else
                if ($ruta == '/productos/paginar' && $params) {
                    Route::get('/productos/paginar/:id', 'productosController@paginar',$params);
                }else
                if ($ruta == '/productos/buscar' && $params) {
                    Route::get('/productos/buscar/:texto', 'productosController@buscarProducto',$params);
                }else
                if($ruta == '/productos/contar'){
                    Route::get('/productos/contar', 'productosController@contar');
                }else
                if ($ruta == '/productos/graficoStockProductos') {
                    Route::get('/productos/graficoStockProductos', 'productosController@graficoStockProductos');
                }else {
                    ErrorClass::e(404, "La ruta no existe");
                }
            break;

            case 'post':
                if ($ruta == '/productos/guardarProducto') {
                    Route::post('/productos/guardarProducto', 'productosController@guardarProducto');
                }else
                if ($ruta == '/productos/subirFotoProducto') {
                    Route::post('/productos/subirFotoProducto', 'productosController@subirFotoProducto', true);
                }else
                if ($ruta == '/productos/eliminarProducto') {
                    Route::post('/productos/eliminarProducto', 'productosController@eliminarProducto');
                }else
                if ($ruta == '/productos/editarProducto') {
                    Route::post('/productos/editarProducto', 'productosController@editarProducto');
                }else
                if($ruta == '/productos/guardarCodigo'){
                    Route::post('/productos/guardarCodigo', 'productosController@guardarCodigos');
                }else
                if ($ruta == '/productos/agregarStock') {
                    Route::post('/productos/agregarStock', 'productosController@agregarStock');
                }else {
                    ErrorClass::e(404, "La ruta no existe");
                }
            break;
        }
    }
}
