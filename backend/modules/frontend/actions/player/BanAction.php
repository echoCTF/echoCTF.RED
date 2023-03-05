<?php
namespace app\modules\frontend\actions\player;

use Yii;

use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\PlayerSsl;
use app\modules\frontend\models\PlayerSearch;
use app\modules\settings\models\Sysconfig;

class BanAction extends \yii\base\Action
{
  /**
   * Ban an existing Player model
   * If ban is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function run($id)
  {
    $trans=Yii::$app->db->beginTransaction();
    try
    {
      if($this->controller->findModel($id)->ban())
      {
        $trans->commit();
        Yii::$app->session->setFlash('success', Yii::t('app','Player deleted and placed on banned table.'));
      }
      else
      {
          throw new \LogicException(Yii::t('app','Failed to delete and ban player.'));
      }
    }
    catch(\Exception $e)
    {
      $trans->rollBack();
      Yii::$app->session->setFlash('error', Yii::t('app','Failed to ban player.'));
    }
    if(Yii::$app->request->referrer)
    {
      return Yii::$app->response->redirect(Yii::$app->request->referrer);
    }
    else
    {
      return Yii::$app->response->redirect(['index']);
    }
  }

}
