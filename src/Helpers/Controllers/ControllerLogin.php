<?php
  /*
    index [GET/{id}] retorna un elemento por medio del id
    all [GET] retorna todo los elementos de la db
    create [POST] almacena un elemento en la base de datos
    update [PUT/{id}]  para poder actualizar un elemento por medio del id
    delete [DELETE/{id}] elimina un elmento por medio del id
  */
  namespace Melara\Helpers\Controllers;

  use Firebase\JWT\JWT;
  use Melara\Helpers\Controllers\Interfaces\ControllerLoginBase;
  
  abstract class ControllerLogin implements ControllerLoginBase {
    private $algorithm  = "HS256";
    private $secret = "supersecretkeyyoushouldnotcommittogithub";
    private $routesMatch = array('create' => 3, 'update' => 4, 'delete' => 5, 'all' => 1, 'index' => 2);

    // Constantes de peticiones basica para la creacion
    protected const HTTP_REQUEST_OK = 200;
    protected const HTTP_REQUEST_FORBIDDEN = 403;
    protected const HTTP_REQUEST_NOT_AUTHORIZATION = 401;

    public function __construct($app) {
      $this->app = $app;
      $this->root = $this->app->getContainer();
      self::generateRoutes();
    }

    private function generateRoutes() {
      foreach ($this->routesMatch as $key => $value) {
        if( method_exists($this, $key) ){
          switch ($value) {
            case 1: $this->app->get('', [$this, $key]); break;
            case 2: $this->app->get('/{id}', [$this, $key]); break;
            case 3: $this->app->post('', [$this, $key]); break;
            case 4: $this->app->update('/{id}', [$this, $key]); break;
            case 5: $this->app->delete('/{id}', [$this, $key]); break;
          }
        }
      }
    }

    // payload para el manejo del json web token
    private function getPayLoad($scope = array()) {
      $now = new \DateTime();
      $future = new \DateTime("now +2 hours");
      $jti = base64_encode(random_bytes(16));

      return [
        "iat" => $now->getTimeStamp(),
        "exp" => $future->getTimeStamp(),
        "jti" => $jti,
        "scope" => $scope
      ];
    }

    protected function jwtTokenEnconde($scope = array()) {
      $payload = self::getPayLoad($scope);
      return array(
        'token' => JWT::encode($payload, $this->secret, $this->algorithm),
        'expires' => $payload['exp']
      );
    }
  }