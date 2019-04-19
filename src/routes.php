<?php

  $app->group('/v1', function () {
    $this->group('/auth', function () {
      $this->group('/login', function () { new Melara\Controllers\LoginController($this); });
    });

    $this->group('/modulos', function () {
      $this->group('/niveles', function () { new Melara\Controllers\NivelesController($this); });
      $this->group('/perfiles', function () { new Melara\Controllers\PerfilesController($this); });
    });

    // $this->group('/todo', function (){ new Melara\Controllers\TodoController($this); });
  });