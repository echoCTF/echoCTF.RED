<?php

namespace app\modules\moderation\controllers;

use Yii;
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
use yii\helpers\Html;
use yii\data\ActiveDataProvider;

class DefaultController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), []);
  }

  /**
   * Lists all Players with zero points and activated writeups.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new PlayerSearch();
    $dataProvider = $searchModel->zeroPointWiteupsActivatedSearch(\Yii::$app->request->queryParams);

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
    $searchModel = new StreamSearch();
    $dataProvider = $searchModel->searchWithLag(\Yii::$app->request->queryParams);

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
    $searchModel = new PlayerLastSearch();
    $dataProvider = $searchModel->searchDuplicateSignupIps(\Yii::$app->request->queryParams);

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
    $skip_domains = $DNS_NS = $DNS_MX = $DNS_A = $spammy = [];
    $players = (new \yii\db\Query())
      ->select(["SUBSTRING_INDEX(email,'@',-1) as email", "count(*) as players"])
      ->from('player')
      ->where(['status' => 10])
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
        $spammy[$p['email']][] = $e->getMessage();
        $spammy[$p['email']]['players'] = $p['players'];
      }
    }

    return $this->render('spammy-domains', [
      'spammy' => $spammy,
    ]);
  }

  /**
   * Deletes all players from the given domain
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param int $id ID
   * @return \yii\web\Response
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionNotifySpammy($domain)
  {
    $trans = \Yii::$app->db->beginTransaction();
    try {
      $query=Player::find()->where([
        'AND',
        ['status' => 10],
        ['=',new \yii\db\Expression("SUBSTRING_INDEX(email,'@',-1)"),$domain]
      ]);
      foreach($query->all() as $player){
        $player->notify('swal:error','Email validation error!!!','Your email address seems to be failing our validation criteria. Please change email address if you want to keep your account.');
      }
      $trans->commit();
      \Yii::$app->getSession()->setFlash('success', Yii::t('app', '{records,plural,=0{No players found} =1{Notified one player} other{Notified # players}}', ['records' => $query->count()]));
    } catch (\Exception $e) {
      $trans->rollBack();
      \Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Failed to notify players. {exception}', ['exception' => Html::encode($e->getMessage())]));
    }
    return $this->goBack((
      !empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null
    ));
  }

}
