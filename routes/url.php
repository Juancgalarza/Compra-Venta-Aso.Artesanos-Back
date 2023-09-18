<?php

class Url{
    private $ruta;
    private $barra;
    private $array_url;
    private $controller;
    private $accion;
    private $params = [];

    public function __construct(){
        
        if(isset($_GET)){
            if(!isset($_GET['url'])){
                $this->barra = '/';
            }
            else{
                $this->ruta = $_GET['url'];
                $this->array_url = explode('/', $this->ruta);
            }
        }
    }

    public function getRuta(){

        if($this->barra == '/'){
            $this->controller = "error";
            $this->accion = "index";
            return $this->barra;
        }else{
            if(isset($this->array_url[0])){
                $this->barra = '/'.$this->array_url[0];
                $this->controller = strtolower($this->array_url[0]);
            }

            if(!isset($this->array_url[1])){
                $this->accion = "index";
                $this->barra = '/'.$this->array_url[0];
            }else
            if($this->array_url[1] == ''){
                $this->accion = "index";
                $this->barra = '/'.$this->array_url[0];
            }else
            if(isset($this->array_url[1])){
                $this->accion = strtolower($this->array_url[1]);
                $this->barra = '/'.$this->array_url[0].'/'.$this->array_url[1];
            } 

            if(count($this->array_url) > 2){
                for($i = 2; $i < count($this->array_url); $i++){
                    // echo $this->array_url[$i]."<br>";
                    $this->params[] = $this->array_url[$i];
                }
            }
            return $this->barra;
        }
    }

    public function getController(){
        return $this->controller;
    }

    public function getParams(){
        return $this->params;
    }

    public function getAccion(){
        return $this->accion;
    }

    public function getPeticion(){
        $peticion = $_SERVER['REQUEST_METHOD'];
        $peticion = strtolower($peticion);
        
        return $peticion;
    }
}