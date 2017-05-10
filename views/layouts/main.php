<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="stylesheet" type="text/css" href="css/general.css">
    <script src="js/general.js"></script>
</head>
<body>
<?php $this->beginBody() ?>
<?php

  $role = "wiwo";
  $ac_usuario = false;
  $ac_datos = false;

  if(!Yii::$app->user->isGuest){
      $role = Yii::$app->user->identity->id_role;
      if($role == 0){
          $role = "superadmin";
          $ac_usuario = true;
      }elseif($role == 1){
          $role = "admin";
          $ac_usuario = true;
      }elseif($role == 2){
          $role = "Agent";
          $ac_usuario = true;
      }elseif($role == 3){
          $role = "Customer";
          $ac_usuario = false;
      }
      $ac_datos = true;

  }

?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Pruebatec',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Inicio', 'url' => ['/site/index']],
            ['label' => 'Registro', 'url' => ['/site/create']],

            [
              'label' => 'Usuarios',
              'url' => ['usuario/index'],
              'visible' => $ac_usuario
            ],

            [
              'label' => 'Mis Datos',
              'url' => ['usuario/datos'],
              'visible' => $ac_usuario
            ],

            Yii::$app->user->isGuest ? (
                          ['label' => 'Login', 'url' => ['/site/login']]
                      ) :
                      (
                          '<li>'
                          . Html::beginForm(['/site/logout'], 'post')
                          . Html::submitButton(
                              'Logout (' . Yii::$app->user->identity->username . ')',
                              ['class' => 'btn btn-link']
                          )
                          . Html::endForm()
                          . '</li>'
                      )


        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; PRUEBATEC <?= date('Y') ?></p>

        <p class="pull-right">Implementado por <a href='http://www.ideartics.com' target='_blank' >Diana FLorez</a></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
