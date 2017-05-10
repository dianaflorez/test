<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\Alert;
use yii\bootstrap\ActiveForm;


//IMPORTANTE Sin esto no funciona el menu del logo
use yii\bootstrap\Tabs;
Tabs::widget();
//FIN
?>

<h3>
<?php
  if( !$msgtipo ){
    $msgtipo = 'info';
  }
  if($msg){
    echo Alert::widget([
        'options' => [
            'class' => 'alert-'.$msgtipo,
        ],
        'body' => $msg,
    ]);
  }
?>
</h3>

<div class="usuario-form col-md-7">

    <?php $form = ActiveForm::begin([
            'method' => 'post',
            'id'     => 'formulario',

          //  'enableClientValidation' => false,
         //   'enableAjaxValidation' => true,
    ]); ?>


    <?= "Datos de Usuario" ?>
    <br />
    <br />
    <?= "Nombre usuario: ".$model->username; ?>
    <br />
    <?= "Nombre: ".$model->nombre1." ".$model->apellido1 ?>
    <br />

    <?= "Email: ".$model->email ?>
    <br />
    <br />
    <br />

    <?= $form->field($model, "clave_anterior")->input("password") ?>

    <?= $form->field($model, 'clave')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, "clave_repeat")->input("password") ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Nuevo' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
