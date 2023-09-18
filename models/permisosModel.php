<?php
require_once 'vendor/autoload.php';
require_once 'core/conexion.php';
require_once 'models/menusModel.php';
require_once 'models/rolesModel.php';

use Illuminate\Database\Eloquent\Model;

class Permisos extends Model{
    protected $table = 'permisos'; // nombre de la tabla
    protected $fillable = ['menus_id','roles_id','acceso','estado'];//atributos de las tablas
    public $timestamps = false;  //los created_at y updated_at los pones false

    //belongsTo => muchos a uno
    public function menus(){
        return $this->belongsTo(Menus::class);
    }

    //belongsTo => muchos a uno
    public function roles(){
        return $this->belongsTo(Roles::class);
    }

}