<?php

namespace app\modules\target\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\data\ArrayDataProvider;

/**
 * Default controller for the `target` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex($id)
    {
        $target=$this->findModel($id);
        $treasures=$findings=[];
        foreach($target->treasures as $treasure)
          $treasures[]=$treasure->id;
        foreach($target->findings as $finding)
          $findings[]=$finding->id;
        $model=\app\models\Stream::find()->select('stream.*,TS_AGO(ts) as ts_ago')
        ->where(['model_id'=>$findings, 'model'=>'finding'])
        ->orWhere(['model_id'=>$treasures, 'model'=>'treasure'])->orderBy(['ts'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $model,
            'pagination' => [
                'pageSizeParam'=>'stream-perpage',
                'pageParam'=>'stream-page',
                'pageSize' => 10,
            ]
          ]);



        $headshotsProvider = new ArrayDataProvider([
            'allModels' => $target->headshots,
            'pagination' => [
                'pageSizeParam'=>'headshot-perpage',
                'pageParam'=>'headshot-page',
                'pageSize' => 10,
            ]]);

        return $this->render('index', [
            'target' => $target,
            'streamProvider'=>$dataProvider,
            'headshotsProvider'=>$headshotsProvider
        ]);
    }
    /**
     * Finds the Target model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Target the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = \app\modules\target\models\Target::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
