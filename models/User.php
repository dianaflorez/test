<?php

namespace app\models;

class User extends \yii\base\Object implements \yii\web\IdentityInterface
{

  public $id;
  public $nombres;
  public $apellidos;
  public $id_tipo_identificacion;
  public $identificacion;
  public $username;
  public $email;
  public $clave;
  public $direccion;
  public $telefono;
  public $id_role;
  public $authkey;
  public $accesstoken;
  public $id_tipo_usuario;

  public static function isSuperAdmin($id)
  {
     if (Usuario::findOne(['id' => $id, 'id_role' => 0])){
          return true;
     } else {

          return false;
     }
  }

  public static function isAdmin($id)
  {
     if (Usuario::findOne(['id' => $id, 'id_role' => 1])){
      return true;
     } else {

      return false;
     }
  }


  public static function isAgent($id)
  {
     if (Usuario::findOne(['id' => $id, 'id_role' => 2])){
      return true;
     } else {

      return false;
     }
  }

  public static function isCustomer($id)
  {
     if (Usuario::findOne(['id' => $id, 'id_role' => 3])){
      return true;
     } else {

      return false;
     }
  }

  /**
   * @inheritdoc
   */

  /* busca la identidad del usuario a través de su $id */

  public static function findIdentity($id)
  {

      $user = Usuario::find()
              ->andWhere("id=:id", ["id" => $id])
              ->one();

      return isset($user) ? new static($user) : null;
  }

  /**
   * @inheritdoc
   */

  /* Busca la identidad del usuario a través de su token de acceso */
  public static function findIdentityByAccessToken($token, $type = null)
  {

      $users = Usuario::find()
              ->where("activate=:activate", [":activate" => 1])
              ->andWhere("accessToken=:accessToken", [":accessToken" => $token])
              ->all();

      foreach ($users as $user) {
          if ($user->accessToken === $token) {
              return new static($user);
          }
      }

      return null;
  }

  /**
   * Finds user by username
   *
   * @param  string      $username
   * @return static|null
   */

  /* Busca la identidad del usuario a través del username */
  public static function findByUsername($username)
  {
      $users = Usuario::find()
              ->andWhere("username=:username", [":username" => $username])
              ->all();

      foreach ($users as $user) {
          if (strcasecmp($user->username, $username) === 0) {
              return new static($user);
          }
      }

      return null;
  }

  /**
   * @inheritdoc
   */

  /* Regresa el id del usuario */
  public function getId()
  {
      return $this->id;
  }

  /**
   * @inheritdoc
   */

  /* Regresa la clave de autenticación */
  public function getAuthKey()
  {
      return $this->authkey;
  }

  /**
   * @inheritdoc
   */

  /* Valida la clave de autenticación */
  public function validateAuthKey($authkey)
  {
      return $this->authkey === $authkey;
  }

  /**
   * Validates clave
   *
   * @param  string  $password password to validate
   * @return boolean if password provided is valid for current user
   */
  public function validatePassword($clave)
  {
      /* Valida el password */
      if (crypt($clave, $this->clave) == $this->clave)
      {
      return $clave === $clave;
      }
  }
}
