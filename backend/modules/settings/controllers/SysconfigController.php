<?php

namespace app\modules\settings\controllers;

use Yii;
use app\modules\settings\models\Sysconfig;
use app\modules\settings\models\SysconfigSearch;
use app\modules\settings\models\ConfigureForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SysconfigController implements the CRUD actions for Sysconfig model.
 */
class SysconfigController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
          'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index','create','update','view','configure'],
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Sysconfig models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysconfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sysconfig model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Sysconfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Sysconfig();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Sysconfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success','<code>'.$model->id.'</code> updated.');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Creates/Updates a Sysconfig set model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionConfigure()
    {
      $model = new ConfigureForm();
      if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['configure']);
      }

      return $this->render('configure', [
          'model' => $model,
      ]);
    }

    /**
     * Deletes an existing Sysconfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionMigrate()
    {
        $pre_up=ob_get_clean();
        ob_start();
        $oldapp=Yii::$app;
        defined('STDIN') or define('STDIN', fopen('php://input', 'r'));
        defined('STDOUT') or define('STDOUT', fopen('php://output', 'w'));
        $runner = new \yii\console\Application([
            'id'       => 'basic-console',
            'controllerNamespace' => dirname(__DIR__.'/../../../migrations'),
            'basePath' => dirname(__DIR__ . '/../../../config'),
            'components' => [
                        'db' => Yii::$app->db,
                    ],
        ]);
        try
        {
          $newstatus=$runner->runAction('migrate/new');
          $new_up=$this->stripYii();
          if(count($new_up)>0)
          {
            ob_start();
            $upstatus=$runner->runAction('migrate/up',[1,'interactive'=>0]);
            $post_up=$this->stripYii();

            if($upstatus==0)
            {
              ob_start();
              echo "migrate/down\n";
              $runner->runAction('migrate/down',[1,'interactive'=>0]);
              $post_down=$this->stripYii();
            }
          }
        }
        catch(\Exception $ex)
        {
            echo $ex->getMessage();
        }
        Yii::$app=$oldapp;
        var_dump($new_up);
        var_dump($post_up);
        var_dump($post_down);
        die();
    }
    /**
     * Finds the Sysconfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Sysconfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sysconfig::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    protected function stripYii()
    {
      $array=explode("\n",trim(ob_get_clean()));
      for ($i=0;$i<3;$i++) array_shift($array);
      return array_map('trim',$array);

    }
}
