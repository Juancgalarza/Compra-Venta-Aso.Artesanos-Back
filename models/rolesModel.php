<?php
require_once 'vendor/autoload.php';
require_once 'core/conexion.php';
require_once 'models/usuariosModel.php';
require_once 'models/permisosModel.php';

use Illuminate\Database\Eloquent\Model;

class Roles extends Model{
    protected $table = 'roles'; //nombre de la tabla
    protected $fillable = ['rol','estado'];//atributos de las tablas
    public $timestamps = false;  //los created_at y updated_at los pones false

    //hasMany => uno a muchos
    public function usuarios(){
        return $this->hasMany(Usuarios::class);
    }

    //hasMany => uno a muchos
    public function permisos(){
        return $this->hasMany(Permisos::class);
    }
}