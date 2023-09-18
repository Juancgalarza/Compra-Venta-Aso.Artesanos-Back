<?php
require_once 'vendor/autoload.php';
require_once 'core/conexion.php';
require_once 'models/categoriasModel.php';
require_once 'models/detalle_ventaModel.php';

use Illuminate\Database\Eloquent\Model;

class Productos extends Model{
    protected $table = 'productos'; // nombre de la tabla
    protected $fillable = ['categorias_id','codigo','nombre','descripcion','imagen','stock','precio','fecha','estado'];//atributos de las tablas
    public $timestamps = false;  //los created_at y updated_at los pones false

    //belongsTo => muchos a uno
    public function categorias(){
        return $this->belongsTo(Categorias::class);
    }

    //hasMany => uno a mucho
    public function detalle_venta(){
        return $this->hasMany(Detalle_Venta::class);
    }
}