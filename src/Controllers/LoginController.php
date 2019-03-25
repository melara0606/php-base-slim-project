<?php 
  namespace Melara\Controllers;

  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;
  
  use Melara\Controllers\BaseController;

  /*
    index [GET/{id}] retorna un elemento por medio del id
    all [GET] retorna todo los elementos de la db
    create [POST] almacena un elemento en la base de datos
    update [PUT/{id}]  para poder actualizar un elemento por medio del id
    delete [DELETE/{id}] elimina un elmento por medio del id
  */

  class LoginController extends Controller {
    public function __construct($app = null) {
      parent::__construct($app);
    }

    public function index(Request $request, Response $response, $args)
    {
      
    }
  }