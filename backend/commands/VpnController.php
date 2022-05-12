<?php
/**
 * @author Pantelis Roditis <proditis@echothrust.com>
 * @copyright 2019
 * @since 0.1
 */

namespace app\commands;

use Yii;
use yii\console\Exception as ConsoleException;
use yii\helpers\Console;
use yii\console\Controller;
use app\modules\frontend\models\Player;
use app\components\OpenVPN;
/**
 * Manages VPN specific operations.
 *
 * @author proditis
 */
class VpnController extends Controller
{

  /**
   * Logout a specific player from the database (no OpenVPN sessions are touched)
   * @param string $player player: id or username.
   */
  public function actionLogout($player)
  {

    $pM=Player::find()->where(['username'=>$player])->orWhere(['id'=>$player])->one();
    if($pM===NULL)
    {
      throw new ConsoleException(Yii::t('app', 'Player not found with id or username of [{values}]', ['values' => $player]));
    }
    printf("Logging out %d\n",$pM->id);
    OpenVPN::logout($pM->id);
  }

  /**
   * Kill a specific player session from OpenVPN.
   * @param string $player player: id or username.
   */
  public function actionKill($player)
  {

    $pM=Player::find()->where(['username'=>$player])->orWhere(['id'=>$player])->one();
    if($pM===NULL)
    {
      throw new ConsoleException(Yii::t('app', 'Player not found with id or username of [{values}]', ['values' => $player]));
    }
    printf("Killing %d with last local IP [%s]\n",$pM->id,$pM->last->vpn_local_address_octet);
    OpenVPN::kill($pM->id,intval($pM->last->vpn_local_address));
  }

}
