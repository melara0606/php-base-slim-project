<?php
  namespace Melara\Helpers\Controllers;

  use Melara\Helpers\Controllers\ControllerLogin;
  use Melara\Helpers\Response\UnauthorizedResponse;
  use Melara\Helpers\Controllers\Interfaces\ControllerBase;

  abstract class Controller extends ControllerLogin implements ControllerBase {
    public function __construct($app) {
      parent::__construct($app);
    }

    public function UnauthorizedResponse()
    {
      return new UnauthorizedResponse('Forbidden', self::HTTP_REQUEST_FORBIDDEN, 'Forbidden');
    }

    public function responseStatus200($response, $data)
    {
      return $this->toJsonView($response, $data , self::HTTP_REQUEST_OK);
    }

    public function UnProblemApiResponse($title = 'Not Implemented')
    {
      return new UnauthorizedResponse($title, self::HTTP_REQUEST_NOT_IMPLEMENTED, 'Not Implemented');
    }

    public function UnNotFound()
    {
      return new UnauthorizedResponse('RECURSO NO ENCONTRADO', self::HTTP_REQUEST_NOT_FOUND, 'Not found');
    }

    protected function editDatabaseBasic($id, $table, $data = array()) {
      $result = null;
      if($id) {
        $result = $this->root->db->where('id', $id)->update($table, $data);
      }

      return $result;
    }

    protected function editDatabase($id, $table, $data = array(), $response, $campo = 'id') {
      if($id) {
        $result = $this->root->db->where($campo, $id)->update($table, $data);

        if($result){
          $record = $this->root->db->where($campo, $id)->getOne($table);
          return $this->responseStatus200($response, [
            "record" => $record
          ]);
        }else {
          return $this->UnProblemApiResponse('Tenemos un problema con la base de datos');
        }
      } else {
        return $this->UnProblemApiResponse('El id es requerido en la peticion');
      }
    }

    protected function getByIdNotFound($record = null, $response) {
      if($record){
        return $this->responseStatus200($response, $record);
      }else{
        return $this->UnNotFound();
      }
    }

    protected function uniQUnique($modulo = '') {
      $generadorPrivate = "melara-project-academica-$modulo";
      return substr(
        base64_encode(
          md5( uniqid($generadorPrivate) )
        ), 0, 24
      );
    }
  }