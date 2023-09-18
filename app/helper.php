<?php

class Helper
{

    public static function save_file($file, $path)
    {
        $response = [];
        $imagen = $file;
        $target_path = $path;
        $target_path = $target_path . basename($imagen['name']);

        $enlace_actual = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        $enlace_actual = str_replace('index.php', '', $enlace_actual);

        $response = [];

        if (move_uploaded_file($imagen['tmp_name'], $target_path)) {
            $response = [
                'status' => true,
                'mensaje' => 'Fichero subido',
                'imagen' => $imagen['name'],
                'direccion' => $enlace_actual . '/' . $target_path,
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No se pudo guardar el fichero',
                'imagen' => null,
                'direccion' => null,
            ];
        }
        return ($response);
    }

    public function generate_key($limit){
        $key = '';

        $aux = sha1(md5(time()));
        $key = substr($aux, 0, $limit);

        return $key;
    }

    public static function mes($pos){
        $pos = intval($pos) -1;

        $array = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        return $array[$pos];
    }

    public static function invertir_array($array){

        $copia = [];

        for($i = 0; $i < count($array); $i++){
            $copia[] = $array[count($array) - $i  - 1];
        }

        return $copia;
    }

    public static function MESES(){
        $m = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        return $m;
    }

    public static function DIAS(){
        $d = [
            'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'
        ];
        return $d;
    }

    public static function hace_tiempo($fecha,$hora){
        $start_date = new DateTime($fecha." ".$hora);
        $since_start = $start_date->diff(new DateTime(date("Y-m-d")." ".date("H:i:s")));
        
        if($since_start->y==0){
            if($since_start->m==0){
                if($since_start->d==0){
                   if($since_start->h==0){
                       if($since_start->i==0){
                          if($since_start->s==0){
                             return $since_start->s.' segundos';
                          }else{
                              if($since_start->s==1){
                                return $since_start->s.' segundo'; 
                              }else{
                                return $since_start->s.' segundos'; 
                              }
                          }
                       }else{
                          if($since_start->i==1){
                              return $since_start->i.' minuto'; 
                          }else{
                            return $since_start->i.' minutos';
                          }
                       }
                   }else{
                      if($since_start->h==1){
                        return $since_start->h.' hora';
                      }else{
                        return $since_start->h.' horas';
                      }
                   }
                }else{
                    if($since_start->d==1){
                        return $since_start->d.' día';
                    }else{
                        return $since_start->d.' días';
                    }
                }
            }else{
                if($since_start->m==1){
                   return $since_start->m.' mes';
                }else{
                    return $since_start->m.' meses';
                }
            }
        }else{
            if($since_start->y==1){
                return $since_start->y.' año';
            }else{
                return $since_start->y.' años';
            }
        }
    }
}
