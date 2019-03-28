<?php

namespace Melara\Controllers;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Melara\Controllers\Controller;
use Melara\Helpers\Response\UnauthorizedResponse;

class TodoController extends Controller {
  public function __construct($app = null) {
    parent::__construct($app);
  }

  public function create(Request $request, Response $response)
  {
    if(false === $this->root->token->hasScope(['todo.create']))
    {
      return new UnauthorizedResponse('Aceso no permitido para esta peticion', 403);
    }

    return $response->withJson(array(
      "response" => true,
      "txt" => 'Excelente hemos realizado la peticion con exito'
    ));
  }
}