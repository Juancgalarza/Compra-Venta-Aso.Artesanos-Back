<?php
require_once 'core/conexion.php';
require_once 'app/helper.php';
require_once 'app/cors.php';
require_once 'app/request.php';
require_once 'app/error.php';
require_once 'models/ventasModel.php';
require_once 'models/codigosModel.php';
require_once 'models/productosModel.php';
require_once 'controllers/detalle_ventaController.php';
require_once 'models/movimientosModel.php';
require_once 'controllers/inventarioController.php';

class VentasController
{
    private $cors;
    private $conexion;

    public function __construct()
    {
        $this->cors = new Cors();
        $this->conexion = new Conexion();
    }

    public function listarxId($params)
    {
        $this->cors->corsJson();
        $id = intval($params['id']);
        $response = [];

        $ventas = Ventas::find($id);

        if($ventas){
            $ventas->usuarios->personas;
            $ventas->clientes->personas;

            foreach($ventas->detalle_venta as $dv){
                $dv->productos->categorias;
            }
            
            $response = [
                'status' => true,
                'mensaje' => 'existen datos',
                'venta' => $ventas,
            ];
        }else{
            $response = [
                'status' => false,
                'mensaje' => 'no existen datos',
                'venta' => null,
            ];
        }
        echo json_encode($response);
    }

    public function mostrarCodigo($params)
    {
        $this->cors->corsJson();
        $tipo = $params['tipo'];
        $serie = Codigos::where('tipo', $tipo)->orderBy('id', 'DESC')->first();
        $response = [];

        if ($serie == null) {
            $response = [
                'status' => true,
                'tipo' => $tipo,
                'mensaje' => 'Primer codigo',
                'codigo' => 'V0001',
            ];
        } else {
            $numero = substr($serie->num_codigo,1);
            $siguiente = 'V000' . ($numero += 1);
            $response = [
                'status' => true,
                'tipo' => $tipo,
                'mensaje' => 'Existen datos, aumentando codigo de la venta',
                'codigo' => $siguiente,
            ];
        }
        echo json_encode($response);
    }

    public function mostrarCodigoVentaCliente($params)
    {
        $this->cors->corsJson();
        $tipo = $params['tipo'];
        $serie = Codigos::where('tipo', $tipo)->orderBy('id', 'DESC')->first();
        $response = [];

        if ($serie == null) {
            $response = [
                'status' => true,
                'tipo' => $tipo,
                'mensaje' => 'Primer codigo',
                'codigo' => 'VCL0001',
            ];
        } else {
            $numero = substr($serie->num_codigo,3);
            $siguiente = 'VCL000' . ($numero += 1);
            $response = [
                'status' => true,
                'tipo' => $tipo,
                'mensaje' => 'Existen datos, aumentando codigo de la venta',
                'codigo' => $siguiente,
            ];
        }
        echo json_encode($response);
    }

    public function guardarCodigo(Request $request) 
    {
        $this->cors->corsJson();
        $serieRequest = $request->input('codigo');
        $num_codigo = $serieRequest->num_codigo;
        $tipo = $serieRequest->tipo;
        $response = [];

        if ($serieRequest == null) {
            $response = [
                'status' => false,
                'mensaje' => 'no ahi datos',
            ];
        } else {
            $nuevoCoidgo = new Codigos();
            $nuevoCoidgo->num_codigo = $num_codigo;
            $nuevoCoidgo->tipo = $tipo;
            $nuevoCoidgo->estado = 'A';
            $nuevoCoidgo->save();

            $response = [
                'status' => true,
                'mensaje' => 'Guardando datos',
                'codigo' => $nuevoCoidgo,
            ];
        }
        echo json_encode($response);
    }

    public function guardarCodigoVentaCliente(Request $request) 
    {
        $this->cors->corsJson();
        $serieRequest = $request->input('codigo');
        $num_codigo = $serieRequest->num_codigo;
        $tipo = $serieRequest->tipo;
        $response = [];

        if ($serieRequest == null) {
            $response = [
                'status' => false,
                'mensaje' => 'no ahi datos',
            ];
        } else {
            $nuevoCoidgo = new Codigos();
            $nuevoCoidgo->num_codigo = $num_codigo;
            $nuevoCoidgo->tipo = $tipo;
            $nuevoCoidgo->estado = 'A';
            $nuevoCoidgo->save();

            $response = [
                'status' => true,
                'mensaje' => 'Guardando datos',
                'codigo' => $nuevoCoidgo,
            ];
        }
        echo json_encode($response);
    }

    public function guardar(Request $request)
    {
        $this->cors->corsJson();
        $ventarequest = $request->input('venta');
        $detallesventa = $request->input('detalle_venta');

        $codigo = $ventarequest->codigo;
        $response = [];

        if($ventarequest){
            $ventarequest->usuarios_id = intval($ventarequest->usuarios_id);
            $ventarequest->clientes_id = intval($ventarequest->clientes_id);
            $ventarequest->codigo = $ventarequest->codigo;
            $ventarequest->subtotal = doubleval($ventarequest->subtotal);
            $ventarequest->iva = doubleval($ventarequest->iva);
            $ventarequest->total = doubleval($ventarequest->total);

            $nuevaventa = new Ventas();
            $nuevaventa->usuarios_id = $ventarequest->usuarios_id;
            $nuevaventa->clientes_id = $ventarequest->clientes_id; 
            $nuevaventa->codigo = $ventarequest->codigo; 
            $nuevaventa->subtotal = $ventarequest->subtotal;
            $nuevaventa->iva = $ventarequest->iva; 
            $nuevaventa->total = $ventarequest->total; 
            $nuevaventa->fecha_venta = date('Y-m-d'); 
            $nuevaventa->estado = 'A';
            
            $existeCodigo = Ventas::where('codigo',$codigo)->get()->first();

            if ($existeCodigo) {
                $response = [
                    'status' => false,
                    'mensaje' => 'El código de la venta ya existe',
                    'compra' => null,
                    'detalle' => null,
                ];
            }else{
                if($nuevaventa->save()){

                    $detalleController = new Detalle_VentaController();
                    $extra = $detalleController->guardar($nuevaventa->id, $detallesventa);

                    //insertar en la tabla movimientos
                    $nuevoMovimiento = $this->nuevoMovimiento($nuevaventa);

                    //INSERTAR EN LA TABLA INVENTARIO
                    $inventariocontroller = new InventarioController();
                    $responseInventario = $inventariocontroller->guardarIngresoProducto($nuevoMovimiento->id, $detallesventa, 'S');

                    $response = [
                        'status' => true,
                        'mensaje' => 'La venta se genero correctamente',
                        'venta' => $nuevaventa,
                        'detalle' => $extra,
                        'movimientos' => $nuevoMovimiento,
                        'inventario' => $responseInventario
                    ];
                }else {
                    $response = [
                        'status' => false,
                        'mensaje' => 'La venta no se puede guardar',
                        'venta' => null,
                        'detalle' => null,
                    ];
                }
            }
        }else{
            $response = [
                'status' => false,
                'mensaje' => 'No hay datos para procesar',
                'venta' => null,
                'detalle' => null,
            ];
        }
        echo json_encode($response);
    }

    public function reporteVentasMensuales($params)
    {
        $this->cors->corsJson();
        $fecha_inicio = $params['fecha_inicio'];
        $fecha_fin = $params['fecha_fin'];
        $meses = Helper::MESES();

        $fecha_inicio = new DateTime($fecha_inicio);
        $fecha_fin = new DateTime($fecha_fin);

        $mes_inicio = intval(explode('-', $params['fecha_inicio'])[1]);
        $mes_fin = intval(explode('-', $params['fecha_fin'])[1]);

        $data = [];  $label = [];  $datatotal = [];  $dataiva = [];  $datasubtotal = [];
        $arrayTodo = []; $arrayFinalData = []; $labelFinalData = [];
        $totalgeneral = 0;  $ivageneral = 0;  $subtotalgeneral = 0;

        for ($i = $mes_inicio; $i <= $mes_fin; $i++) {
            $sql = "SELECT SUM(total) as total, SUM(subtotal) as subtotal , SUM(iva) as iva, fecha_venta FROM ventas where MONTH(fecha_venta) =($i) and estado ='A'";
            
            $ventamensuales = $this->conexion->database::select($sql)[0];

            //ventas
            $subtotal = (isset($ventamensuales->subtotal)) ? (round($ventamensuales->subtotal, 2)) : 0;
            $iva = (isset($ventamensuales->iva)) ? (round($ventamensuales->iva, 2)) : 0;
            $total = (isset($ventamensuales->total)) ? (round($ventamensuales->total, 2)) : 0;
            $fecha = (isset($ventamensuales->fecha_venta)) ? $ventamensuales->fecha_venta : '-';
            $mes = $meses[$i - 1];

            $arrayTodo = [
                $subtotal,
                $iva,
                $total
            ];

            //ventas 
            $aux = ['fecha' => $fecha, 'mes' => $mes, 'subtotal' => $subtotal, 'iva' => $iva, 'total' => $total];
            $aux2 = ['meses' => $meses[$i - 1], 'data' => $aux];
            $data[] = $aux2;
            $label[] = ucfirst($meses[$i - 1]);
            $datatotal[] = $total;
            $dataiva[] = $iva;
            $datasubtotal[] = $subtotal;
            $totalgeneral += $total;
            $ivageneral += $iva;
            $subtotalgeneral += $subtotal;

            $aux = [
                'name' =>$meses[$i - 1],
                'data' => $arrayTodo,
            ];
            array_push($arrayFinalData, $aux);
        }
        $ivageneral = round($ivageneral, 2);
        $labelFinalData = [
            'Subtotal',
            'IVA',
            'Total'
        ];

        $response = [ 
            'lista' => $data,
            'totales' => [
                'total' => $totalgeneral,
                'iva' => $ivageneral,
                'subtotal' => $subtotalgeneral,
            ],
            'barra' => [
                'labels' => $label,
                'datatotal' => $datatotal,
                'datasubtotal' => $datasubtotal,
                'dataiva' => $dataiva,
            ],
            'labels' => $labelFinalData,
            'dataFinal' => $arrayFinalData
        ];
        echo json_encode($response);
    }

    public function listarDataTable()
    {
        $this->cors->corsJson();
        $ventas = Ventas::where('estado','A')->orderBy('id','Desc')->get();
        $data = []; $i = 1;

        foreach($ventas as $v){
            $codigo = $v->codigo;
            $cliente = $v->clientes->personas;
            $total = $v->total;
            $fecha_venta = $v->fecha_venta;

            $botones = '<div>
                            <button class="btn bg-purple btn-sm" onclick="verComprobante(' . $v->id . ')">
                                <i class="fas fa-file-alt fa-lg"></i>
                            </button>
                        </div>';
            
            $data[] = [
                0 => $i,
                1 => $codigo,
                2 => $cliente->nombre . ' ' .$cliente->apellido,
                3 => $total,
                4 => $fecha_venta,
                5 => $botones
            ];
            $i++;
        }
        $result = [
            'sEcho' => 1,
            'iTotalRecords' => count($data),
            'iTotalDisplayRecords' => count($data),
            'aaData' => $data,
        ];
        echo json_encode($result);
    }

    public function ventastotales2()
    {
        $this->cors->corsJson();
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $posMes = intval(date('m')) - 1;
        $hoy = date('Y-m-d');
        $inicio_mes = date('Y') . '-' . date('m') . '-01';

        $ventas = Ventas::where('estado', 'A')
            ->where('fecha_venta', '>=', $inicio_mes)
            ->where('fecha_venta', '<=', $hoy)->get();

        $response = [];
        $total = 0;

        if ($ventas) {
            foreach ($ventas as $v) {
                $aux = $total += $v->total;
                $total = round($aux, 2);
            }
            $response = [
                'status' => true,
                'mensaje' => 'Existen datos',
                'total' => $total,
                'mes' => $meses[$posMes],
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No Existen datos',
                'total' => 0,
                'mes' => $meses[$posMes],
            ];
        }
        echo json_encode($response);
    }

    public function comprobantesVenta($params)
    {
        $this->cors->corsJson();
        $fecha_inicio = $params['fecha_inicio'];
        $fecha_fin = $params['fecha_fin'];
        $response = [];
        $dataFinalGraficaSubtotal = []; $dataFinalGraficaIva = []; $dataFinalGraficaTotal = [];
        $dataSub = 0; $dataIva = 0; $dataTotal = 0;

        $ventas = Ventas::where('fecha_venta', '>=', $fecha_inicio)
        ->where('fecha_venta', '<=', $fecha_fin)->where('estado', 'A')->get();

        if(count($ventas) > 0){
            foreach ($ventas as $v) {
                $v->usuarios->personas;
                $v->clientes->personas;
                foreach($v->detalle_venta as $dv){
                    $dv->productos->categorias;
                }

                $dataSub += $v->subtotal;
                $dataVenta = ($dataSub) ? round($dataSub, 2) : 0;
                $dataFinalGraficaSubtotal = [
                    'name' => 'Subtotal',
                    'data' => (array)$dataVenta,
                ];
                $dataIva += $v->iva;
                $dataVentaIva = ($dataIva) ? round($dataIva, 2) : 0;
                $dataFinalGraficaIva = [
                    'name' => 'IVA',
                    'data' => (array)$dataVentaIva,
                ];
                $dataTotal += $v->total;
                $dataVentaTotal = ($dataTotal) ? round($dataTotal, 2) : 0;
                $dataFinalGraficaTotal = [
                    'name' => 'Total',
                    'data' => (array)$dataVentaTotal,
                ];
            }

            $response = [
                'status' => true,
                'mensaje' => 'existen datos',
                'venta' => $ventas,
                'dataGrafica' => [
                    $dataFinalGraficaSubtotal,
                    $dataFinalGraficaIva,
                    $dataFinalGraficaTotal
                ]
            ];
        }else{
            $response = [
                'status' => false,
                'mensaje' => 'no existen datos',
                'venta' => null,
            ];
        }
        echo json_encode($response);
    }

    public function graficatotalxmesGeneralVentas()
    {
        $this->cors->corsJson();
        $meses = Helper::MESES();
        $response = [];  $dataVenta = [];   $year = date('Y');  $data = [];

        for ($i=0; $i < count($meses); $i++) { 
            $ventas = Ventas::where('estado','A')->whereYear('fecha_venta', '=', $year)
                            ->whereMonth('fecha_venta','=',$i+1)->get()->sum('total');
            $dataVenta = ($ventas > 0) ? round($ventas,2) : 0;

            $aux = [
                $meses[$i],
                $dataVenta,
            ];
            array_push($data, $aux);
        }
        $response = [
            'data' =>$data,
            'anio' => $year
        ];
        echo json_encode($response);
    }

    public function graficaVentasDiarias()
    {
        $this->cors->corsJson();
        $year = date('Y');

        $dias = [
            'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'
        ];
        $data = [];

        //Obtener total de las ordenes
        for ($i = 0; $i < count($dias); $i++) {
            $sql = "SELECT IFNULL(SUM(total), 0) as suma FROM `ventas` WHERE WEEKDAY(fecha_venta + 1) = ($i + 1) AND YEAR(fecha_venta) = YEAR(curdate()) AND estado = 'A'";

            $ventas = $this->conexion->database::select($sql);

            $dataVenta = ($ventas[0]->suma) ? round($ventas[0]->suma, 2) : 0;

            $aux = [
                $dias[$i],
                $dataVenta,
            ];
            array_push($data, $aux);
        }
        $response = [
            'data' =>$data,
            'anio' => $year
        ];
        echo json_encode($response);
    }

    public function ventasFrecuentes($params){
        $this->cors->corsJson();
        $inicio = $params['fecha_inicio'];
        $fin = $params['fecha_fin'];
        $limit = intval($params['limite']);

        $ventas = Ventas::where('fecha_venta', '>=', $inicio)
            ->where('fecha_venta', '<=', $fin)
            ->where('estado', 'A')
            ->take($limit)->get();

        $productos_id = []; //array principal
        $secundario = [];

        foreach ($ventas as $item) {
            $item->detalle_venta; //array
            foreach ($item->detalle_venta as $detalle) {

                $aux = [
                    'id' => $detalle->productos_id,
                    'cantidad' => $detalle->cantidad,
                ];

                $productos_id[] = (object) $aux;
                $secundario[] = $detalle->productos_id;
            }
        }

        $no_repetidos = array_values(array_unique($secundario));
        $nuevo_array = [];
        $contador = 0;

        //Algoritmo para contar y eliminar los elementos repetidos de un array
        for ($i = 0; $i < count($no_repetidos); $i++) {
            foreach ($productos_id as $item) {
                if ($item->id === $no_repetidos[$i]) {
                    $contador += $item->cantidad;
                }
            }
            $aux = [
                'producto_id' => $no_repetidos[$i],
                'cantidad' => $contador,
            ];

            $contador = 0;
            $nuevo_array[] = (object) $aux;
            $aux = [];
        }

        $array_productos = $this->ordenar_array($nuevo_array);
        $array_productos = Helper::invertir_array($array_productos);

        $array_seudoFinal = [];
        //Recortar segun limite
        if (count($array_productos) < $limit) {
            $array_seudoFinal = $array_productos;
        } else
        if (count($array_productos) == $limit) {
            $array_seudoFinal = $array_productos;
        } else
        if (count($array_productos) > $limit) {
            for ($i = 0; $i < $limit; $i++) {
                $array_seudoFinal[] = $array_productos[$i];
            }
        }

        $arrayFinal = [];   $arrayPercent = [];  
        $total_global = 0;  
        $totalParcentaje = 0; $index = 0;

        foreach ($array_seudoFinal as $item) {
            $p = Productos::find($item->producto_id);
            $total = $p->precio * $item->cantidad;
            $total_global += $total;
            $totalParcentaje += $item->cantidad;

            if($index == 0){
                $aux = [
                    'name' => $p->nombre,
                    'y' => $item->cantidad,
                    'sliced' => true,
                    'selected' => true,
                ];
            }else{
                $aux = [
                    'name' => $p->nombre,
                    'y' => $item->cantidad
                ];
            }

            $arrayFinal[] = (object) $aux;
            $index++;
        }

        $index = 0;
        foreach ($array_seudoFinal as $item) {
            $p = Productos::find($item->producto_id);
            $total = $p->precio * $item->cantidad;
            // $total_global += $total;

            $percent = round((100 * $item->cantidad) / $totalParcentaje, 2);

            if($index == 0){
                $aux = [
                    'name' => $p->nombre,
                    'y' => $percent,
                    'sliced' => true,
                    'selected' => true,
                ];
            }else{
                $aux = [
                    'name' => $p->nombre,
                    'y' => $percent
                ];
            }
            $index++;
            $arrayPercent[] = (object) $aux;
        }

        $response = [
            'cantidad' => [
                'lista' => $arrayFinal,
                'total' => $total_global
            ],
            'porcentaje' => [
                'lista' => $arrayPercent
            ]
        ];

        echo json_encode($response);
    }
    
    public function ordenar_array($array)
    {
        for ($i = 1; $i < count($array); $i++) {
            for ($j = 0; $j < count($array) - $i; $j++) {
                if ($array[$j]->cantidad > $array[$j + 1]->cantidad) {

                    $k = $array[$j + 1];
                    $array[$j + 1] = $array[$j];
                    $array[$j] = $k;
                }
            }
        }

        return $array;
    }

    public function nuevoMovimiento($nuevaventa)
    {
        $nuevMovimiento = new Movimientos();
        $nuevMovimiento->tipo_movimiento = 'S';
        $nuevMovimiento->ventas_id = $nuevaventa->id;
        $nuevMovimiento->fecha = date('Y-m-d');
        $nuevMovimiento->save();

        return $nuevMovimiento;
    }
}