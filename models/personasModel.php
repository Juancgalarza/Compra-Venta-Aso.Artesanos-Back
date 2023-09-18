<?php
require_once 'vendor/autoload.php';
require_once 'core/conexion.php';
require_once 'models/usuariosModel.php';
require_once 'models/clientesModel.php';

use Illuminate\Database\Eloquent\Model;

class Personas extends Model{
    protected $table = 'personas'; // nombre de la tabla
    protected $fillable = ['cedula','nombre','apellido','celular','direccion','estado'];//atributos de las tablas
    public $timestamps = false;  //los created_at y updated_at los pones false

    //hasMany => uno a muchos
    public function usuarios(){
        return $this->hasMany(Usuarios::class);
    }

    //hasMany => uno a muchos
    public function clientes(){
        return $this->hasMany(Clientes::class);
    }
}