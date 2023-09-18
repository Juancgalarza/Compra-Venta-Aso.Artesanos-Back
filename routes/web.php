<?php

require_once 'routes/url.php';
require_once 'routes/route.php';

class Web{

    private $url;

    public function __construct(){
        $this->url = new Url();
    }

    //Configurar las rutas de la api
    public function lanzarRutas(){

        $ruta = $this->url->getRuta();
        
        //Instanciarlos por controlador
        $claseAccion = $this->url->getController();
    
        $fileClass  = 'accion/'.$claseAccion."Accion.php";
        if(file_exists($fileClass)){
            include_once $fileClass;

            $class = ucfirst($claseAccion)."Accion";
            
            if(class_exists($class)){
                $instancia = new $class();
                $params =  $this->url->getParams();
                $instancia->index($this->url->getPeticion(), $this->url->getRuta(), $params);
            }else{
                echo "No existe la clase";
            }   
        }else{
            echo "<br>No existe el archivo ".$fileClass."<br>";
        }                
    }
}