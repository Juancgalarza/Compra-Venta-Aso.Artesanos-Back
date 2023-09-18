<?php
require_once 'vendor/autoload.php';
require_once 'core/conexion.php';
require_once 'models/productosModel.php';

use Illuminate\Database\Eloquent\Model;

class Categorias extends Model{
    protected $table = 'categorias'; // nombre de la tabla
    protected $fillable = ['nombre_categoria','estado'];//atributos de las tablas
    public $timestamps = false;  //los created_at y updated_at los pones false

    //hasMany => uno a muchos
    public function productos(){
        return $this->hasMany(Productos::class);
    }
}