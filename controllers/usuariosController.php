<?php
require_once 'app/cors.php';
require_once 'app/request.php';
require_once 'app/error.php';
require_once 'app/helper.php';
require_once 'core/conexion.php';
require_once 'models/usuariosModel.php';
require_once 'models/personasModel.php';
require_once 'controllers/personasController.php';

class UsuariosController{
    private $cors;
    private $personaCntr;

    public function __construct()
    {
        $this->cors = new Cors();
        $this->personaCntr = new PersonasController();
    }

    /* public function listar(){
        $this->cors->corsJson();
        $usuario = Usuarios::where('estado', 'A')->get();
        $response = [];

        if (count($usuario) > 0) {
            foreach ($usuario as $us) {
                $us->personas;
                $us->roles;

                $response = [
                    'status' => false,
                    'mensaje' => 'Existen Usuarios',
                    'usuario' => $us,
                ];
            }
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay Usuarios Disponibles',
                'usuario' => null,
            ];
        }
        echo json_encode($response);
    } */

    public function listarDataTable()
    {
        $this->cors->corsJson();
        $datausuario = Usuarios::where('estado', 'A')->get();
        $data = [];  $i = 1;

        foreach ($datausuario as $du) {
            $personas = $du->personas;
            $roles = $du->roles;

            $url = BASE . 'resources/usuarios/' . $du->imagen;
            $icono = $du->estado == 'I' ? '<i class="fa fa-check-circle fa-lg"></i>' : '<i class="fa fa-trash fa-lg"></i>';
            $clase = $du->estado == 'I' ? 'btn-success btn-sm' : 'btn-dark btn-sm';
            $other = $du->estado == 'A' ? 0 : 1;

            $botones = '<div class="btn-group">
                            <button class="btn bg-purple btn-sm" onclick="editarUsuario(' . $du->id . ')">
                                <i class="fa fa-edit fa-lg"></i>
                            </button>
                            <button class="btn ' . $clase . '" onclick="eliminarUsuario(' . $du->id . ',' . $other . ')">
                                ' . $icono . '
                            </button>
                        </div>';

            $data[] = [
                0 => $i,
                1 => '<div class="box-img-usuarios"><img src=' . "$url" . ' class="img-fluid img-circle img-sm"></div>',
                2 => $personas->cedula,
                3 => $personas->nombre,
                4 => $personas->apellido,
                5 => $du->usuario,
                6 => $roles->rol,
                7 => $du->correo,
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

    public function listarUsuarioId($params){
        $this->cors->corsJson();
        $id = intval($params['id']);
        $dataUsuario = Usuarios::find($id);
        $response = [];

        if ($dataUsuario) {
            $dataUsuario->personas;
            $dataUsuario->roles;

            $response = [
                'status' => true,
                'mensaje' => 'Si hay datos',
                'usuario' => $dataUsuario,
            ];
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay datos',
                'usuario' => null,
            ];
        }
        echo json_encode($response);
    }

    public function login(Request $request){
        $this->cors->corsJson();
        $data = $request->input('login');
        $usuario = $data->usuario; 
        $clave = $data->clave;
        $encriptarClave = hash('sha256', $clave);
        $response = [];

        $captcha = $data->captcha;
        $secret = "6LcFBbohAAAAAFlihyDmzeVRcVA7IEo9Em1nWk_O"; 
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $responseCaptcha = file_get_contents($url."?secret=".$secret."&response=".$captcha);
        $atributosCaptcha = json_decode($responseCaptcha);

        if (!$atributosCaptcha->success) {
            $response = [
                'status' => false,
                'mensaje' => 'Captcha Inválido',
            ];
        } else {
            if ((!isset($usuario) || $usuario == "") || (!isset($clave) || $clave == "")) {
                $response = [
                    'status' => false,
                    'mensaje' => 'Falta datos',
                ];
            }else{
                $usuario = Usuarios::where('usuario', $usuario)->orWhere('correo', $usuario)->get()->first();
                 
                if($usuario){
                    if($this->validarCredenciales($encriptarClave,$usuario->clave)){
                        $rol_cargo = $usuario->roles->rol;
                        $persona_id = $usuario->personas_id;
    
                        $per = Personas::find($persona_id);
                        $usuario['persona'] = $per;
                        $nombre = $per->nombre . ' ' . $per->apellido;

                        //cliente id
                        $cliente = $usuario->personas->clientes;
                        $cli_id = [];
                        foreach ($cliente as $c) {
                            $cli_id = $c->id;
                        }
    
                        $response = [
                            'status' => true,
                            'mensaje' => 'Acceso al Sistema',
                            'rol' => $rol_cargo,
                            'persona' => $nombre,
                            'usuario' => $usuario,
                            'cliente' => $cli_id,
                        ];
                    }else {
                        $response = [
                            'status' => false,
                            'mensaje' => 'La contraseña es incorrecta',
                        ];
                    }
                }else {
                    $response = [
                        'status' => false,
                        'mensaje' => 'El usuario es incorrecto',
                    ];
                }
            }   
        }
        echo json_encode($response);
    }

    private function validarCredenciales($credencial1, $credencial2)
    {
        if ($credencial1 == $credencial2) {
            return true;
        } else {
            return false;
        }
    }

    public function guardarUsuario(Request $request)
    {
        $this->cors->corsJson();
        $usuarioRequest = $request->input('usuario');
        $response = [];

        if (!isset($usuarioRequest) || $usuarioRequest == null) {
            $response = [
                'status' => false,
                'mensaje' => "No hay datos para procesar",
                'usuario' => null,
            ];
        } else {
            $responsePersona = $this->personaCntr->guardarPersona($request);

            $id_persona = $responsePersona['persona']->id; //recuperar el id de persona

            $clave = $usuarioRequest->clave;
            $encriptar = hash('sha256', $clave);

            $nuevoUsuario = new Usuarios();
            $nuevoUsuario->roles_id = intval($usuarioRequest->roles_id);
            $nuevoUsuario->personas_id = intval($id_persona);
            $nuevoUsuario->usuario = ucfirst($usuarioRequest->usuario);
            $nuevoUsuario->correo = $usuarioRequest->correo;
            $nuevoUsuario->clave = $encriptar;
            $nuevoUsuario->conf_clave = $encriptar;
            $nuevoUsuario->imagen = $usuarioRequest->imagen;
            $nuevoUsuario->estado = 'A';

            $existeUsuario = Usuarios::where('personas_id', $id_persona)->get()->first();

            if ($existeUsuario) {
                $response = [
                    'status' => false,
                    'mensaje' => 'El usuario ya existe',
                    'usuario' => null,
                ];
            } else {
                if ($nuevoUsuario->save()) {
                    $response = [
                        'status' => true,
                        'mensaje' => 'El usuario se guardo correctamente',
                        'usuario' => $nuevoUsuario,
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'mensaje' => 'El usuario no se pudo guardar',
                        'usuario' => null,
                    ];
                }
            }
        }
        echo json_encode($response);
    }

    public function subirFoto($file)
    {
        $this->cors->corsJson();
        $img = $file['fichero'];
        $path = 'resources/usuarios/';
        $response = Helper::save_file($img, $path);
        echo json_encode($response);
    }

    public function eliminarUsuario(Request $request)
    {
        $this->cors->corsJson();
        $usuarioRequest = $request->input('usuario');
        $id = $usuarioRequest->id;
        $dataUsuario = Usuarios::find($id);

        if ($usuarioRequest) {
            if ($dataUsuario) {
                $dataUsuario->estado = 'I';
                $dataUsuario->save();

                $response = [
                    'status' => true,
                    'mensaje' => "Se ha eliminado el usuario",
                ];
            } else {
                $response = [
                    'status' => false,
                    'mensaje' => "No se puede eliminar el usuario",
                ];
            }
        } else {
            $response = [
                'status' => false,
                'mensaje' => 'No hay datos',
            ];
        }
        echo json_encode($response);
    }

    public function editarUsuario(Request $request)
    {
        $this->cors->corsJson();
        $usuarioRequest = $request->input('usuario');
        $id = intval($usuarioRequest->id);

        $persona_id = intval($usuarioRequest->personas_id); 
        $roles_id = intval($usuarioRequest->roles_id); 
        
        $dataUsuario = Usuarios::find($id); 
        $response = [];

        if($usuarioRequest){
            if($dataUsuario){
                $dataUsuario->roles_id = $roles_id; 
                $dataUsuario->personas_id = $persona_id; 
                $dataUsuario->usuario = $usuarioRequest->usuario; 
                $dataUsuario->correo = $usuarioRequest->correo;

                $dataPersona = Personas::find($dataUsuario->personas_id);
                $dataPersona->nombre = ucfirst($usuarioRequest->nombre);
                $dataPersona->apellido = ucfirst($usuarioRequest->apellido);
                $dataPersona->celular = $usuarioRequest->celular;
                $dataPersona->direccion = $usuarioRequest->direccion;
                $dataPersona->save();
                $dataUsuario->save();
                
                $response = [
                    'status' => true,
                    'mensaje' => 'El usuario se ha actualizado correctamente',
                    'usuario' => $dataUsuario,
                ];
            }else {
                $response = [
                    'status' => false,
                    'mensaje' => 'No se puede actualizar el usuario',
                ];
            }
        }else{
            $response = [
                'status' => false,
                'mensaje' => 'No hay datos ',
            ];
        }
        echo json_encode($response);
    }

    public function contar()
    {
        $this->cors->corsJson();
        $datausuario = Usuarios::where('estado', 'A')->get();
        $response = [];

        if ($datausuario) {
            $response = [
                'status' => true,
                'mensaje' => 'existe datos',
                'modelo' => 'Usuarios',
                'cantidad' => $datausuario->count(),
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
}