<?php
require_once 'app/cors.php';
require_once 'app/request.php';
require_once 'app/error.php';
require_once 'models/detalle_ventaModel.php';
require_once 'models/productosModel.php';

class Detalle_VentaController
{
    private $cors;

    public function __construct()
    {
        $this->cors = new Cors();
    }

    public function guardar($ventas_id, $detalles=[]){
        $response = [];
        
        if(count($detalles) > 0){
            foreach($detalles as $det){
                $nuevaventadetalle = new Detalle_Venta();
                $nuevaventadetalle->ventas_id = intval($ventas_id);
                $nuevaventadetalle->productos_id = intval($det->productos_id);
                $nuevaventadetalle->cantidad = intval($det->cantidad);
                $nuevaventadetalle->precio = doubleval($det->precio);
                $nuevaventadetalle->total = doubleval($det->total);
                $nuevaventadetalle->save();

                $stockProd = $nuevaventadetalle->cantidad * (-1);
                $this->actualizarProducto($det->productos_id, $stockProd);
            }

            $detalleguardar=Detalle_Venta::where('ventas_id',$ventas_id)->get();
            $response=[
                'status'=>true,
                'mensaje'=>'Se guardaron los productos',
                'detalle_venta'=>$detalleguardar
            ];
        }else{
            $response=[
                'status'=>false,
                'mensaje'=>'No hay productos para guardar',
                'detalle_venta'=>null
            ];
        }
        return $response;
    }

    protected function actualizarProducto($productos_id,$stock){
        $producto = Productos::find($productos_id);
        $producto->stock += $stock;
        $producto->save();

    }
}