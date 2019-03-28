<?php

  namespace Melara\Helpers;

  class Token {
    public $decoded;

    public function __construct(array $decoded = array())
    {
      $this->populate($decoded);
    }

    public function populate(array $decoded)
    {
      $this->decoded = $decoded;
    }

    public function hasScope(array $scope)
    {
      return !!count(array_intersect($scope, $this->decoded["scope"]));
    }
  }