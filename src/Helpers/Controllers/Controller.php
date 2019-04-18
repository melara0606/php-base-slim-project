<?php
  namespace Melara\Helpers\Controllers;

  use Melara\Helpers\Controllers\Interfaces\ControllerBase;
  use Melara\Helpers\Controllers\ControllerLogin;

  abstract class Controller extends ControllerLogin implements ControllerBase {
    public function __construct($app) {
      parent::__construct($app);
    }
  }