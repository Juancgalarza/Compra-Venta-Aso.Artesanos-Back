<?php

require_once 'app/error.php';

class Request
{

    private $data;
    private $put;

    public function __construct()
    {
    
    }

    public function getPost()
    {
        if (isset($_POST) && count($_POST) == 0) {
            ErrorClass::e('500', 'No se puede procesar porque no ha enviado parámetros por post');
        } else {
            $this->data = json_decode($_POST['data']);
        }
    }
    
    private function getPut(){
        
        $valor = json_decode(file_get_contents("php://input"));
        $this->put = $valor->data;
        
    }
    
    public function inputPut($nombreData){       
        $this->getPut();

        if(isset($this->put)){
            if(property_exists($this->put, $nombreData)){
                return $this->put->$nombreData;
            }else{
                ErrorClass::e('500', 'No se puede encontrar la propiedad en el request');
                return null;
            }
            return $this->put;
        }
        else{
            return null;
        }
    }

    public function input($nombreData)
    {
        if (isset($this->data->$nombreData)) {
            if (property_exists($this->data, $nombreData)) {
                return $this->data->$nombreData;
            } else {
                ErrorClass::e('500', 'No se puede encontrar la propiedad en el request');
                return null;
            }
        } else {
            return null;
        }
    }
}
