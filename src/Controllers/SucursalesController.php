<?php 

namespace Melara\Controllers;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Melara\Helpers\Controllers\Controller;
use Melara\Helpers\Response\UnauthorizedResponse;


/* $this->app  [variable de entorno], $this->root [all container] */
class SucursalesController extends Controller {
  public function __construct($app) {
    parent::__construct($app);
  }

  /* all [GET] -> [route: '/routePath'] */
  public function all(Request $request, Response $response, $args){
    if($this->root->token->hasScope(['sucursal.consultar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $records = $this->root->db->get('sucursales');
    return $this->getByIdNotFound(["records" => $records], $response);
  }

  /* index [GET/{id}] -> [route: '/routePath/{id}'] */
  public function index(Request $request, Response $response, $args){ 
    $id = $args['id'];
    if($this->root->token->hasScope(['sucursal.consultar']) === false) {
      return $this->UnauthorizedResponse();
    }
    $record = $this->root->db->where('sucursal_cod', $id)->getOne('sucursales');
    return $this->getByIdNotFound(["record" => $record], $response);
  }

  /* create [POST] -> [route: '/routePath'] */
  public function create(Request $request, Response $response, $args) {
    $params = $request->getParsedBody();
    if($this->root->token->hasScope(['sucursal.agregar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $array = [
      "sucursal_cod"  => $this->uniQUnique('sucursal'),
      "nombre"        => $params['nombre'],
      "estado"        => 1
    ];

    return $this->editDatabase(
      $array['sucursal_cod'], 'sucursales', $array, $response, 'sucursal_cod', 'create'
    );
  }

  /* edit [PUT/{id}] -> [route: '/routePath/{id}'] */
  public function edit(Request $request, Response $response, $args){
    $id = $args['id'];
    $params = $request->getParsedBody();
    if($this->root->token->hasScope(['sucursal.editar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $array = [
      "nombre"    => $params['nombre'],
    ];

    return $this->editDatabase(
      $id, 'sucursales', $array, $response, 'sucursal_cod', 'update'
    );
  }

  /* delete [DELETE/{id}] -> [route: '/routePath/{id}'] */
  public function delete(Request $request, Response $response, $args){
    $id = $args['id'];
    $params = $request->getParsedBody();
    if($this->root->token->hasScope(['sucursal.eliminar']) === false) {
      return $this->UnauthorizedResponse();
    }

    return $this->editDatabase(
      $id, 'sucursales', ["estado" => $params['estado'] ], $response, 'sucursal_cod', 'update'
    );
  }
}
