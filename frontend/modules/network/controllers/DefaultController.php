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
             'query' => $tmod->forNet($id)->player_progress(Yii::$app->user->id),
             'sort'=> [
                'defaultOrder' => ['status'=>SORT_DESC ,'scheduled_at'=>SORT_ASC, 'difficulty' => SORT_ASC,'ip' => SORT_ASC, 'name' => SORT_ASC]
             ],
             'pagination' => [
                 'pageSizeParam'=>'target-perpage',
                 'pageParam'=>'target-page',
             ]
         ]);
         $targetProgressProvider->setSort([
            'attributes' => array_merge(
                $targetProgressProvider->getSort()->attributes,
                [
                    'headshots' => [
                      'asc' => ['total_headshots'=>SORT_ASC],
                      'desc' => ['total_headshots'=>SORT_DESC],
                    ],
                    'total_findings' => [
                      'asc' => ['total_findings'=>SORT_ASC],
                      'desc' => ['total_findings'=>SORT_DESC],
                    ],
                    'total_treasures' => [
                      'asc' => ['total_treasures'=>SORT_ASC],
                      'desc' => ['total_treasures'=>SORT_DESC],
                    ],
                    'progress'=>[
                      'asc'=>['(player_points/total_points)*100'=>SORT_ASC],
                      'desc'=>['(player_points/total_points)*100'=>SORT_DESC],
                    ]
                ]
            ),
        ]);

         return $this->render('view', [
             'networkTargetProvider'=>$targetProgressProvider,
             'model' => $this->findModel($id),
         ]);
     }
     protected function findModel(int $id)
     {
         if(($model=Network::findOne(['id'=>$id])) !== null && ($model->active || (!\Yii::$app->user->isGuest && \Yii::$app->user->identity->isAdmin)))
         {
             return $model;
         }

         throw new NotFoundHttpException('The requested network does not exist.');
     }

}
