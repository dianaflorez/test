<?php

namespace app\controllers;

use Yii;
use app\models\Usuario;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\Role;
use app\models\TipoIdentificacion;
use yii\helpers\ArrayHelper;

use app\models\Formsearch;
use yii\data\Pagination;
use yii\helpers\Html;

use yii\filters\AccessControl;
use app\models\User;

/**
 * UsuarioController implements the CRUD actions for Usuario model.
 */
class UsuarioController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
    return [
      'access' => [
          'class' => AccessControl::className(),
          'only' => ['index', 'create', 'update','view'],
          'rules' => [

              [
                 //Los usuarios simples tienen permisos sobre las siguientes acciones
                 'actions' => ['index', 'create', 'update','view'],
                 //Esta propiedad establece que tiene permisos
                 'allow' => true,
                 //Usuarios autenticados, el signo ? es para invitados
                 'roles' => ['@'],
                 //Este método nos permite crear un filtro sobre la identidad del usuario
                 //y así establecer si tiene permisos o no
                 'matchCallback' => function ($rule, $action) {
                    //Llamada al método que comprueba si es un usuario simple
                    return User::isAdmin(Yii::$app->user->identity->id);
                },
             ],
             [
                    //El administrador tiene permisos sobre las siguientes acciones
                    'actions' => ['index', 'create', 'view'],
                    //Esta propiedad establece que tiene permisos
                    'allow' => true,
                    //Usuarios autenticados, el signo ? es para invitados
                    'roles' => ['@'],
                    //Este método nos permite crear un filtro sobre la identidad del usuario
                    //y así establecer si tiene permisos o no
                    'matchCallback' => function ($rule, $action) {
                        //Llamada al método que comprueba si es un administrador
                        return User::isAgent(Yii::$app->user->identity->id);
                    },
                ],
          ],
      ],
       //Controla el modo en que se accede a las acciones, en este ejemplo a la acción logout
       //sólo se puede acceder a través del método post
      'verbs' => [
          'class' => VerbFilter::className(),
          'actions' => [
              'logout' => ['post'],
          ],
      ],
    ];
  }

    /**
     * Lists all Usuario models.
     * @return mixed
     */
    public function actionIndex($msg = null)
    {

      $form = new Formsearch;
      $search = null;
      if($form->load(Yii::$app->request->get()))
      {
          if ($form->validate())
          {
              $search = strtoupper(Html::encode($form->q));
              $table = Usuario::find()
                      ->orWhere(["like", "upper(nombres)", $search])
                      ->orWhere(["like", "apellidos", $search])
                      ;

              $count = clone $table;
              $pages = new Pagination([
                  "pageSize" => 3,
                  "totalCount" => $count->count()
              ]);
              $model = $table
                      ->offset($pages->offset)
                      ->limit($pages->limit)
                      ->all();
          }
          else
          {
              $form->getErrors();
          }
      }else{
              $table = Usuario::find();
              $count = clone $table;
              $pages = new Pagination([
                  "pageSize" => 4,
                  "totalCount" => $count->count(),
              ]);
              $model = $table
                      ->offset($pages->offset)
                      ->limit($pages->limit)
                      ->all();
      }

      return $this->render('index', [
       //   'searchModel'   => $searchModel,
        //  'dataProvider'  => $dataProvider,
          'model'         => $model,
          "form"          => $form,
          "search"        => $search,
          "pages"         => $pages,
          "msg"           => $msg,
      ]);
    }



    public function actionDatos($msg = null){
        $this->layout='main';

        $model = Usuario::findOne(Yii::$app->user->identity->id);
        $model->clave        = "";
        $model->clave_repeat = "";

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if($model->clave == $model->clave_repeat ){

                if(!$model->clave_anterior){
                    return $this->redirect(['datos',
                    'msg' => "No puede estar vacia la clave anterior"]);
                }else{
                    $claveant = crypt($model->clave_anterior, Yii::$app->params["salt"]);

                    $verificar = Usuario::find()
                            ->where([
                            'id' =>Yii::$app->user->identity->id,
                            'clave' =>$claveant])
                            ->count();

                    if($verificar){
                        $tabla = Usuario::findOne(Yii::$app->user->identity->id);
                        $tabla->clave = crypt($model->clave, Yii::$app->params["salt"]);
                        $tabla->clave_repeat = $tabla->clave;

                        if($tabla->save()){
                           return $this->redirect(['datos', 'msg' => "Datos actualizados exitosamente."]);
                        }else{
                            print_r($tabla->getErrors());

                        }
                    }else{
                    return $this->redirect(['datos',
                        'msg' => "La clave anterior no es correcta"]);
                    }
                }
            }else{
                return $this->redirect(['datos',
                    'msg' => "Las claves deben ser iguales"]);

            }
        } else {
            return $this->render('datos', [
                                        'model' => $model,
                                        "msg"   => $msg,

                                ]);
        }
    }

    /**
     * Displays a single Usuario model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
      $model  = $this->findModel($id);

      //Usuario que modifico
      $usumod = Usuario::findOne(['id' => $model->id]);
      $usumod = ucwords($usumod->nombres.' '.$usumod->apellidos);

      $tipoidi = TipoIdentificacion::findOne(['id' => $model->id_tipo_identificacion]);
      $tipousu = Role::findOne(['id' => $model->id_role]);

      return $this->render('view', [
          'model'     => $model,
          'usumod'    => $usumod,
          'tipoIdentificacion'      => $tipoidi->nombre,
          'tipoUsuario'      => $tipousu->nombre,
      ]);
    }

    /**
     * Creates a new Usuario model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Usuario();
        $msg = "";

        $tipoUsuario = ArrayHelper::map(Role::find()->all(), 'id', 'nombre');
        $tipoIdentificacion = ArrayHelper::map(TipoIdentificacion::find()->all(), 'id', 'nombre');

        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
          //return $this->redirect(['view', 'id' => $model->id]);
          Yii::$app->response->format = Response::FORMAT_JSON;
          return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {

          if($model->validate())
          {
            //Preparamos la consulta para guardar el usuario
            $table = new Usuario;
            $table->nombres     = trim(ucwords($model->nombres));
            $table->apellidos   = trim(ucwords($model->apellidos));
            $table->id_tipo_identificacion      = $model->id_tipo_identificacion;
            $table->id_role      = $model->id_role;

            $table->identificacion = $model->identificacion;
            $table->email       = trim(strtolower($model->email));
            $table->username       = trim(strtolower($model->email));

            //Encriptamos el password
            $table->clave       = crypt($model->clave, Yii::$app->params["salt"]);
            $table->clave_repeat       = crypt($model->clave_repeat, Yii::$app->params["salt"]);

            //Si el registro es guardado correctamente
            if ($table->save())
            {
              return $this->redirect(['view', 'id' => $table->id]);

            }else{
              $msg = "Ha ocurrido un error al llevar a cabo tu registro";
                echo "<br /><br /><br /><br /><br /><br /><br />";
                var_dump($table->getErrors());
            }
          }else{
            echo "<br /><br /><br /><br /><br /><br /><br />";

            $msg = "Ha ocurrido un error al llevar a cabo tu registro";
          }
        } else {
            return $this->render('create', [
                'model' => $model,
                "msg"   => $msg,
                'tipoUsuario' => $tipoUsuario,
                'tipoIdentificacion' => $tipoIdentificacion,
            ]);
        }
    }

    /**
     * Updates an existing Usuario model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $tipoUsuario = ArrayHelper::map(Role::find()->all(), 'id', 'nombre');
        $tipoIden = ArrayHelper::map(TipoIdentificacion::find()->all(), 'id', 'nombre');
        $model->clave_repeat = $model->clave;

        if ($model->load(Yii::$app->request->post())) {

          if($model->validate())
          {
            //Preparamos la consulta para guardar el usuario
            $table = $this->findModel($id);
            $table->nombres     = trim(ucwords($model->nombres));
            $table->apellidos   = trim(ucwords($model->apellidos));
            $table->id_tipo_identificacion      = $model->id_tipo_identificacion;
            $table->id_role      = $model->id_role;

            $table->identificacion = $model->identificacion;
            $table->email       = trim(strtolower($model->email));
            $table->username       = trim(strtolower($model->email));

            //Encriptamos el password
            $table->clave       = crypt($model->clave, Yii::$app->params["salt"]);
            $table->clave_repeat       = crypt($model->clave_repeat, Yii::$app->params["salt"]);

            //Si el registro es guardado correctamente
            if ($table->save())
            {
              return $this->redirect(['view', 'id' => $table->id]);

            }else{
              $msg = "Ha ocurrido un error al llevar a cabo tu registro--";
                echo "<br /><br /><br /><br /><br /><br /><br />";
                var_dump($table->getErrors());
            }
          }else{
            echo "<br /><br /><br /><br /><br /><br /><br />ll";

            $msg = "Ha ocurrido un error al llevar a cabo tu registro";
          }

        } else {
            return $this->render('update', [
                'model' => $model,
                'tipoUsuario' => $tipoUsuario,
                'tipoIdentificacion' => $tipoIden,
            ]);
        }
    }

    /**
     * Deletes an existing Usuario model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        $id  = Html::encode($_POST["id"]);

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Usuario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Usuario the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Usuario::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
