<?php
require_once 'vendor/autoload.php';
require_once 'core/conexion.php';
require_once 'models/ventasModel.php';
require_once 'models/productosModel.php';

use Illuminate\Database\Eloquent\Model;

class Detalle_Venta extends Model{
    protected $table = 'detalle_venta'; // nombre de la tabla
    protected $fillable = ['ventas_id','productos_id','cantidad','precio','total'];//atributos de las tablas
    public $timestamps = false;  //los created_at y updated_at los pones false

    //belongsTo => muchos a uno
    public function ventas(){
        return $this->belongsTo(Ventas::class);
    }

    //belongsTo => muchos a uno
    public function productos(){
        return $this->belongsTo(Productos::class);
    }
}