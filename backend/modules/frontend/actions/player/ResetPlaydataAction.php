<?php
namespace app\modules\frontend\actions\player;

use Yii;

use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\PlayerSsl;
use app\modules\frontend\models\PlayerSearch;
use app\modules\settings\models\Sysconfig;

class ResetPlaydataAction extends \yii\base\Action
{
  public function run()
  {
    try
    {
      \Yii::$app->db->createCommand("CALL reset_playdata()")->execute();
      Yii::$app->session->setFlash('success', Yii::t('app','Successfully removed all player data'));
    }
    catch(\Exception $e)
    {
      Yii::$app->session->setFlash('error', Yii::t('app','Failed to remove player data'));
    }
    return $this->controller->redirect(['index']);

  }
}
