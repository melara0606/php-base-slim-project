<?php
  namespace Melara\Helpers\Controllers;

  use Melara\Helpers\Controllers\ControllerLogin;
  use Melara\Helpers\Response\UnauthorizedResponse;
  use Melara\Helpers\Controllers\Interfaces\ControllerBase;

  abstract class Controller extends ControllerLogin implements ControllerBase {
    public function __construct($app) {
      parent::__construct($app);
    }

    public function UnauthorizedResponse()
    {
      return new UnauthorizedResponse('Forbidden', self::HTTP_REQUEST_FORBIDDEN, 'Forbidden');
    }

    public function responseStatus200($response, $data)
    {
      return $this->toJsonView($response, $data , self::HTTP_REQUEST_OK);
    }

    public function UnProblemApiResponse($title = 'Not Implemented')
    {
      return new UnauthorizedResponse($title, self::HTTP_REQUEST_NOT_IMPLEMENTED, 'Not Implemented');
    }

    public function UnNotFound()
    {
      return new UnauthorizedResponse('RECURSO NO ENCONTRADO', self::HTTP_REQUEST_NOT_FOUND, 'Not found');
    }
  }