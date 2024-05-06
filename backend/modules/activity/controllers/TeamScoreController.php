<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\TeamScore;
use app\modules\activity\models\TeamScoreSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * TeamScoreController implements the CRUD actions for TeamScore model.
 */
class TeamScoreController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'rules' => [
                'class' => 'yii\filters\AjaxFilter',
                'only' => ['ajax-search']
            ],
            'access' => [
              'class' => \yii\filters\AccessControl::class,
              'rules' => [
                'adminActions'=>[
                      'allow' => true,
                      'roles' => ['@'],
                ],
                'authActions'=>[
                    'allow' => true,
                    'actions'=>['index','view','top15','top15-inclusive'],
                    'roles' => ['@'],
                ],
                'denyAll'=>[
                    'allow' => false,
                ],
              ],
            ],
            'verbs' => [
              'class' => VerbFilter::class,
              'actions' => [
                'delete' => ['POST'],
              ],
            ],
          ];
    }

    /**
     * Lists all TeamScore models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new TeamScoreSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TeamScore model.
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
     * Creates a new TeamScore model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new TeamScore();

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->team_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TeamScore model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model=$this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->team_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TeamScore model.
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
     * Lists all TeamScore models.
     * @return mixed
     */
    public function actionTop15Inclusive()
    {
        $searchModel=new TeamScoreSearch();
        $csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
        fputcsv($csv, ['Rank','Team','Full name','Username','Email','category','affiliation']);
        for($academic=0;$academic<3;$academic++)
        {
            $query=TeamScore::find()->joinWith(['team'])->andFilterWhere(['team.academic' => $academic])
                ->orderBy(['points'=>SORT_DESC, 'ts'=>SORT_ASC, 'team_id'=>SORT_ASC])
                ->limit(15);
            $rank=1;
            foreach($query->all() as $ts)
            {
                foreach($ts->team->teamPlayers as $tp)
                {
                    if($tp->approved==0)
                        continue;
                    if($tp->player->metadata)
                      fputcsv($csv, [$rank,$ts->team->name,$tp->player->fullname, $tp->player->username,$tp->player->email,Yii::$app->sys->{"academic_".$tp->player->academic.'short'},$tp->player->metadata->affiliation]);
                    else
                      fputcsv($csv, [$rank,$ts->team->name,$tp->player->fullname, $tp->player->username,$tp->player->email,Yii::$app->sys->{"academic_".$tp->player->academic.'short'},'']);
                }
                $rank++;
            }
        }
        rewind($csv);
        return \Yii::$app->response->sendStreamAsFile($csv,'top15-inclusive.csv');
    }

    /**
     * Lists all TeamScore models.
     * @return mixed
     */
    public function actionTop15()
    {
        $searchModel=new TeamScoreSearch();
        $params=Yii::$app->request->queryParams;
        $csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
        for($i=0;$i<3;$i++)
        {
            $params['TeamScoreSearch']['team_academic']=$i;
            $dataProvider=$searchModel->search($params);
            $dataProvider->pagination->pageSize=15;
            fputcsv($csv, ['---------',Yii::$app->sys->{"event_name_".$i},'---------']);
            fputcsv($csv, ['Rank','Team','Points']);
            foreach($dataProvider->getModels() as $k=>$line)
            {
                fputcsv($csv, [$k+1,$line->team->name,$line->points]);
            }
        }
        rewind($csv);

        return \Yii::$app->response->sendStreamAsFile($csv,'top15.csv');
    }

    /**
     * Finds the TeamScore model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TeamScore the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(($model=TeamScore::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
