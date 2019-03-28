<?php 
  namespace Melara\Controllers;

  use Firebase\JWT\JWT;
  use Melara\Controllers\Controller;
  use \Psr\Http\Message\ResponseInterface as Response;
  use \Psr\Http\Message\ServerRequestInterface as Request;
  
  /*
    index [GET/{id}] retorna un elemento por medio del id
    all [GET] retorna todo los elementos de la db
    create [POST] almacena un elemento en la base de datos
    update [PUT/{id}]  para poder actualizar un elemento por medio del id
    delete [DELETE/{id}] elimina un elmento por medio del id
  */

  class LoginController extends Controller {
    protected $algorithm  = "HS256";
    protected $secret = "supersecretkeyyoushouldnotcommittogithub";

    public function __construct($app = null) {
      parent::__construct($app);
    }

    public function create(Request $request, Response $response, $args)
    { 
      $payload = $this->getPayLoad();
      return $response
        ->withStatus(self::HTTP_REQUEST_OK)
        ->withJson(array(
          "token" => JWT::encode($payload, $this->secret, $this->algorithm),
          "expires" => $payload['exp']
        ));
    }

    /* Metodos para el login */
    private function getScopesValid () {
      return [
        // "todo.create",
        "todo.read",
        "todo.update",
        "todo.delete",
        "todo.list",
        "todo.all"
      ];
    }

    private function getPayLoad() {
      $now = new \DateTime();
      $future = new \DateTime("now +2 hours");
      $jti = base64_encode(random_bytes(16));

      return [
        "iat" => $now->getTimeStamp(),
        "exp" => $future->getTimeStamp(),
        "jti" => $jti,
        "scope" => self::getScopesValid()
      ];
    }
  }