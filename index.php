<?php
  /* Para la configuracion del framework  */
  require 'vendor/autoload.php';

  // Configuracion del framework
  $settings = require './src/settings.php';
  
  // Creacion de la app
  $app = new \Slim\App($settings);

  // Dependencias
  require './src/dependencies.php';
  
  // Rutas
  require './src/routes.php';
  
  // !Run
  $app->run();