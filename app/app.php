<?php

require_once 'core/conexion.php';
require_once 'routes/url.php';
require_once 'routes/web.php';
require_once 'app/cors.php';

class App
{
    private $conexion;
    private $url;

    public function __construct()
    {
        $cors = new Cors();
        $cors->defualt();

        $this->conexion = new Conexion();
        $this->url = new Url();
    }

    public function run()
    {
        $ruta = $this->url->getRuta();

        if ($ruta == '/') {
            //cargar una documentacion en html
            include_once 'public/index.php';
        } else {
            $web = new Web();
            $web->lanzarRutas();
        }
    }
}
