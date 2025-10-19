<?php

namespace app\modules\gameplay\controllers;

use Yii;
use app\modules\gameplay\models\Treasure;
use app\modules\gameplay\models\TreasureSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * TreasureController implements the CRUD actions for Treasure model.
 */
class TreasureController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), []);
  }

  /**
   * Lists all Treasure models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new TreasureSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }
  /**
   * Lists all Treasure models.
   * @return mixed
   */
  public function actionValidate()
  {
    $string = (string)trim(Yii::$app->request->post('code'));

    if ($string !== "") {
      $secretKey = Yii::$app->sys->treasure_secret_key;
      $results = Yii::$app->db->createCommand("select treasure.id,player.id as player_id from treasure,player where md5(HEX(AES_ENCRYPT(CONCAT(code, player.id), :secretKey))) LIKE :code", [':secretKey' => $secretKey, ':code' => $string])->queryOne();
      if ($results === [] || $results===false) {
        Yii::$app->session->setFlash('warning', Yii::t('app', "Code not found."));
      } else {
        $player = \app\modules\frontend\models\Player::findOne($results['player_id']);
        $treasure = Treasure::findOne($results['id']);
        $profileLink = \app\widgets\ProfileLink::widget([
          'username' => $player->username,
          'actions' => false
        ]);
        if($player->teamPlayer!==NULL)
        {
          $msg = sprintf('Code belongs to player [%s] from team [%s] for target %s and treasure %s', $profileLink,$player->teamPlayer->team->name, $treasure->target->name, $treasure->name);
        }
        else
        {
          $msg = sprintf('Code belongs to player [%s] for target %s and treasure %s', $profileLink, $treasure->target->name, $treasure->name);
        }

        Yii::$app->session->setFlash('success', $msg);
        $string='';
      }
    }

    return $this->render('validate',['code'=>$string]);
  }

  /**
   * Displays a single Treasure model.
   * @param integer $id
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
   * Creates a new Treasure model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new Treasure();
    if (\app\modules\gameplay\models\Target::find()->count() == 0) {
      // If there are no player redirect to create player page
      Yii::$app->session->setFlash('warning', Yii::t('app', "No targets found create one first."));
      return $this->redirect(['/gameplay/target/create']);
    }

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing Treasure model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing Treasure model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id)
  {
    $this->findModel($id)->delete();

    return $this->redirect(['index']);
  }

  /**
   * Finds the Treasure model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return Treasure the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = Treasure::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
