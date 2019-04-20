<?php

  $app->group('/v1', function () {
    $this->group('/auth', function () {
      $this->group('/login', function () { new Melara\Controllers\LoginController($this); });
    });

    $this->group('/modulos', function () {
      $this->group('/niveles', function () { new Melara\Controllers\NivelesController($this); });
      $this->group('/perfiles', function () { new Melara\Controllers\PerfilesController($this); });
      $this->group('/usuarios', function () { new Melara\Controllers\UsuariosController($this); });
      $this->group('/personales', function () { new Melara\Controllers\PersonalesControllers($this); });
      $this->group('/sucursales', function () { new Melara\Controllers\SucursalesController($this); });
    });

    // $this->group('/todo', function (){ new Melara\Controllers\TodoController($this); });
  });