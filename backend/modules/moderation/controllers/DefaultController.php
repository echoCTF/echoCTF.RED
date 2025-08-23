<?php

namespace app\modules\moderation\controllers;

use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\PlayerSsl;
use app\modules\frontend\models\PlayerSearch;
use app\modules\settings\models\Sysconfig;
use app\modules\activity\models\StreamSearch;
use app\modules\activity\models\PlayerLastSearch;
use yii\base\UserException;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class DefaultController extends \app\components\BaseController
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

    /**
     * Lists stream entries with estimated lag from their previous entry.
     * @return mixed
     */
    public function actionDuplicateSignupIps()
    {
        $searchModel=new PlayerLastSearch();
        $dataProvider=$searchModel->searchDuplicateSignupIps(\Yii::$app->request->queryParams);

        return $this->render('duplicate-signup-ips', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
  /**
   * Check mails for known spam and disposable hosters.
   */
  public function actionCheckSpammy()
  {
    $skip_domains = $DNS_NS = $DNS_MX = $DNS_A = $spammy=[];
    $players = (new \yii\db\Query())
    ->select(["SUBSTRING_INDEX(email,'@',-1) as email","count(*) as players"])
    ->from('player')
    ->where(['status'=>10])
    ->groupBy(new \yii\db\Expression("SUBSTRING_INDEX(email,'@',-1)"));

    foreach ($skip_domains as $d)
      $players->andWhere(['not like', 'email', $d]);

    $validator = new \app\components\validators\MXServersValidator();
    $validator->mxonly = true;
    foreach ($players->all() as $p) {
        try {
            if (!$validator->validate($p['email'], $error)) {
                throw new UserException($error);
            }
        } catch (\Exception $e) {
            $spammy[$p['email']][]=$e->getMessage();
            $spammy[$p['email']]['players']=$p['players'];
        }
    }

    return $this->render('spammy-domains', [
        'spammy' => $spammy,
    ]);

  }
}
