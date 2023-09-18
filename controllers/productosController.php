<?php
require_once 'app/cors.php';
require_once 'app/request.php';
require_once 'app/error.php';
require_once 'app/helper.php';
require_once 'models/productosModel.php';
require_once 'models/codigosModel.php';
require_once 'controllers/inventarioController.php';

class ProductosController
{
    private $cors;
    private $limiteCodigo = 6;

    public function __construct()
    {
        $this->cors = new Cors();
    }

    public function listarProducto()
    {
        $this->cors->corsJson();
        $dataproducto = Productos::where('estado', 'A')->get();
        $response = [];

        if (count($dataproducto) > 0) {
            foreach ($dataproducto as $produ) { 
                $produ->categorias;
            }

            $response = [
                'status' => true,
                'mensaje' => 'Existen datos',
                'producto' => $dataproducto,
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No existen datos',
                'producto' => null,
            ];
        }
        echo json_encode($response);
    }

    public function paginar($params) 
    {
        $this->cors->corsJson();
        $pagina = $params['id'];

        $total_pagina = 8;
        $total = count(Productos::where('estado', 'A')->get());
        $salto = ($pagina - 1) * $total_pagina;
        $cant_paginas = $total / $total_pagina;


        $productos = Productos::where('estado', 'A')
                                ->where('stock', '>=', 1)
                                ->orderBy('nombre')
                                ->skip($salto)->take($total_pagina)->get();

        $cant_paginas = ceil($cant_paginas);


        if(count($productos) > 0){
            foreach ($productos as $produ) {
                $aux = [
                    'producto' => $produ,
                    'producto_id' => $produ->id,
                    'categoria_id' => $produ->categorias->id, 
                    'cantidad' => 1   
                ];
                $prod[] = (object) $aux;
            }

            $response = [
                'total_registros' => $total,
                'pagina_actual' => intval($pagina),
                'total_paginas' => $cant_paginas,
                'primera_pagina' => 1,
                'ultima_pagina' => $cant_paginas,
                'productos' => $prod,
            ];
        }else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay productos disponibles',
            ];
        }
        echo json_encode($response);
    }

    public function buscarProducto($params){
        $this->cors->corsJson();
        $texto = strtolower($params['texto']);
        $productos = Productos::where('nombre', 'like', '%' . $texto . '%')
                            ->orWhere('codigo_producto', 'like', '%' . $texto . '%')
                            ->where('estado','A')
                            ->where('stock', '>=', 1)
                            ->orderBy('nombre')
                            ->get();
        $response = [];

        if (count($productos) > 0) {
            foreach ($productos as $produ) {
                $aux = [
                    'producto' => $produ,
                    'producto_id' => $produ->id,
                    'categoria_id' => $produ->categorias->id, 
                    'cantidad' => 1   
                ];
                $prod[] = (object) $aux;
            }

            $response = [
                'status' => true,
                'mensaje' => 'Concidencias encontradas',
                'productos' => $prod,
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay registro',
                'productos' => null
            ];
        }
        echo json_encode($response);
    }

    /* borrar luego */
    public function listarProductoMayorStock(){
        $this->cors->corsJson();
        $dataproducto = Productos::where('stock', '>=', 1)->get();
        $response = [];

        if ($dataproducto) {
            foreach ($dataproducto as $produ) {
                $produ->categorias;
            }
            $response = [
                'status' => true,
                'mensaje' => 'Existen datos',
                'producto' => $dataproducto,
                
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'no existen datos',
                'producto' => null,
            ];
        }
        echo json_encode($response);

    }

    public function listarProductoMayorStockMasCantidad(){
        $this->cors->corsJson();
        $dataproducto = Productos::where('stock', '>=', 1)->get();
        $response = [];

        if (count($dataproducto) > 0) {
            foreach ($dataproducto as $produ) {
                $aux = [
                    'producto' => $produ,
                    'producto_id' => $produ->id,
                    'categoria_id' => $produ->categorias->id, 
                    'cantidad' => 1   
                ];
                $response[] = (object) $aux;
            }
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay productos disponibles',
            ];
        }
        echo json_encode($response);
    }
    /* hasta aqui */

    public function listarProductoxIdMasCantidad($params)
    {
        $this->cors->corsJson();
        $id = intval($params['id']);
        $producto = Productos::find($id);
        $response = [];

        if ($producto) {
            $aux = [
                'status' => true,
                'mensaje' => 'Si hay datos',
                'producto' => $producto,
                'categoria_id' => $producto->categorias->id,
                'cantidad' => 1
            ];
            $response[] = (object) $aux;
        }else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay productos disponibles',
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
                'codigo' => 'P0001',
            ];
        } else {
            $numero = substr($serie->num_codigo,1);
            $siguiente = 'P000' . ($numero += 1);
            $response = [
                'status' => true,
                'tipo' => $tipo,
                'mensaje' => 'Existen datos, aumentando codigo del porducto',
                'codigo' => $siguiente,
            ];
        }
        echo json_encode($response);
    }

    public function guardarCodigos(Request $request) 
    {
        $this->cors->corsJson();
        $codigoRequest = $request->input('codigo');
        $num_codigo = $codigoRequest->num_codigo;
        $tipo = $codigoRequest->tipo;
        $response = [];

        if ($codigoRequest == null) {
            $response = [
                'status' => false,
                'mensaje' => 'no ahi datos',
            ];
        } else {
            $nuevoCodigo = new Codigos();
            $nuevoCodigo->num_codigo = $num_codigo;
            $nuevoCodigo->tipo = $tipo;
            $nuevoCodigo->estado = 'A';
            $nuevoCodigo->save();

            $response = [
                'status' => true,
                'mensaje' => 'Guardando datos',
                'codigo' => $nuevoCodigo,
            ];
        }
        echo json_encode($response);
    }

    public function listarProductoxId($params)
    {
        $this->cors->corsJson();
        $id = intval($params['id']);
        $producto = Productos::find($id);
        $response = [];

        if ($producto) {
            $producto->categorias;
            $response = [
                'status' => true,
                'mensaje' => 'Si hay datos',
                'producto' => $producto,
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No ahi datos',
                'producto' => null,
            ];
        }
        echo json_encode($response);

    }

    public function guardarProducto(Request $request)
    {
        $this->cors->corsJson();
        $productoRequest = $request->input('producto');
        $response = [];

        if ($productoRequest) {
            $categorias_id = intval($productoRequest->categorias_id);
            $nombre = ucfirst($productoRequest->nombre);
            $descripcion = ucfirst($productoRequest->descripcion);
            $imagen = $productoRequest->imagen;
            $codigo = $productoRequest->codigo;
            $precio = doubleval($productoRequest->precio);

            $existeCodigo = Productos::where('codigo', $codigo)->get()->first();
            if ($existeCodigo) {
                $response = [
                    'status' => false,
                    'mensaje' => 'El Código del Producto ya existe',
                    'producto' => null,
                ];
            } else {
                $nuevoProducto = new Productos();
                $nuevoProducto->categorias_id = $categorias_id;
                $nuevoProducto->codigo = $codigo;
                $nuevoProducto->nombre = $nombre;
                $nuevoProducto->descripcion = $descripcion;
                $nuevoProducto->imagen = $imagen;
                $nuevoProducto->stock = 0;
                $nuevoProducto->precio = $precio;
                $nuevoProducto->fecha = date('Y-m-d');
                $nuevoProducto->estado = 'A';

                if ($nuevoProducto->save()) {
                    $response = [
                        'status' => true,
                        'mensaje' => 'El producto se registro correctamente',
                        'producto' => $nuevoProducto,
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'mensaje' => 'El producto no se ha guardado',
                        'producto' => null,
                    ];
                }
            }
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay datos para procesar',
                'producto' => null,
            ];
        }
        echo json_encode($response);
    }

    public function subirFotoProducto($file)
    {
        $this->cors->corsJson();
        $img = $file['fichero'];
        $path = 'resources/productos/';
        $response = Helper::save_file($img, $path);
        echo json_encode($response);

    }

    public function listarProductoDataTable()
    {
        $this->cors->corsJson();
        $productos = Productos::where('estado', 'A')->orderBy('codigo')->get();
        $data = [];  $i = 1;
        
        foreach ($productos as $p) {
            $url = BASE . 'resources/productos/' . $p->imagen;
            $disabled = $p->stock > 0 ? 'disabled' : ' ';
    
            $botones = '<div class="btn-group">
                            <button class="btn btn-info btn-sm" onclick="agregarStock(' . $p->id . ')">
                                <i class="fa fa-plus fa-lg"></i>
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="editarProducto(' . $p->id . ')">
                                <i class="fa fa-edit fa-lg"></i>
                            </button>
                            <button ' . $disabled . ' class="btn btn-dark btn-sm" onclick="eliminarProducto(' . $p->id . ')">
                                <i class="fa fa-trash fa-lg"></i>
                            </button>
                        </div>';

            $colorStock = "";
            if ($p->stock < 5) {
                $colorStock = '<div class="text-center"><span class="badge bg-danger" style="font-size: 1.2rem;">' . $p->stock . '</span></div>';
            } else
            if ($p->stock >= 6 && $p->stock < 20) {
                $colorStock = '<div class="text-center"><span class="badge bg-warning" style="font-size: 1.2rem;">' . $p->stock . '</span></div>';
            } else {
                $colorStock = '<div class="text-center"><span class="badge bg-success" style="font-size: 1.2rem;">' . $p->stock . '</span></div>';
            }

            $descripcion = '<small>'. $p->descripcion .'</small>';
            $nombre = '<small>'. $p->nombre .'</small>';
            $categoria = '<small>'. $p->categorias->nombre_categoria .'</small>';

            $data[] = [
                0 => $i,
                1 => '<div class="box-img-producto"><img src=' . "$url" . '></div>',
                2 => $p->codigo,
                3 => $nombre,
                4 => $categoria,
                5 => $colorStock,
                6 => number_format($p->precio, 2, '.', ''),
                7 => $descripcion,
                8 => $botones,
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

    public function eliminarProducto(Request $request)
    {
        $this->cors->corsJson();
        $productoRequest = $request->input('producto');
        $id = intval($productoRequest->id);
        $producto = Productos::find($id);
        $response = [];

        if ($productoRequest) {
            if ($producto) {
                $producto->estado = 'I';
                $producto->save();

                $response = [
                    'status' => true,
                    'mensaje' => "Se ha eliminado el producto",
                ];
            } else {
                $response = [
                    'status' => false,
                    'mensaje' => "No se puede eliminar el producto",
                ];
            }
        } else {
            $response = [
                'status' => false,
                'memsaje' => 'no hay datos para procesar',
                'producto' => null,
            ];
        }
        echo json_encode($response);
    }

    public function editarProducto(Request $request)
    {
        $this->cors->corsJson();
        $productoRequest = $request->input('producto');
        $id = intval($productoRequest->id);
        $categorias_id = intval($productoRequest->categorias_id);
        $nombre = ucfirst($productoRequest->nombre);
        $descripcion = ucfirst($productoRequest->descripcion);
        $precio = doubleval($productoRequest->precio);
        $producto = Productos::find($id);
        $response = [];

        if ($productoRequest) {
            if ($producto) {
                $producto->categorias_id = $categorias_id;
                $producto->nombre = $nombre;
                $producto->descripcion = $descripcion;
                $producto->precio = $precio;
                
                $categoria = Categorias::find($producto->categorias_id);
                $categoria->save();
                $producto->save();

                $response = [
                    'status' => true,
                    'mensaje' => 'Se ha actualizado el producto',
                ];
            } else {
                $response = [
                    'status' => false,
                    'mensaje' => 'No se puede actualizar el producto',
                ];
            }
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay datos para procesar',
            ];
        }
        echo json_encode($response);

    }

    public function agregarStock(Request $request)
    {
        $this->cors->corsJson();
        $productoRequest = $request->input('producto');
        $id = intval($productoRequest->id);
        $stock = intval($productoRequest->stock);
        $producto = Productos::find($id);
        $response = [];
        $detallesProd = [];

        if ($productoRequest) {
            if ($producto) {
                $producto->stock += $stock;
                if ($producto->save()) {
                    //insertar en la tabla movimientos
                    $nuevoMovimiento = $this->nuevoMovimiento($producto);
    
                    $aux = [
                        'productos_id' =>intval($producto->id),
                        'cantidad' => intval($stock),
                        'precio' => $producto->precio
                    ];
                    array_push($detallesProd,(object)$aux);
    
                    //INSERTAR EN LA TABLA INVENTARIO
                    $inventariocontroller = new InventarioController();
                    $responseInventario = $inventariocontroller->guardarIngresoProducto($nuevoMovimiento->id, $detallesProd, 'E');
                    
                    $response = [
                        'status' => true,
                        'mensaje' => 'Se ha actualizado el stock',
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'mensaje' => 'Ocurrio un error',
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'mensaje' => 'No se puede actualizar el stock',
                ];
            }
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay datos para procesar',
            ];
        }
        echo json_encode($response);

    }

    public function contar()
    {
        $this->cors->corsJson();
        $dataProductos = Productos::where('estado', 'A')->get();
        $response = [];

        if ($dataProductos) {
            $response = [
                'status' => true,
                'mensaje' => 'existe datos',
                'modelo' => 'Productos',
                'cantidad' => $dataProductos->count(),
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'no existe datos',
                'modelo' => 'Usuario',
                'cantidad' => 0,
            ];
        }
        echo json_encode($response);

    }

    public function graficoStockProductos(){
        $this->cors->corsJson();
        $productos = Productos::where('estado', 'A')->get();
        $categorias = Categorias::where('estado', 'A')->get();

        $productos_id = []; //array principal
        $secundario = [];

        $arrayFinal = [];   $arrayPercent = [];
        $total_global = 0;  
        $totalParcentaje = 0; $index = 0;

        foreach ($categorias as $item) {
            $nombreCategoria = $item->nombre_categoria;
            $producto = $item->productos;
            $data[] = count($producto);
            $aux = [];  $_cont = 0;
            foreach ($producto as $p) {
                if ($item->id == $p->categorias->id) {
                    $_cont += $p->stock;
                }

                if($index == 0){
                    $aux = [
                        'name' => $nombreCategoria,
                        'y' => $_cont,
                        'sliced' => true,
                        'selected' => true,
                    ];
                }else{
                    $aux = [
                        'name' => $nombreCategoria,
                        'y' => $_cont
                    ];
                }
            }
            $arrayFinal[] = (object) $aux;
            $index++;
        }
        $final = [
            'data' => $arrayFinal,
        ];

        echo json_encode($final);
    }

    public function nuevoMovimiento($producto)
    {
        $nuevMovimiento = new Movimientos();
        $nuevMovimiento->tipo_movimiento = 'E';
        $nuevMovimiento->productos_id = $producto->id;
        $nuevMovimiento->fecha = date('Y-m-d');
        $nuevMovimiento->save();

        return $nuevMovimiento;
    }

}