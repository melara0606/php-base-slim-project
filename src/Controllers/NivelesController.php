<?php 

namespace Melara\Controllers;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Melara\Helpers\Controllers\Controller;
use Melara\Helpers\Response\UnauthorizedResponse;


/* $this->app  [variable de entorno], $this->root [all container] */
class NivelesController extends Controller {
  public function __construct($app) {
    parent::__construct($app);

    $this->app->get('/{id}/topics', [$this, 'topics']);
    $this->app->post('/{id}/topics', [$this, 'topicsAdd']);
    $this->app->put('/{id}/topics/{topicsId}', [$this, 'topicsUpdate']);
  }

  /**
   * Routas para los topics por nivel
  */
  /* all [GET] -> [route: '/routePath/{id}/topics'] */
  public function topics(Request $request, Response $response, $args){
    if($this->root->token->hasScope(['niveles.consultar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $records = $this->root->db->where('nivel_id', $args['id'])->get('topics');
    $nivel = $this->root->db->where('id', $args['id'])->getOne('niveles');
    return $this->responseStatus200($response, ['records' => $records, 'nivel' => $nivel]);
  }

  /* create [POST] -> [route: '/routePath/{id}/topics'] */
  public function topicsAdd(Request $request, Response $response, $args) {
    $params = $request->getParsedBody();
    if($this->root->token->hasScope(['niveles.agregar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $id = $this->root->db->insert('topics', array (
      'nivel_id'  => $args['id'],
      'nombre' => trim(addslashes($params['nombre']))
    ));

    $record = $this->root->db->where('id', $id)->getOne('topics');
    return $this->responseStatus200($response, [
      "record" => $record
    ]);
  }

  /* edit [PUT/{id}] -> [route: '/routePath/{id}'] */
  public function topicsUpdate(Request $request, Response $response, $args){
    if($this->root->token->hasScope(['niveles.editar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $id = $args['topicsId'];
    $params = $request->getParsedBody();

    $result = $this->root->db->where('id', $id)->update('topics', [
      "nombre" => $params['nombre']
    ]);

    if($result){
      $record = $this->root->db->where('id', $id)->getOne('topics');
      return $this->responseStatus200($response, [
        "record" => $record
      ]);
    }else{
      return $this->UnProblemApiResponse('Tenemos un problema con la base de datos');
    }
  }
  

  /* all [GET] -> [route: '/routePath'] */
  public function all(Request $request, Response $response, $args){
    if($this->root->token->hasScope(['niveles.consultar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $records = $this->root->db->get('niveles');
    return $this->responseStatus200($response, ['records' => $records]);
  }

  /* index [GET/{id}] -> [route: '/routePath/{id}'] */
  public function index(Request $request, Response $response, $args){
    if($this->root->token->hasScope(['niveles.consultar']) === false) {
      return $this->UnauthorizedResponse();
    }
    $record = $this->root->db->where('id', $args['id'])->getOne('niveles');
    if($record){
      return $this->responseStatus200($response, ['record' => $record]);
    }else{
      return $this->UnNotFound();
    }
  }

  /* create [POST] -> [route: '/routePath'] */
  public function create(Request $request, Response $response, $args) {
    $params = $request->getParsedBody();
    if($this->root->token->hasScope(['niveles.agregar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $id = $this->root->db->insert('niveles', array(
      'nombre' => trim(addslashes($params['nombre']))
    ));

    $record = $this->root->db->where('id', $id)->getOne('niveles');
    return $this->responseStatus200($response, [
      "record" => $record
    ]);
  }

  /* edit [PUT/{id}] -> [route: '/routePath/{id}'] */
  public function edit(Request $request, Response $response, $args){
    if($this->root->token->hasScope(['niveles.editar']) === false) {
      return $this->UnauthorizedResponse();
    }

    $id = $args['id'];
    $params = $request->getParsedBody();

    $result = $this->root->db->where('id', $id)->update('niveles', [
      "nombre" => $params['nombre']
    ]);

    if($result){
      $record = $this->root->db->where('id', $id)->getOne('niveles');
      return $this->responseStatus200($response, [
        "record" => $record
      ]);
    }else{
      return $this->UnProblemApiResponse('Tenemos un problema con la base de datos');
    }
  }

  /* delete [DELETE/{id}] -> [route: '/routePath/{id}'] */
  public function delete(Request $request, Response $response, $args){}
}
?>
