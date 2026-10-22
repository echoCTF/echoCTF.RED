<?php

namespace app\modules\activity\controllers;

use Yii;
use app\modules\activity\models\PlayerScore;
use app\modules\activity\models\PlayerScoreSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * PlayerScoreController implements the CRUD actions for PlayerScore model.
 */
class PlayerScoreController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[
        'access' => [
          'rules' => [
            'authActions' => [
              'actions' => ['top15'],
            ],
          ],
        ],
      ]);
    }

    /**
     * Lists all PlayerScore models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new PlayerScoreSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PlayerScore model.
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
     * Creates a new PlayerScore model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new PlayerScore();

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->player_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PlayerScore model.
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
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PlayerScore model.
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
     * Exports the top 15 players and the total number of players per academic grouping.
     * @return mixed
     */
    public function actionTop15()
    {
        $groupings=intval(Yii::$app->sys->academic_grouping);
        $csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
        for($i=0;$i<max($groupings,1);$i++)
        {
            $query=PlayerScore::find()->joinWith(['player'])->andWhere(['player.status' => 10]);
            if(Yii::$app->sys->player_require_approval === true)
                $query->andWhere(['player.approval' => [1,2]]);
            if($groupings>0)
                $query->andWhere(['player.academic' => $i]);
            $total=$query->count();
            fputcsv($csv, ['---------',$groupings>0 ? Yii::$app->sys->{"event_name_".$i} : Yii::$app->sys->event_name,'---------']);
            fputcsv($csv, ['Rank','Username','Full name','Points']);
            $rank=1;
            foreach($query->orderBy(['points'=>SORT_DESC, 'player_score.ts'=>SORT_ASC, 'player_score.player_id'=>SORT_ASC])->limit(15)->all() as $ps)
            {
                fputcsv($csv, [$rank++,$ps->player->username,$ps->player->fullname,$ps->points]);
            }
            fputcsv($csv, ['Total players',$total]);
        }
        rewind($csv);

        return \Yii::$app->response->sendStreamAsFile($csv,'top15-players.csv');
    }

    /**
     * Finds the PlayerScore model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PlayerScore the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(($model=PlayerScore::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
