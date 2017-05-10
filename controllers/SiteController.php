<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Usuario;

use yii\helpers\ArrayHelper;
use app\models\TipoIdentificacion;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
     public function behaviors()
      {
        return [
          'access' => [
              'class' => AccessControl::className(),
              'only' => ['logout', 'superAdmin', 'Admin', 'Agent', 'Customer'],
              'rules' => [
                  [
                     //Los usuarios simples tienen permisos sobre las siguientes acciones
                     'actions' => ['logout', 'SuperAdmin'],
                     //Esta propiedad establece que tiene permisos
                     'allow' => true,
                     //Usuarios autenticados, el signo ? es para invitados
                     'roles' => ['@'],
                     //Este método nos permite crear un filtro sobre la identidad del usuario
                     //y así establecer si tiene permisos o no
                     'matchCallback' => function ($rule, $action) {
                        //Llamada al método que comprueba si es un usuario simple
                        return User::isSuperAdmin(Yii::$app->user->identity->id);
                    },
                  ],
                  [
                      //El administrador tiene permisos sobre las siguientes acciones
                      'actions' => ['logout', 'Admin'],
                      //Esta propiedad establece que tiene permisos
                      'allow' => true,
                      //Usuarios autenticados, el signo ? es para invitados
                      'roles' => ['@'],
                      //Este método nos permite crear un filtro sobre la identidad del usuario
                      //y así establecer si tiene permisos o no
                      'matchCallback' => function ($rule, $action) {
                          //Llamada al método que comprueba si es un administrador
                          return User::isAdmin(Yii::$app->user->identity->id);
                      },
                  ],
                  [
                      //El administrador tiene permisos sobre las siguientes acciones
                      'actions' => ['logout', 'Agent'],
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
                  [
                      //El administrador tiene permisos sobre las siguientes acciones
                      'actions' => ['logout', 'Customer'],
                      //Esta propiedad establece que tiene permisos
                      'allow' => true,
                      //Usuarios autenticados, el signo ? es para invitados
                      'roles' => ['@'],
                      //Este método nos permite crear un filtro sobre la identidad del usuario
                      //y así establecer si tiene permisos o no
                      'matchCallback' => function ($rule, $action) {
                          //Llamada al método que comprueba si es un administrador
                          return User::isCustomer(Yii::$app->user->identity->id);
                      },
                  ]
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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionPrivacypolicy()
    {
        return $this->render('privacypolicy');
    }

    /**
     * DF Edit password
     *
     */
    public function actionDatos($msg = null, $msgtipo = null){

       $model = Usuario::findOne(Yii::$app->user->identity->id);
       $model->clave        = '';
       $model->clave_repeat = '';

       if ($model->load(Yii::$app->request->post()) && $model->validate()) {

           if($model->clave == $model->clave_repeat ){

               if(!$model->clave_anterior){
                   $msg = "No puede estar vacia la clave anterior";
                   $msgtipo = 'danger';
               }else{
                   $claveant = crypt($model->clave_anterior, Yii::$app->params["salt"]);

                   $verificar = Usuario::find()
                           ->where([
                           'id' =>Yii::$app->user->identity->id,
                           'clave' => $claveant])
                           ->count();

                   if($verificar){
                       $tabla = Usuario::findOne(Yii::$app->user->identity->id);
                       $tabla->clave = crypt($model->clave, Yii::$app->params["salt"]);

                       if($tabla->save()){
                          $msg = "Datos actualizados exitosamente.";
                          $msgtipo = 'success';
                       }else{
                           print_r($model->getErrors());
                       }
                   }else{
                      $msg = "La clave anterior no es correcta";
                      $msgtipo = 'danger';
                   }
               }
           }else{
              $msg = "Las claves deben ser iguales";
              $msgtipo = 'danger';
           }
      }
      $model->clave        = '';
      $model->clave_anterior = '';
      $model->clave_repeat = '';

      return $this->render('datos', [
                                 'model' => $model,
                                 "msg"   => $msg,
                                 "msgtipo"   => $msgtipo,
                         ]);

   }


   public function actionCreate()
   {
       $model = new Usuario();
       $msg = "";

       $tipoIdentificacion = ArrayHelper::map(TipoIdentificacion::find()->all(), 'id', 'nombre');

       if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
         //return $this->redirect(['view', 'id' => $model->id]);
         Yii::$app->response->format = Response::FORMAT_JSON;
         return ActiveForm::validate($model);
       }

       if ($model->load(Yii::$app->request->post())) {

         $model->id_role      = 3;

         if($model->validate())
         {
           //Preparamos la consulta para guardar el usuario
           $table = new Usuario;
           $table->nombres     = trim(ucwords($model->nombres));
           $table->apellidos   = trim(ucwords($model->apellidos));
           $table->id_tipo_identificacion      = $model->id_tipo_identificacion;
           $table->id_role      = 3;

           $table->identificacion = $model->identificacion;
           $table->email       = trim(strtolower($model->email));
           $table->username       = trim(strtolower($model->email));

           //Encriptamos el password
           $table->clave       = crypt($model->clave, Yii::$app->params["salt"]);
           $table->clave_repeat       = crypt($model->clave_repeat, Yii::$app->params["salt"]);

           //Si el registro es guardado correctamente
           if ($table->save())
           {
             return $this->redirect(['login']);

           }else{
             $msg = "Ha ocurrido un error al llevar a cabo tu registro";
               echo "<br /><br /><br /><br /><br /><br /><br />....";
               var_dump($table->getErrors());
           }
         }else{
           echo "<br /><br /><br /><br /><br /><br /><br />ñlk";

           $msg = "Ha ocurrido un error al llevar a cabo tu registro";
           var_dump($model->getErrors());

         }
       } else {
           return $this->render('create', [
               'model' => $model,
               "msg"   => $msg,
               'tipoIdentificacion' => $tipoIdentificacion,
           ]);
       }
   }
}
