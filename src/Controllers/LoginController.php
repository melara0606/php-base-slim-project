<?php 
  namespace Melara\Controllers;

  use Melara\Helpers\Controllers\ControllerLogin;
  use Melara\Helpers\Response\UnauthorizedResponse;
  use \Psr\Http\Message\ResponseInterface as Response;
  use \Psr\Http\Message\ServerRequestInterface as Request;

  /**
   * TODO: VERIFICAR BIEN SI EXISTE PERSONAL, PERFIL Y SUCURSAL A UN USUARIO
   */
  
  class LoginController extends ControllerLogin {
    public function __construct($app) {
      parent::__construct($app);
    }

    public function create(Request $request, Response $response, $args) {
      $params  = $request->getParsedBody();
      if( @$params['email'] && @$params['password'] ) {        
        $verifyLoginResponse = self::verify_login($params);
  
        if($verifyLoginResponse['error'] === TRUE) {
          return $verifyLoginResponse['response'];
        }else{
          $verifyLoginResponse['routesAndScopes'] = self::generatedScopesAndRoutes($verifyLoginResponse['response']['$perfil']['id']);
          $jsonWebToken = $this->jwtTokenEnconde( 
            $verifyLoginResponse['routesAndScopes'][1], [ $verifyLoginResponse['response']]
          );

          $dataResponse = $verifyLoginResponse['response'];
          $jsonWebToken['data'] = array (
            "usuario"     => $dataResponse['$usuario']['email'],
            "personal"    => [$dataResponse['$personal']['nombres_personal'], $dataResponse['$personal']['apellidos_personal']],
            "sucursal"    => $dataResponse['$sucursal']['nombre'],
            "perfil"      => $dataResponse['$perfil']['nombre'],
            "routes"      => $verifyLoginResponse['routesAndScopes'][0],
            "permisos"    => $verifyLoginResponse['routesAndScopes'][1]
          );

          return $this->toJsonView($response, $jsonWebToken);
        }
      }
    }
  }