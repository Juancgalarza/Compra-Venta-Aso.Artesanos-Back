<?php
require_once 'app/cors.php';
require_once 'app/request.php';
require_once 'app/error.php';
require_once 'models/categoriasModel.php';

class CategoriasController
{
    private $cors;

    public function __construct()
    {
        $this->cors = new Cors();
    }

    public function listarXid($params){
        $this->cors->corsJson(); 
        $id=intval($params['id']);
        $response = [];
        $categorias = Categorias::find($id);
        if ($categorias) {
            $response = [
                'status' => true,
                'mensaje' => 'Existen datos',
                'categoria' => $categorias
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No existen datos',
                'categoria' => null
            ];
        }
        echo json_encode($response);
    }

    public function listarCategoriasDataTable()
    {
        $this->cors->corsJson();
        $dataCategoria = Categorias::where('estado', 'A')->orderBy('id', 'desc')->get();
        $data = [];  $i = 1;

        foreach ($dataCategoria as $dc) {
            $botones = '<div class="btn-group">
                            <button class="btn btn-primary btn-sm" onclick="editarCategoria(' . $dc->id . ')">
                                <i class="fa fa-edit fa-lg"></i>
                            </button>
                            
                            <button class="btn btn-dark btn-sm" onclick="eliminarCategoria(' . $dc->id . ')">
                                <i class="fa fa-trash fa-lg"></i>
                            </button>
                        </div>';

            $data[] = [
                0 => $i,
                1 => $dc->nombre_categoria,
                2 => $botones,
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

    public function guardar(Request $request)
    {
        $this->cors->corsJson();
        $categoriaRequest = $request->input("categoria");
        $nombre_categoria = ucfirst($categoriaRequest->nombre_categoria);

        if ($categoriaRequest) {
            $existeCategoria = Categorias::where('nombre_categoria', $nombre_categoria)->get()->first();

            if ($existeCategoria) {
                $response = [
                    'status' => false,
                    'mensaje' => 'La categoría ya existe',
                    'categoria' => null,
                ];
            } else {
                $nuevaCategoria = new Categorias();
                $nuevaCategoria->nombre_categoria = $nombre_categoria;
                $nuevaCategoria->estado = 'A';

                if ($nuevaCategoria->save()) {
                    $response = [
                        'status' => true,
                        'mensaje' => 'La categoría se ha guardado',
                        'categoria' => $nuevaCategoria,
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'mensaje' => 'La categoría no se puede guardar',
                        'categoria' => null,
                    ];
                }
            }
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay datos para procesar',
                'categoria' => null,
            ];
        }
        echo json_encode($response);
    }

    public function eliminar(Request $request)
    {
        $this->cors->corsJson();
        $eliminarRequest = $request->input('categoria');
        $id = intval($eliminarRequest->id);
        $response = [];

        $datacategoria = Categorias::find($id);
        if ($datacategoria) {
            $datacategoria->estado = 'I';
            $datacategoria->save();

            $response = [
                'status' => true,
                'mensaje' => 'Categoría Eliminada',
                'categoria' => $datacategoria
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No se puede eliminar la categoría',
                'categoria' => null
            ];
        }
        echo json_encode($response);
    }

    public function editar(Request $request){
        $this->cors->corsJson();
        $categoriaRequest=$request->input('categoria');
        $id=intval($categoriaRequest->id);
        $nombre_categoria= ucfirst($categoriaRequest->nombre_categoria);
        $response=[];

        $categoria=Categorias::find($id);
        if($categoriaRequest){
            if($categoria){
                $categoria->nombre_categoria=$nombre_categoria;
                $categoria->save();

                $response=[
                    'status'=>true,
                    'mensaje'=>'Categoría Editada',
                    'categoria'=>$categoria
                ];
            }else{
                $response=[
                    'status'=>true,
                    'mensaje'=>'No se pudo editar la categoría',
                    'categoria'=>null
                ];
            }
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No existen datos',
            ];
        }
        echo json_encode($response);
    }

    public function selectCategoria()
    {
        $this->cors->corsJson();
        $response = [];
        $categorias = Categorias::where('estado', 'A')->get();
        if (count($categorias) > 0) {
            $response = [
                'status' => true,
                'mensaje' => 'Existen datos',
                'categoria' => $categorias
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No existen datos',
                'categoria' => null
            ];
        }
        echo json_encode($response);
    }

    public function listarXidInventario($params)
    {
        $this->cors->corsJson(); 
        $id=intval($params['id']);
        $response = [];
        
        $categorias = Categorias::find($id);
        if ($categorias) {
            $categorias->productos;
            $response = [
                'status' => true,
                'mensaje' => 'Existen datos',
                'categoria' => $categorias
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No existen datos',
                'categoria' => null
            ];
        }
        echo json_encode($response);
    }
}