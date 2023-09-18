<?php

require_once 'vendor/autoload.php';
require_once 'params.php';

use Illuminate\Database\Capsule\Manager as Database;

class Conexion
{

    public $database;

    public function __construct()
    {
        $this->database = new Database;

        $this->database->addConnection([
            'driver' => DRIVER,
            'host' => HOST,
            'database' => DATABASE,
            'username' => USERNAME,
            'password' => PASSWORD,
            'charset' => CHARSET,
            'collation' => COLLACTION,
            'prefix' => '',
        ]);

        $this->database->setAsGlobal();

        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $this->database->bootEloquent();
        //echo "Clase conexion";
    }

    public function getDatabse()
    {
        return $this->datadatabase;
    }
}
