<?php

namespace app\modules\frontend\controllers;

use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\PlayerSsl;
use app\modules\frontend\models\PlayerSearch;
use app\modules\settings\models\Sysconfig;
use app\modules\activity\models\StreamSearch;
use yii\helpers\ArrayHelper;

class ModerationController extends \app\components\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all Players with zero points and activated writeups.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new PlayerSearch();
        $dataProvider=$searchModel->zeroPointWiteupsActivatedSearch(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists stream entries with estimated lag from their previous entry.
     * @return mixed
     */
    public function actionStreamLag()
    {
        $searchModel=new StreamSearch();
        $dataProvider=$searchModel->searchWithLag(\Yii::$app->request->queryParams);

        return $this->render('stream-lag', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


}
