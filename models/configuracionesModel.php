<?php

require_once 'vendor/autoload.php';
require_once 'core/conexion.php';

use Illuminate\Database\Eloquent\Model;

class Configuraciones extends Model
{
    protected $table = "configuraciones";
    protected $filleable = ['iva', 'estado'];
    public $timestamps = false;

}