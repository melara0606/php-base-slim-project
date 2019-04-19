<?php 

namespace Melara\Controllers;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Melara\Helpers\Controllers\Controller;
use Melara\Helpers\Response\UnauthorizedResponse;


/* $this->app  [variable de entorno], $this->root [all container] */
class UsuariosController extends Controller {
  public function __construct($app) {
    parent::__construct($app);
  }

  private function getUsers($id = null) {
    $strCampos = 'usuarios.usuario_cod, usuarios.email, usuarios.estado, sucursales.nombre AS nombreSucursal, sucursales.estado AS EstadoSucursal, perfiles.nombre AS nombrePerfil, personales.nombres_personal, personales.apellidos_personal, personales.estado AS Estadopersonal';

    $db = $this->root->db->
      join('sucursales', 'usuarios.sucursal_code = sucursales.sucursal_cod','INNER')->
      join('usuarios_perfiles', 'usuarios_perfiles.usuario_cod = usuarios.usuario_cod','INNER')->
      join('perfiles', 'usuarios_perfiles.perfil_id = perfiles.id','INNER')->
      join('usuarios_personales', 'usuarios_personales.usuario_cod = usuarios.usuario_cod','INNER')->
      join('personales', 'usuarios_personales.personal_cod = personales.personal_cod','INNER');

    if($id) {
      return $db->where('usuarios.usuario_cod', $id)->getOne('usuarios', $strCampos);
    }
    return $db->get('usuarios', null, $strCampos);
  }

  /* all [GET] -> [route: '/routePath'] */
  public function all(Request $request, Response $response, $args){
    if($this->root->token->hasScope(['usuario.consultar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $records = $this->getUsers();
    return $this->responseStatus200($response, [
      'records' => $records
    ]);
  }

  /* index [GET/{id}] -> [route: '/routePath/{id}'] */
  public function index(Request $request, Response $response, $args){ 
    $id = $args['id'];
    if($this->root->token->hasScope(['usuario.consultar']) === false) {
      return $this->UnauthorizedResponse();
    }

    return $this->getByIdNotFound([
      'record' => $this->getUsers($id)
    ], $response);
}

  /* create [POST] -> [route: '/routePath'] */
  public function create(Request $request, Response $response, $args) {
    $params = $request->getParsedBody();
    if($this->root->token->hasScope(['usuario.agregar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $usuario_code = $this->uniQUnique('usuarios');
    $result = $this->root->db->insert('usuarios', [
      'usuario_cod'   => $usuario_code,
      'estado'        => 1,
      'email'         => addslashes(trim($params['email'])),
      // 'password'      => password_hash(addslashes(trim($params['password']), DEFAULT_PASSWORD )),
      'password'      =>  $params['password'],
      'sucursal_code'  => $params['sucursal_cod']
    ]);

    if($result) {
      $result = $this->root->db->insert('usuarios_personales', [
        'usuario_cod'   => $usuario_code,
        'personal_cod'  => $params['personal_cod']
      ]);

      if($result) {
        $result = $this->root->db->insert('usuarios_perfiles', [
          "usuario_cod"   => $usuario_code,
          "perfil_id"     => $params['perfil_id']
        ]);
        return $this->responseStatus200($response, [
          "record" => $this->getUsers($usuario_code)
        ]);
      }else {
        return $this->UnProblemApiResponse($this->root->db->getLastError());
      }
    }
    return $this->UnProblemApiResponse($this->root->db->getLastError());
  }

  /* delete [DELETE/{id}] -> [route: '/routePath/{id}'] */
  public function delete(Request $request, Response $response, $args){
    $id = $args['id'];
    $params = $request->getParsedBody();

    if($this->root->token->hasScope(['usuario.eliminar']) === false) {
      return $this->UnauthorizedResponse();
    }

    return $this->editDatabase($id, 'usuarios', [
      'estado' => $params['estado']
    ], $response, 'usuario_cod');
  }

  /* edit [PUT/{id}] -> [route: '/routePath/{id}'] */
  public function edit(Request $request, Response $response, $args){
    $id = $args['id'];
    $params = $request->getParsedBody();
    if($this->root->token->hasScope(['usuario.editar']) === false) {
      return $this->UnauthorizedResponse();
    }

    switch (trim($params['TYPE'])) {
      case 'PERSONAL' : return $this->updatePersonal($params, $id, $response);      
      case 'PERFIL'   : return $this->updatePerfil($params, $id, $response);
      case 'SUCURSAL' : return $this->updateSucursal($params, $id, $response);
      case 'PASSWORD' : return $this->updatePassword($params, $id, $response);
      default:
        return $this->UnNotFound();
    }
  }
  
  // Metodos para la actualizacion
  protected function updatePersonal($params = null, $id = null, $response) {
    return $this->editDatabase($id, 'usuarios_personales', [
      "personal_cod" => $params['personal_cod']
    ], $response, 'usuario_cod');
  }

  protected function updatePerfil($params = null, $id = null, $response) {
    return $this->editDatabase($id, 'usuarios_perfiles', [
      "perfil_id"     => $params['perfil_id']
    ], $response, 'usuario_cod');
  }

  protected function updateSucursal($params = null, $id = null, $response) {
    return $this->editDatabase($id, 'usuarios', [
      "sucursal_code"     => $params['sucursal_code']
    ], $response, 'usuario_cod');
  }

  protected function updatePassword($params = null, $id = null, $response) {
    // falta el hash
    return $this->editDatabase($id, 'usuarios', [
      "password"     => $params['password']
    ], $response, 'usuario_cod');
  }
}
