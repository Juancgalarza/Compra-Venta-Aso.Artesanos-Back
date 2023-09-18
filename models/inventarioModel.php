<?php
require_once 'vendor/autoload.php';
require_once 'core/conexion.php';
require_once 'models/productosModel.php';
require_once 'models/movimientosModel.php';

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model{
    protected $table = 'inventario'; //nombre de la tabla
    protected $fillable = ['productos_id','movimientos_id','tipo','cantidad','cantidad_disponible'];//atributos de las tablas
   
    //belongsTo =>muchos a uno
    public function productos(){
        return $this->belongsTo(Productos::class);
    }

    //belongsTo =>muchos a uno
    public function movimientos(){
        return $this->belongsTo(Movimientos::class);
    } 
}