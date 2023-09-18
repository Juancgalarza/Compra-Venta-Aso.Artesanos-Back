<?php

require_once 'app/request.php';
require_once 'routes/url.php';
require_once 'app/error.php';
require_once 'app/request.php';

class Route
{

    private static function procesarControlador($ruta, $controller, $params = null, Request $request = null)
    {
        $array = explode('@', $controller);
        $controlador = $array[0];
        $metodo = $array[1];

        $file = "controllers/" . $controlador . ".php";

        if (file_exists($file)) {
            require_once $file;

            $class = ucfirst($controlador);
            if (class_exists($class)) {

                $instancia = new $class;
                if (method_exists($class, $metodo)) {
                    if ($request == null) {
                        if ($params) {
                            $instancia->$metodo($params);
                        } else {
                            $instancia->$metodo();
                        }
                    } else {
                        if ($params == null) {
                            $instancia->$metodo($request);
                        } else {
                            $instancia->$metodo($request, $params);
                        }
                    }
                } else {
                    ErrorClass::e('500', 'No existe el método => ' . $metodo . ' en la clase => ' . $class);
                }
            } else {
                ErrorClass::e('500', 'No existe la clase => ' . $class);
            }
        }
    }

    public static function get($ruta, $controller, $params = null)
    {

        $url = new Url();
        //Si no viene parametros
        if ($params == null) {
            Route::procesarControlador($ruta, $controller);
            
        } else {
            $arraynameParams = explode(':', $ruta);
            $nombresParametros = [];

            for ($i = 1; $i < count($arraynameParams); $i++) {
                if (strpos($arraynameParams[$i], '/')) {
                    $arraynameParams[$i] = substr($arraynameParams[$i], 0, -1);
                }
                array_push($nombresParametros, $arraynameParams[$i]);
            }

            if (count($nombresParametros) == count($params)) {
                $arrayFinal = [];

                for ($i = 0; $i < count($params); $i++) {
                    $arrayFinal[$nombresParametros[$i]] = $params[$i];
                }

                Route::procesarControlador($ruta, $controller, $arrayFinal);

            } else {
                echo "La cantidad de parametros no coincide con la url";
            }
        }
    }

    public static function post($ruta, $controller, $files = false)
    {
        $url = new Url();

        if ($url->getRuta() == $ruta) {
            if (!$files) {
                $request = new Request();
                $request->getPost();
                Route::procesarControlador($ruta, $controller, null, $request);
            } else {
                Route::procesarControlador($ruta, $controller, $_FILES, null);
            }
        }

    }

    public static function put($ruta, $controller, $params = null)
    {

        $request = new Request();

        if ($params == null) {

            Route::procesarControlador($ruta, $controller, null, $request);

        } else {

            $arraynameParams = explode(':', $ruta);
            $nombresParametros = [];

            for ($i = 1; $i < count($arraynameParams); $i++) {
                if (strpos($arraynameParams[$i], '/')) {
                    $arraynameParams[$i] = substr($arraynameParams[$i], 0, -1);
                }
                array_push($nombresParametros, $arraynameParams[$i]);
            }

            if (count($nombresParametros) == count($params)) {
                $arrayFinal = [];

                for ($i = 0; $i < count($params); $i++) {
                    $arrayFinal[$nombresParametros[$i]] = $params[$i];
                }

                Route::procesarControlador($ruta, $controller, $arrayFinal, $request);

            } else {
                echo "La cantidad de parametros no coincide con la url";
            }
        }
    }

    public static function delete($ruta, $controller, $params)
    {

        if ($params) {
            $arraynameParams = explode(':', $ruta);
            $nombresParametros = [];

            for ($i = 1; $i < count($arraynameParams); $i++) {
                if (strpos($arraynameParams[$i], '/')) {
                    $arraynameParams[$i] = substr($arraynameParams[$i], 0, -1);
                }
                array_push($nombresParametros, $arraynameParams[$i]);
            }

            if (count($nombresParametros) == count($params)) {
                $arrayFinal = [];

                for ($i = 0; $i < count($params); $i++) {
                    $arrayFinal[$nombresParametros[$i]] = $params[$i];
                }

                Route::procesarControlador($ruta, $controller, $arrayFinal);

            } else {
                echo "La cantidad de parametros no coincide con la url";
            }
        }
    }
}
