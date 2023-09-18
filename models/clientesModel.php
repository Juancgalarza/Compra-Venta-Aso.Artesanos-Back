<?php
require_once 'vendor/autoload.php';
require_once 'core/conexion.php';
require_once 'models/personasModel.php';

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model{
    protected $table = 'clientes'; // nombre de la tabla
    protected $fillable = ['personas_id','estado'];//atributos de las tablas
    public $timestamps = false;  //los created_at y updated_at los pones false

    //belongsTo => muchos a uno
    public function personas(){
        return $this->belongsTo(Personas::class);
    }
}