<?php
require_once 'app/cors.php';
require_once 'app/request.php';
require_once 'app/error.php';
require_once 'core/conexion.php';
require_once 'models/menusModel.php';
require_once 'models/permisosModel.php';

class PermisosController{

    private $cors;

    public function __construct()
    {
        $this->cors = new Cors();
    }

    public function menu()
    {
        $this->cors->corsJson();
        $menus = $this->menusPadres();
        $response = [];

        if ($menus) {
            $response = [
                'status' => true,
                'menu_padre' => $menus,
            ];
        } else {
            $response = [
                'status' => true,
                'menu_padre' => [],
            ];
        }
        echo json_encode($response);
    }

    private function menusPadres()
    {
        $menus = Menus::where('estado', 'A')->where('seccion_id', '0')->get();
        if ($menus) {
            return $menus;
        } else {
            return false;
        }
    }

    public function permisos($params)
    {
        $this->cors->corsJson();
        $id_rol = intval($params['id']);
        $accesos = Permisos::where('roles_id', $id_rol)->where('acceso', 'S')->get();
        $response = [];

        if (count($accesos) > 0) {
            $menus_padres = [];  $menus_hijos = [];  $menusPadresOrdenadosAccesos = [];  $menuFinal = [];

            $bdMenusPadres = Menus::where('seccion_id', 0)
                                    ->where('estado', 'A')
                                    ->orderBy('posicion')
                                    ->get();

            //Separar menus padres de hijos que tienen acceso
            foreach ($accesos as $item) {
                $aux = [
                    'id' => $item->menus->id,
                    'nombre' => $item->menus->menu,
                    'icono' => $item->menus->icono,
                    'url' => $item->menus->url,
                    'seccion_id' => $item->menus->seccion_id,
                ];

                if ($item->menus->seccion_id == 0) {
                    $menus_padres[] = $aux;
                } else {
                    $menus_hijos[] = $aux;
                }
            }

            //Ordenar los menus padres solo con acceso
            foreach ($bdMenusPadres as $ordenados) {
                foreach ($menus_padres as $desorden) {
                    if ($ordenados->id === $desorden['id']) {
                        $menusPadresOrdenadosAccesos[] = (object) $desorden;
                    }
                }
            }

            foreach ($menusPadresOrdenadosAccesos as $padre) {
                $menus_hijos_ordenados = Menus::where('estado', 'A')
                                            ->where('seccion_id', $padre->id)
                                            ->orderBy('posicion')
                                            ->get();

                $hijos_ordenados = [];
                $auxFinal['id'] = $padre->id;
                $auxFinal['nombre'] = $padre->nombre;
                $auxFinal['icono'] = $padre->icono;
                $auxFinal['url'] = $padre->url;

                if (count($menus_hijos_ordenados) > 0) {
                    foreach ($menus_hijos_ordenados as $ordenado) {
                        foreach ($menus_hijos as $desorden) {
                            if ($desorden['id'] === $ordenado->id) {
                                $hijos_ordenados[] = (object) $desorden;
                            }
                        }
                    }
                    $auxFinal['menus_hijos'] = $hijos_ordenados;
                } else {
                    $auxFinal['menus_hijos'] = [];
                }
                $menuFinal[] = $auxFinal;
            }

            $response = [
                'status' => true,
                'mensaje' => 'Hay información',
                'menus' => $menuFinal,
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay menus para el rol',
                'menus' => [],
            ];
        }
        echo json_encode($response['menus']);

    }

    private function mostrarMenus($id_seccion)
    {
        $menus = Menus::where('estado', 'A')->where('seccion_id', $id_seccion)->get();

        if ($menus) {
            return $menus;
        } else {
            return false;
        }
    }

    public function listarPermiso()
    {
        $this->cors->corsJson();
        $dataMenus = $this->mostrarMenus('0');
        $ressponse = [];

        for($i = 0; $i < count($dataMenus); $i++){
            $hijos = $this->mostrarMenus($dataMenus[$i]->id);
            $padre = $dataMenus[$i];

            $object = [
                'padre' => $padre,
                'hijos' => $hijos
            ];
            $ressponse[] = (object)$object;
        }
        echo json_encode($ressponse);
    }

    public function mostrarPermisoRol($params)
    {
        $this->cors->corsJson();
        $rol_id = intval($params['rol_id']);
        $dataPermisos = Permisos::where('estado','A')->where('roles_id',$rol_id)->get();

        echo json_encode($dataPermisos);

    }

    public function otorgarPermiso(Request $request)
    {
        $permisoRequest = $request->input('permiso');
        $roles_id = intval($permisoRequest->roles_id);
        $menus_id = intval($permisoRequest->menus_id);
        $response = [];
        
        if($permisoRequest->permiso == 'N'){
            $permisos_id = intval($permisoRequest->permisos_id);

            $permiso = Permisos::find($permisos_id);
            $permiso->delete();
            $response = [
                'status' => true,
                'mensaje' => 'Se ha eliminado el acceso'
            ];
        }else{
            $nuevo = new Permisos;
            $nuevo->roles_id = $roles_id;
            $nuevo->menus_id = $menus_id;
            $nuevo->acceso = 'S';
            $nuevo->estado = 'A';
            $nuevo->save();

            $response = [
                'status' => true,
                'mensaje' => 'Se ha otorgado el acceso'
            ];
        }
        echo json_encode($response);
    }
}