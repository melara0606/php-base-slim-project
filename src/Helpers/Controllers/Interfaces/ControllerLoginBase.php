<?php
  namespace Melara\Helpers\Controllers\Interfaces;

  use \Psr\Http\Message\ResponseInterface as Response;
  use \Psr\Http\Message\ServerRequestInterface as Request;

  interface ControllerLoginBase {
    public function create(Request $request, Response $response, $args);
  }