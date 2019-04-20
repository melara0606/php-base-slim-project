<?php 

namespace Melara\Controllers;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Melara\Helpers\Controllers\Controller;
use Melara\Helpers\Response\UnauthorizedResponse;


/* $this->app  [variable de entorno], $this->root [all container] */
class PersonalesControllers extends Controller {
  public function __construct($app) {
    parent::__construct($app);
  }

  /* all [GET] -> [route: '/routePath'] */
  public function all(Request $request, Response $response, $args){
    if($this->root->token->hasScope(['personal.consultar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $records = $this->root->db->get('personales');
    return $this->getByIdNotFound(["records" => $records], $response);
  }

  /* index [GET/{id}] -> [route: '/routePath/{id}'] */
  public function index(Request $request, Response $response, $args){ 
    $id = $args['id'];
    if($this->root->token->hasScope(['personal.consultar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $record = $this->root->db->where('personal_cod', $id)->getOne('personales');
    return $this->getByIdNotFound(["record" => $record], $response);
}

  /* create [POST] -> [route: '/routePath'] */
  public function create(Request $request, Response $response, $args) {
    $params = $request->getParsedBody();
    if($this->root->token->hasScope(['personal.agregar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $array = [
      "personal_cod"       => $this->uniQUnique('personal'),
      "nombres_personal"    => $params['nombres_personal'],
      "apellidos_personal"  => $params['apellidos_personal'],
      "estado"              => 1
    ];

    return $this->editDatabase(
      $array['personal_cod'], 'personales', $array, $response, 'personal_cod', 'create'
    );
  }

  /* edit [PUT/{id}] -> [route: '/routePath/{id}'] */
  public function edit(Request $request, Response $response, $args){
    $id = $args['id'];
    $params = $request->getParsedBody();

    if($this->root->token->hasScope(['personal.editar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $array = [
      "nombres_personal"    => $params['nombres_personal'],
      "apellidos_personal"  => $params['apellidos_personal'],
    ];

    return $this->editDatabase(
      $id, 'personales', $array, $response, 'personal_cod', 'update'
    );
}

  /* delete [DELETE/{id}] -> [route: '/routePath/{id}'] */
  public function delete(Request $request, Response $response, $args){
    $id = $args['id'];
    $params = $request->getParsedBody();

    if($this->root->token->hasScope(['personal.eliminar']) === false) {
      return $this->UnauthorizedResponse();
    }

    return $this->editDatabase(
      $id, 'personales', [
        "estado" => $params['estado']
      ], $response, 'personal_cod', 'update'
    );
  }
}
