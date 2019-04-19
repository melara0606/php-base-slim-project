<?php
  namespace Melara\Helpers\Controllers;

  use Firebase\JWT\JWT;
  use Melara\Helpers\Response\UnauthorizedResponse;
  use Melara\Helpers\Controllers\Interfaces\ControllerLoginBase;
  
  abstract class ControllerLogin implements ControllerLoginBase {
    private $algorithm  = "HS256";
    private $secret = "supersecretkeyyoushouldnotcommittogithub";
    private $routesMatch = array('create' => 3, 'edit' => 4, 'delete' => 5, 'all' => 1, 'index' => 2);

    // Constantes de peticiones basica para la creacion
    protected const HTTP_REQUEST_OK = 200;
    protected const HTTP_REQUEST_FORBIDDEN = 403;
    protected const HTTP_REQUEST_NOT_AUTHORIZATION = 401;
    protected const HTTP_REQUEST_NOT_FOUND = 404;

    public $routeName = 'todos';
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
            case 4: $this->app->put('/{id}', [$this, $key]); break;
            case 5: $this->app->delete('/{id}', [$this, $key]); break;
          }
        }
      }
    }

    // payload para el manejo del json web token
    private function getPayLoad($scope = array(), array $data = array()) {
      $now = new \DateTime();
      $future = new \DateTime("now +2 hours");
      $jti = base64_encode(random_bytes(16));

      return [
        "iat"   => $now->getTimeStamp(),
        "exp"   => $future->getTimeStamp(),
        "jti"   => $jti,
        "scope" => $scope,
        "data"  => $data
      ];
    }

    protected function jwtTokenEnconde($scope = array(), array $data = array()) {
      $payload = self::getPayLoad($scope, $data);
      return array(
        'token' => JWT::encode($payload, $this->secret, $this->algorithm),
        'expires' => $payload['exp']
      );
    }

    protected function verify_login( $params ) {
      $array_response = array(
        "error" => TRUE
      );

      $usuario = $this->root->db->where('email', addslashes( $params['email'] ))->getOne('usuarios');

      if(!$usuario) {         
        $array_response['response'] = new UnauthorizedResponse(
          "El usuario '${params['email']}' no esta registrado en nuestra base de datos", 401, 'Unauthorized'
        );
        return $array_response;
      }

      if(password_verify( trim(addslashes($params['password'])) , $usuario['password']) == FALSE) {
        $array_response['response'] = new UnauthorizedResponse (
          "La contraseña es incorrecta, prueba con la correcta", 401, 'Unauthorized'
        );
        return $array_response;
      }

      // verificando si el personal esta activo
      $personal = $this->root->db
        ->join('usuarios_personales', 'usuarios_personales.usuario_cod = usuarios.usuario_cod', 'INNER')
        ->join('personales', 'usuarios_personales.personal_cod = personales.personal_cod', 'INNER')
        ->where('usuarios.usuario_cod', $usuario['usuario_cod'])
        ->where('personales.estado', 1)
        ->getOne('usuarios', 'personales.*');

      $perfil = $this->root->db
          ->join('perfiles', 'usuarios_perfiles.perfil_id = perfiles.id', 'INNER')
          ->where('usuarios_perfiles.usuario_cod', $usuario['usuario_cod'])
          ->where('perfiles.estado', 1)
          ->getOne('usuarios_perfiles', 'perfiles.*');

      $sucursal = $this->root->db
          ->where('sucursal_cod', $usuario['sucursal_code'])
          ->where('sucursales.estado', 1)
          ->getOne('sucursales');

      if( !($personal && $sucursal && $perfil && $usuario['estado'] != 0) ) {
        $array_response['response'] = new UnauthorizedResponse (
          "El usuario '${params['email']}' esta desactivado, ponte en contacto con tu administrador", 401, 'Unauthorized'
        );
        return $array_response;
      }

      $array_response = array(
        'response' => array(
          '$personal' => $personal,
          '$sucursal' => $sucursal,
          '$perfil'   => $perfil,
          '$usuario'  => [
            'usuario_cod' => $usuario['usuario_cod'], 'email' => $usuario['email'], 'is_admin' => $usuario['is_admin']
          ]
        ),
        'error' => FALSE
      );

      return $array_response;
    }

    public function toJsonView($response, $array, $status = 200) {
      return $response->withStatus($status)->withJson($array);
    }

    private function getRecourses( $id ) {
      return $this->root->db
                ->join('perfiles_recursos', 'perfiles_recursos.recurso_id = recursos.id', 'INNER')
                ->where('perfiles_recursos.perfil_id', $id )
                ->where('recursos.estado', 1)
                ->get('recursos',null,'perfiles_recursos.*, recursos.*');
    }

    protected function generatedScopesAndRoutes($perfil_id){
      $array_scopes = [];
      $array_routes = [];

      $recursos = $this->getRecourses($perfil_id);
      foreach ($recursos as $key => $value) {
        array_push($array_scopes, "${value['short_name']}.consultar");
        if($value['agregar'] == 1)   { array_push($array_scopes, "${value['short_name']}.agregar"); }
        if($value['editar'] == 1)    { array_push($array_scopes, "${value['short_name']}.editar"); }
        if($value['eliminar'] == 1)  { array_push($array_scopes, "${value['short_name']}.eliminar"); }

        array_push($array_routes, [
          "nombre"      => $value['nombre'],
          "route"       => $value['url'],
          "icon"        => $value['icons'],
          "view_menu"   => $value['view_menu'],
          "short_name"  => $value['short_name']
        ]);
      }

      return [$array_routes, $array_scopes];
    }
  }