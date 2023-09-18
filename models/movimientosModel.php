<?php
require_once 'vendor/autoload.php';
require_once 'core/conexion.php';
require_once 'models/productosModel.php';
require_once 'models/ventasModel.php';
require_once 'models/movimientosModel.php';

use Illuminate\Database\Eloquent\Model;

class Movimientos extends Model{
    protected $table = 'movimientos'; //nombre de la tabla
    protected $fillable = ['tipo_movimiento','productos_id','ventas_id','fecha'];//atributos de las tablas
    public $timestamps = false;  //los created_at y updated_at los pones false

    //belongsTo => muchos a uno
    public function productos(){
        return $this->belongsTo(Productos::class);
    }

    //belongsTo => muchos a uno
    public function ventas(){
        return $this->belongsTo(Ventas::class);
    }

    //hasMany => uno a muchos
    public function inventario(){
        return $this->hasMany(Inventario::class);
    }
}