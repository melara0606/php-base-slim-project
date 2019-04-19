<?php
  namespace Melara\Helpers\Response;

  use Slim\Http\Stream;
  use Slim\Http\Headers;
  use Slim\Http\Response;
  use Crell\ApiProblem\ApiProblem;

  class UnauthorizedResponse extends Response {
    public function __construct($message = '', $status = 401, $type = 'v') {

      $problem = new ApiProblem($message, $type);
      $problem->setStatus($status);

      $handle = fopen("php://temp", "wb+");
      $body = new Stream($handle);
      $body->write($problem->asJson(true));
      $headers = new Headers;
      $headers->set("Content-type", "application/problem+json");
      parent::__construct($status, $headers, $body);
    }
  }