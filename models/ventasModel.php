<?php
require_once 'vendor/autoload.php';
require_once 'core/conexion.php';
require_once 'models/usuariosModel.php';
require_once 'models/clientesModel.php';
require_once 'models/detalle_ventaModel.php';

use Illuminate\Database\Eloquent\Model;

class Ventas extends Model{
    protected $table = 'ventas'; //nombre de la tabla
    protected $fillable = ['usuarios_id','clientes_id','codigo','subtotal','iva','total','fecha_venta','estado'];//atributos de las tablas
    public $timestamps = false;  //los created_at y updated_at los pones false

    //belongsTo => muchos a uno
    public function usuarios(){
        return $this->belongsTo(Usuarios::class);
    }

    //belongsTo => muchos a uno
    public function clientes(){
        return $this->belongsTo(Clientes::class);
    }

    //hasMany => uno a mucho
    public function detalle_venta(){
        return $this->hasMany(Detalle_Venta::class);
    }
}