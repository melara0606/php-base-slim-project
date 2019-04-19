<?php
  use Tuupola\Middleware\JwtAuthentication;

  use Melara\Helpers\Token;
  use Melara\Helpers\Response\UnauthorizedResponse;

  $container = $app->getContainer();

  $container['token'] = function () {
    return new Token;
  };

  $container["JwtAuthentication"] = function ($container) {
    return new JwtAuthentication([
      "path" => "/v1",
      "ignore" => ["/v1/auth/login"],
      "attribute" => "jwt",
      "relaxed" => ["192.168.50.52", "127.0.0.1", "localhost"],
      "secret" => "supersecretkeyyoushouldnotcommittogithub",
      "logger" => $container["logger"],
      "algorithm" => ["HS256", "HS384"],
      "before" => function ($request, $arguments) use ($container) {
        $container["token"]->populate($arguments["decoded"]);
      },
      "error" => function ($response, $arguments) {
        return new UnauthorizedResponse($arguments['message'], 401, 'Unauthorized');
      }
    ]);
  };

  $app->add("JwtAuthentication");
