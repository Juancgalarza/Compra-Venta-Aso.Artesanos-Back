<?php
require_once 'app/cors.php';
require_once 'app/request.php';
require_once 'app/error.php';
require_once 'models/inventarioModel.php';

class InventarioController
{
    private $cors;

    public function __construct()
    {
        $this->cors = new Cors();
    }

    public function guardarIngresoProducto($id_Movimiento, $detalles = [], $tipo)
    {
        $response = [];  $extra = [];

        if (count($detalles) > 0) {
            foreach ($detalles as $item) {
                $nuevo = new Inventario();
                $productos_id = intval($item->productos_id);
                $movimientos_id = intval($id_Movimiento);
                $aux = intval($item->cantidad);
                $cantidad = ($tipo == 'E') ? $aux : ((-1) * $aux);

                $nuevo->productos_id = $productos_id;
                $nuevo->movimientos_id = $movimientos_id;
                $nuevo->tipo = $tipo;
                $nuevo->cantidad = $cantidad;

                //verifica si existe un registro anterior del producto
                $existe = Inventario::where('productos_id', $productos_id)->get()->count();

                if ($existe == 0) { //primer registro
                    $nuevo->cantidad_disponible = $cantidad; 
                    $extra = $this->tipo_inventario_first($tipo, $nuevo);
                } else { //segundo o mas registro registro
                    $extra = $this->tipo_inventario_mas_registro($tipo, $nuevo);
                }
            }
            $response = [
                'status' => true,
                'mensaje' => 'Inventario actualizado correctamente',
                'extra' => $extra,
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No se ha actualizado el inventario',
            ];
        }
        return $response;
    }

    private function tipo_inventario_first($tipo, Inventario $inventario)
    {
        $response = [];

        if ($tipo == 'E') {
            //guardar
            $inventario->save();

            $response = [
                'status' => true,
                'mensaje' => 'Primer movimiento del producto ' . $inventario->productos_id,
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay productos en stock disponible',
            ];
        }
        return $response;
    }

    private function tipo_inventario_mas_registro($tipo, Inventario $inventario)
    {
        $response = [];
        $ultimo = Inventario::where('productos_id', $inventario->productos_id)
                            ->orderBy('id', 'DESC')->get()->first();

        $cantidad = $inventario->cantidad + $ultimo->cantidad_disponible; //suma
        $inventario->cantidad_disponible = $cantidad;

        if ($tipo == 'S') { //salida
            $cantidad = ($inventario->cantidad * (-1)) - $ultimo->cantidad_disponible; //resta
            $inventario->cantidad_disponible = abs($cantidad);
        }

        if ($inventario->save()) {
            $response = [
                'status' => true,
                'mensaje' => 'Inventario actualizado ' . $inventario->productos_id,
                'inventario' => $inventario,
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No se pudo actualizar el inventario',
                'inventario' => $inventario,
            ];
        }
        return $response;
    }

    public function verInventario($params)
    {
        $this->cors->corsJson();
        $productos_id = intval($params['productos_id']);
        $inventario = Inventario::where('productos_id',$productos_id)->get();
        $data = [];  $i = 1;

        foreach ($inventario as $inv) {
            $inv->producto;
            $inv->movimientos;
            
            $entrada = [];  $salida = []; $tipo = ''; $fecha = date_format($inv->created_at,'Y-m-d');

            if($inv->movimientos->tipo_movimiento == 'E'){
                $entrada = [0 => $inv->cantidad]; $salida = [0 => '']; $tipo = 'Entrada';
            }else{
                $salida = [0 => abs($inv->cantidad)];  $entrada = [0 => '']; $tipo = 'Salida';
            }

            $data [] = [
                0 => $i,
                1 => $fecha,
                2 => $tipo,
                3 => $entrada[0],
                4 => $salida[0],
                5 => $inv->cantidad_disponible
            ];
            $i++;
        }
        $result = [
            'sEcho' => 1,
            'iTotalRecords' => count($inventario),
            'iTotalDisplayRecords' => count($inventario),
            'aaData' => $data,
        ];
        echo json_encode($result);
    }
}