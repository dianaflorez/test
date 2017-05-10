<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuario2".
 *
 * @property integer $id
 * @property string $nombres
 * @property string $apellidos
 * @property string $id_tipo_identificacion
 * @property string $identificacion
 * @property string $email
 * @property string $username
 * @property string $direccion
 * @property string $telefono
 * @property integer $id_role
 *
 * @property Role $idRole
 */
class Usuario extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $clave_repeat;
    public $clave_anterior;

    public static function tableName()
    {
        return 'usuario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombres', 'apellidos', 'id_tipo_identificacion', 'identificacion', 'email', 'id_role', 'clave', 'clave_repeat'], 'required'],
            [['id_role'], 'integer'],
            [['nombres', 'apellidos', 'telefono'], 'string', 'max' => 15],
            [['identificacion'], 'string', 'max' => 12],
            [['email'], 'string', 'max' => 100],
            [['direccion'], 'string', 'max' => 50],
            [['identificacion'], 'unique'],
            [['email', 'username'], 'unique'],

            ['clave_anterior', 'match', 'pattern' => "/^.{5,16}$/", 'message' => 'Mínimo 5 y máximo 16 caracteres'],

            ['clave', 'match', 'pattern' => "/^.{5,16}$/", 'message' => 'Mínimo 5 y máximo 16 caracteres'],
            ['clave_repeat', 'compare', 'compareAttribute' => 'clave', 'message' => 'Las claves no coinciden'],

            [['id_role'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['id_role' => 'id']],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombres' => 'Nombres',
            'apellidos' => 'Apellidos',
            'id_tipo_identificacion' => 'Tipo Identificacion',
            'identificacion' => 'Identificacion',
            'email' => 'Email',
            'username' => 'Nombre de Usuario',
            'clave' => 'Clave',
            'clave_repeat' => 'Repetir Clave',
            'direccion' => 'Direccion',
            'telefono' => 'Telefono',
            'id_role' => 'Role',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'id_role']);
    }

    public function getIdTipoIdentificacion()
    {
        return $this->hasOne(TipoIdentificacion::className(), ['id' => 'id_tipo_identificacion']);
    }
}
