<?php

  $app->group('/v1', function () {
    // rutas para el login
    $this->group('/auth', function () {
      $this->group('/login', function () { new Melara\Controllers\LoginController($this); });
    });
  });