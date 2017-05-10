<?php

use yii\helpers\Html;
use yii\grid\GridView;

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\data\Pagination;
use yii\bootstrap\Alert;
use yii\widgets\LinkPager;

use yii\jui\AutoComplete;
use yii\web\JsExpression;

use app\models\Usuario;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuario-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Crear Usuario', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php $f = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("usuario/index"),
    "enableClientValidation" => true,
]);
?>
<div class="form-group">
    <?= $f->field($form, "q")->input("search") ?>
</div>
<?= Html::submitButton("Buscar", ["class" => "btn btn-primary"]) ?>
<?php $f->end() ?>
<h3><?= $search ?></h3>


    <div class="rwd">

      <table class="table table-striped  table-bordered table-showPageSummary">
        <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Dirección</th>
            <th class="action-column ">&nbsp;</th>
        </tr>
        <?php foreach($model as $row): ?>
        <tr>
            <td><?= $row->id ?></td>
            <td><?= $row->nombres.' '. $row->apellidos ?></td>
            <td><?= $row->email ?></td>
            <td><?= $row->direccion ?></td>

        <td>
          <!-- View -->
          <a href="<?= Url::toRoute(["usuario/view", "id" => $row->id]) ?>" title="Ver" aria-label="Ver">
            <span class="glyphicon glyphicon-eye-open"></span>
          </a>
          <!--End View-->

          <!-- Update -->
          <a href="<?= Url::toRoute(["usuario/update", "id" => $row->id]) ?>" title="Actualizar" aria-label="Actualizar">
            <span class="glyphicon glyphicon-pencil"></span>
          </a>
          <!--End Update-->

          <!--Delete-->
           <a href="#" data-toggle="modal" data-target="#id_<?= $row->id ?>" title="Eliminar" aria-label="Eliminar">
           <span class="glyphicon glyphicon-trash"></span>
           </a>
              <div class="modal fade" role="dialog" aria-hidden="true" id="id_<?= $row->id ?>">
                    <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                  <h4 class="modal-title">Eliminar Empresa</h4>
                            </div>
                            <div class="modal-body">
                                  <p>¿Realmente deseas eliminar esta usuario con nombres <?= $row->nombres.' '.$row->apellidos ?>?</p>
                            </div>
                            <div class="modal-footer">
                            <?= Html::beginForm(Url::toRoute("usuario/delete"), "POST") ?>
                                  <input type="hidden" name="id" value="<?= $row->id ?>">
                                  <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                  <button type="submit" class="btn btn-primary">Eliminar</button>
                            <?= Html::endForm() ?>
                            </div>
                          </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
              </div><!-- /.modal -->
          </a>
          <!--End Delete-->
      </td>
        <?php endforeach ?>
 </table>


  </div>
</div>
<?= LinkPager::widget([
    "pagination" => $pages,
]); ?>
