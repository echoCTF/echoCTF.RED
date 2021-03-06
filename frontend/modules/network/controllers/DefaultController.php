<?php

namespace app\modules\network\controllers;

use Yii;
use app\modules\network\models\Network;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
/**
 * DefaultController implements the CRUD actions for Network model.
 */
class DefaultController extends \app\components\BaseController
{

  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
      return ArrayHelper::merge(parent::behaviors(),[
        'access' => [
          'class' => AccessControl::class,
          'only' => ['index', 'view', 'subscribe'],
          'rules' => [
              'eventStartEnd'=>[
                 'actions' => ['index', 'view', 'subscribe'],
              ],
              'teamsAccess'=>[
                 'actions' => ['index', 'view', 'subscribe'],
              ],
              'disabledRoute'=>[
                  'actions' => ['index', 'view', 'subscribe'],
              ],
              [
                  'allow' => true,
                  'roles'=>['@']
              ],
          ],
      ]]);
  }


    /**
     * Lists all Network models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider=new ActiveDataProvider([
            'query' => Network::find()->active()->orderBy(['weight'=>SORT_ASC,'name'=>SORT_ASC, 'id'=>SORT_ASC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * View Network model by id.
     * @return mixed
     */
     public function actionView(int $id)
     {
         $tmod=\app\modules\target\models\Target::find();
         $targetProgressProvider=new ActiveDataProvider([
             'query' => $tmod->player_progress(Yii::$app->user->id)->forNet($id),
             'pagination' => [
                 'pageSizeParam'=>'target-perpage',
                 'pageParam'=>'target-page',
             ]

         ]);

         return $this->render('view', [
             'networkTargetProvider'=>$targetProgressProvider,
             'model' => $this->findModel($id),
         ]);
     }
     protected function findModel(int $id)
     {
         if(($model=Network::findOne(['id'=>$id])) !== null && $model->active)
         {
             return $model;
         }

         throw new NotFoundHttpException('The requested network does not exist.');
     }

}
