<?php
require_once 'vendor/autoload.php';
require_once 'core/conexion.php';

use Illuminate\Database\Eloquent\Model;

class Codigos extends Model{
    protected $table = 'codigos'; //nombre de la tabla
    protected $fillable = ['num_codigo','tipo','estado'];//atributos de las tablas
    public $timestamps = false;  //los created_at y updated_at los pones false
}