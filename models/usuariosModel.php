<?php
require_once 'vendor/autoload.php';
require_once 'core/conexion.php';
require_once 'models/rolesModel.php';
require_once 'models/personasModel.php';
require_once 'models/ventasModel.php';

use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model{
    protected $table = 'usuarios'; // nombre de la tabla
    protected $hidden = ['clave','conf_clave']; //ocultar las credenciales o clave
    protected $fillable = ['roles_id','personas_id','usuario','correo','clave','conf_clave','imagen','estado'];//atributos de las tablas
    public $timestamps = false;  //los created_at y updated_at los pones false

    //belongsTo => muchos a uno
    public function roles(){
        return $this->belongsTo(Roles::class);
    }

    //belongsTo => muchos a uno
    public function personas(){
        return $this->belongsTo(Personas::class);
    }

    //hasMany =>uno a muchos
    public function ventas(){
        return $this->hasMany(Ventas::class);
    }

}