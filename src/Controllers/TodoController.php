<?php 

namespace Melara\Controllers;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Melara\Helpers\Controllers\Controller;
use Melara\Helpers\Response\UnauthorizedResponse;


/* $this->app  [variable de entorno], $this->root [all container] */
class TodoController extends Controller {
  public function __construct($app) {
    parent::__construct($app);
  }

  /* all [GET] -> [route: '/routePath'] */
  public function all(Request $request, Response $response, $args){ }

  /* index [GET/{id}] -> [route: '/routePath/{id}'] */
  public function index(Request $request, Response $response, $args){ }

  /* create [POST] -> [route: '/routePath'] */
  public function create(Request $request, Response $response, $args) {
    if($this->root->token->hasScope(['todos.agregar']) === false) {
      return $this->UnauthorizedResponse();
    }
    return $this->responseStatus200($response, $this->root->token->decoded);
  }

  /* edit [PUT/{id}] -> [route: '/routePath/{id}'] */
  public function edit(Request $request, Response $response, $args){
    if($this->root->token->hasScope(['todos.editar']) === false) {
      return $this->UnauthorizedResponse();
    }
    print_r($args);
  }

  /* delete [DELETE/{id}] -> [route: '/routePath/{id}'] */
  public function delete(Request $request, Response $response, $args){ }

}
?>
