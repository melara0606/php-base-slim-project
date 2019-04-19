<?php
  namespace Melara\Helpers\Controllers\Interfaces;

  use \Psr\Http\Message\ResponseInterface as Response;
  use \Psr\Http\Message\ServerRequestInterface as Request;

  interface ControllerBase {
    public function all(Request $request, Response $response, $args);
    public function edit(Request $request, Response $response, $args);
    public function delete(Request $request, Response $response, $args);
    public function index(Request $request, Response $response, $args);
  }