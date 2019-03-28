<?php

  $app->group('/v1', function () {
    $this->group('/auth', function () {
      $this->group('/login', function () { new Melara\Controllers\LoginController($this); });
    });

    $this->group('/todo', function (){ new Melara\Controllers\TodoController($this); });
  });