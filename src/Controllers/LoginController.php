<?php 
  namespace Melara\Controllers;

  use Melara\Helpers\Controllers\ControllerLogin;
  use \Psr\Http\Message\ResponseInterface as Response;
  use \Psr\Http\Message\ServerRequestInterface as Request;
  
  class LoginController extends ControllerLogin {
    public function __construct($app) {
      parent::__construct($app);
    }

    public function create(Request $request, Response $response, $args) {}
  }