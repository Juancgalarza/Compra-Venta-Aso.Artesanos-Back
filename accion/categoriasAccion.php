<?php
require_once 'app/error.php';

class CategoriasAccion
{
    public function index($metodo_http, $ruta, $params = null)
    {
        switch ($metodo_http) {
            case 'get':
                if ($ruta == '/categorias/listarId' && $params) {
                    Route::get('/categorias/listarId/:id', 'categoriasController@listarXid', $params);
                }else
                if ($ruta == '/categorias/selectCategoria') {
                    Route::get('/categorias/selectCategoria', 'categoriasController@selectCategoria');
                }else
                if ($ruta == '/categorias/listarXidProducto' && $params) {
                    Route::get('/categorias/listarXidProducto/:id', 'categoriasController@listarXidInventario', $params);
                }else
                if ($ruta == '/categorias/listarCategoriasDataTable') {
                    Route::get('/categorias/listarCategoriasDataTable', 'categoriasController@listarCategoriasDataTable');
                }else {
                    ErrorClass::e(404, "La ruta no existe");
                }
            break;

            case 'post':
                if ($ruta == '/categorias/guardar') {
                    Route::post('/categorias/guardar', 'categoriasController@guardar');
                }else
                if ($ruta == '/categorias/eliminar') {
                    Route::post('/categorias/eliminar', 'categoriasController@eliminar');
                }else
                if ($ruta == '/categorias/editar') {
                    Route::post('/categorias/editar', 'categoriasController@editar');
                }else {
                    ErrorClass::e(404, "La ruta no existe");
                }
            break;
        }
    }
}