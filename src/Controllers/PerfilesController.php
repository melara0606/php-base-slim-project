<?php 

namespace Melara\Controllers;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Melara\Helpers\Controllers\Controller;
use Melara\Helpers\Response\UnauthorizedResponse;


/* $this->app  [variable de entorno], $this->root [all container] */
class PerfilesController extends Controller {
  public function __construct($app) {
    parent::__construct($app);

    $this->app->post('/{id}/recursos', [$this, 'recursosAdd']);
    $this->app->put('/{id}/recursos/{recursoId}', [$this, 'recursosEdit']);
    $this->app->delete('/{id}/recursos/{recursoId}', [$this, 'recursosDelete']);

    // para los recursos
    $this->app->put('/recursos/{id}', [$this, 'recursoEdit']);
    $this->app->delete('/recursos/{id}', [$this, 'recursosStatus']);
  }

  /**
   * Mantenimiento de los recursos
  */
  /* edit recurso [PUT /perfiles/recursos/{id}] -> [route: '/perfiles/recursos/{id}'] */
  public function recursoEdit(Request $request, Response $response, $args){
    $params = $request->getParsedBody();
    if ($this->root->token->hasScope(['perfil.editar']) === false) {
      return $this->UnauthorizedResponse();
    }

    return $this->editDatabase($args['id'], 'recursos', [
      'nombre'   => trim(addslashes($params['nombre'])),
      'icons'      => trim(addslashes($params['icons']))
    ], $response);
  }

  /* delete [DELETE/{id}] -> [route: '/routePath/recursos/{id}'] */
  public function recursosStatus(Request $request, Response $response, $args){
    $id = $args['id'];
    $params = $request->getParsedBody();

    if ($this->root->token->hasScope(['perfil.eliminar']) === false) {
      return $this->UnauthorizedResponse();
    }

    return $this->editDatabase($args['id'], 'recursos', [
      'estado'   => trim(addslashes($params['estado']))
    ], $response);
  }

  /**
   * Rutas para la asignacion de recursos
  */
  /* create [POST] -> [route: '/routePath/{id}/recursos'] */
  public function recursosAdd(Request $request, Response $response, $args) {
    $params = $request->getParsedBody();
    if($this->root->token->hasScope(['perfil.agregar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $record = $this->root->db
      ->where('perfil_id', $args['id'])
      ->where('recurso_id', trim(addslashes($params['recurso_id'])))
      ->getOne('perfiles_recursos');

    if(count($record) > 0) {
      return $this->UnProblemApiResponse('El recursos ya existe para este perfil');
    }

    $record = array(
      'consultar'   => trim(addslashes($params['consultar'])),
      'agregar'      => trim(addslashes($params['agregar'])),
      'editar'      => trim(addslashes($params['editar'])),
      'eliminar'    => trim(addslashes($params['eliminar'])),
      'recurso_id'  => trim(addslashes($params['recurso_id'])),
      'perfil_id'   => trim(addslashes($args['id']))
    );

    $id = $this->root->db->insert('perfiles_recursos', $record );
    return $this->responseStatus200($response, [
      "record" => $record
    ]);
  }

  /* edit [PUT/{id}/recursos{recursosId}] -> [route: '/routePath/{id}/recursos/{recursoId}'] */
  public function recursosEdit(Request $request, Response $response, $args){
    $params = $request->getParsedBody();
    if ($this->root->token->hasScope(['perfil.editar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $data = array(
      'consultar'   => trim(addslashes($params['consultar'])),
      'agregar'      => trim(addslashes($params['agregar'])),
      'editar'      => trim(addslashes($params['editar'])),
      'eliminar'    => trim(addslashes($params['eliminar']))
    );

    $result = $this->root->db
        ->where('perfil_id', $args['id'])
        ->where('recurso_id', $args['recursoId'])
        ->update('perfiles_recursos', $data);

    $record = $this->root->db
      ->where('perfil_id', $args['id'])
      ->where('recurso_id', $args['recursoId'])
      ->getOne('perfiles_recursos');

    return $this->responseStatus200($response, [ "record" => $record ]);
  }

  /* delete [DELETE/{id}] -> [route: '/routePath/{id}/recursos/{recursoId}'] */
  public function recursosDelete(Request $request, Response $response, $args){
    if ($this->root->token->hasScope(['perfil.eliminar']) === false) {
      return $this->UnauthorizedResponse();
    }
    
    $id = $args['id'];
    $recursoId = $args['recursoId'];

    $result = $this->root->db
      ->where('perfil_id', $id)
      ->where('recurso_id', $recursoId)
      ->delete('perfiles_recursos');
    
    if($result){
      return $this->responseStatus200($response, [
        "message" => "Elemento eliminado", 
        "ok" => true
      ]);
    } else {
      return $this->UnProblemApiResponse(
        [ "ok" => false, "message" => 'Lo sentimos pero hay un problema ...' ]
      );
    }
  }

  /* all [GET] -> [route: '/routePath'] */
  public function all(Request $request, Response $response, $args){
    if($this->root->token->hasScope(['perfil.consultar']) === false) {
      return $this->UnauthorizedResponse();
    }

    return $this->responseStatus200($response, [
      "records" => $this->root->db->get('perfiles')
    ]);
  }

  /* index [GET/{id}] -> [route: '/routePath/{id}'] */
  public function index(Request $request, Response $response, $args){ 
    $id = $args['id'];
    if($this->root->token->hasScope(['perfil.consultar']) === false) {
      return $this->UnauthorizedResponse();
    }

    return $this->responseStatus200($response, [
      "record" => $this->root->db->where('id', $id)->getOne('perfiles')
    ]);
  }

  /* create [POST] -> [route: '/routePath'] */
  public function create(Request $request, Response $response, $args) {
    $params = $request->getParsedBody();
    if($this->root->token->hasScope(['perfil.agregar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $id = $this->root->db->insert('perfiles', array(
      'nombre' => trim(addslashes($params['nombre']))
    ));

    $record = $this->root->db->where('id', $id)->getOne('perfiles');
    return $this->responseStatus200($response, [
      "record" => $record
    ]);
  }

  /* edit [PUT/{id}] -> [route: '/routePath/{id}'] */
  public function edit(Request $request, Response $response, $args){
    $params = $request->getParsedBody();
    if ($this->root->token->hasScope(['perfil.editar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $id = $args['id'];
    return $this->editDatabase($id, 'perfiles', [
      'nombre' => $params['nombre']
    ], $response);
  }

  /* delete [DELETE/{id}] -> [route: '/routePath/{id}'] */
  public function delete(Request $request, Response $response, $args){
    $id = $args['id'];
    $params = $request->getParsedBody();
    if ($this->root->token->hasScope(['perfil.eliminar']) === false) {
      return $this->UnauthorizedResponse();
    }

    return $this->editDatabase($id, 'perfiles', [
      'estado' => $params['estado']
    ], $response);
  }
}
?>
