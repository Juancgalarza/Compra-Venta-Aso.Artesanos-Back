<?php
require_once 'vendor/autoload.php';
require_once 'core/conexion.php';
require_once 'models/permisosModel.php';

use Illuminate\Database\Eloquent\Model;

class Menus extends Model{
    protected $table = 'menus'; // nombre de la tabla
    protected $fillable = ['seccion_id','menu','url','icono','posicion','estado'];//atributos de las tablas
    public $timestamps = false;  //los created_at y updated_at los pones false

    //hasMany => uno a muchos
    public function permisos(){
        return $this->hasMany(Permisos::class);
    }

}