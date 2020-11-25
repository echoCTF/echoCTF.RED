<?php

namespace app\modules\network\controllers;

use Yii;
use app\modules\network\models\Network;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

/**
 * DefaultController implements the CRUD actions for Network model.
 */
class DefaultController extends Controller
{

  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
      return [
        'access' => [
          'class' => AccessControl::class,
          'only' => ['index', 'view', 'subscribe'],
          'rules' => [
              [
                 'actions' => ['index', 'view', 'subscribe'],
                 'allow' => false,
                 'matchCallback' => function ($rule, $action) {
                     return Yii::$app->sys->event_start!==false && (time()<Yii::$app->sys->event_start || time()>Yii::$app->sys->event_end);
                 },
                 'denyCallback' => function() {
                   Yii::$app->session->setFlash('info', 'This area is disabled at the moment');
                   return  \Yii::$app->getResponse()->redirect(['/dashboard/index']);
                 }
              ],
              [
                 'actions' => ['index', 'view', 'subscribe'],
                 'allow' => false,
                 'matchCallback' => function ($rule, $action) {
                   if(Yii::$app->sys->team_required===false)
                   {
                      return false;
                   }

                   if(Yii::$app->user->identity->teamPlayer===NULL)
                   {
                     Yii::$app->session->setFlash('warning', 'You need to join a team before being able to access this area.');
                     return true;
                   }
                   if(Yii::$app->user->identity->teamPlayer->approved!==1)
                   {
                     Yii::$app->session->setFlash('warning', 'You need to have your team membership approved before being able to access this area.');
                     return true;
                   }
                   return false;
                 },
                 'denyCallback' => function() {
                   return  \Yii::$app->getResponse()->redirect(['/team/default/index']);
                 }
              ],
              [
                  'actions' => ['index', 'view', 'subscribe'],
                  'allow' => true,
                  'roles' => ['@'],
                  'matchCallback' => function ($rule, $action) {
                    return !Yii::$app->DisabledRoute->disabled($action);
                  },
              ],
          ],
      ]];
  }


    /**
     * Lists all Network models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider=new ActiveDataProvider([
            'query' => Network::find()->active()->orderBy(['name'=>SORT_ASC, 'id'=>SORT_ASC]),
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
