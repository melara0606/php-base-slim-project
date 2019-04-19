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
  }

  /* all [GET] -> [route: '/routePath'] */
  public function all(Request $request, Response $response, $args){
    if($this->root->token->hasScope(['perfil.consultar']) === false) {
      return $this->UnauthorizedResponse();
    }
  }

  /* index [GET/{id}] -> [route: '/routePath/{id}'] */
  public function index(Request $request, Response $response, $args){ }

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
    $result = $this->root->db->where('id', $id)->update('perfiles', [
      "nombre" => $params['nombre']
    ]);

    if($result){
      $record = $this->root->db->where('id', $id)->getOne('perfiles');
      return $this->responseStatus200($response, [
        "record" => $record
      ]);
    }else {
      return $this->UnProblemApiResponse('Tenemos un problema con la base de datos');
    }
  }

  /* delete [DELETE/{id}] -> [route: '/routePath/{id}'] */
  public function delete(Request $request, Response $response, $args){ }

}
?>
