<?php
  namespace Melara\Controllers;

  use Melara\Controllers\Controller;

  abstract class Controller {
    // Routes bases para el controlador
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
  }