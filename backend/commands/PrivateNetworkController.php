<?php

namespace app\commands;

use Yii;
use app\components\ConsoleLockController as Controller;
use yii\console\widgets\Table;
use app\modules\infrastructure\models\PrivateNetworkTarget;
use app\modules\infrastructure\models\Server;
use app\modules\gameplay\models\NetworkTarget;
use app\modules\infrastructure\models\DockerContainer;
use app\components\Pf;
use yii\base\UserException;

/**
 * Private Network console controller for system checks.
 */
class PrivateNetworkController extends Controller
{

  public function actionIndex()
  {
    $this->stdout("*** Private Network COMMAND ***\n");

    echo Table::widget([
      'headers' => ['Action', 'Usage', 'Description'],
      'rows' => [
        ['Action' => 'private-network/target-ops',   'Usage' => 'private-network/target-ops', 'Description' => 'Perform private network related target operations (starts/restart/destroy)'],
      ],
    ]);
  }

  /**
   * Perform private network target operations (start/restart/stop)
   */
  public function actionTargetOps()
  {
    $pnt = PrivateNetworkTarget::find()->pending_action()->all();

    foreach ($pnt as $p) {
      try {
        $ips = [];
        $dc = new DockerContainer($p->target);
        $server = $p->server;
        if (intval($p->server_id) === 0 || $p->ipoctet === null) {
          $notifType = "success";
          $notifTitle = $notifMessage = sprintf("Target %s from your private network got started!", $p->target->name);
          // pick a server
          $server = Server::findNextFreeOne();
          $p->state = 0;
          $p->server_id = $server->id;
        } else if (intval($p->state) === 1) {
          $notifType = "success";
          $notifTitle = $notifMessage = sprintf("Target %s from your private network got rebooted!", $p->target->name);          // restart a target on a given server
          $p->state = 0;
        }
        echo date("Y-m-d H:i:s ") . sprintf("PrivateNetworkOps: Processing target %s for private network %s (id: %s) for player %s on server %s", $p->target->name, $p->privateNetwork->name, $p->private_network_id, $p->privateNetwork->player->username, $server->name);

        $dc->timeout = ($server->timeout ? $server->timeout : 5000);
        if ($p->target->targetVolumes !== null)
          $dc->targetVolumes = $p->target->targetVolumes;

        if ($p->target->targetVariables !== null) {
          $dc->targetVariables = $p->target->targetVariables;
        }
        if ($p->target->dynamic_treasures) {
          // Fetch the encrypted env flag
          $encryptedTreasures = $p->encryptedTreasures;

          // Check existing environment variables for ETSCTF_FLAG keys
          foreach ($dc->targetVariables as $key => $tv) {
            // Replace the old key with the encrypted treasure
            if ($tv->key == 'ETSCTF_FLAG' && array_key_exists('env', $encryptedTreasures['fs'])) {
              $tv->val = str_replace($encryptedTreasures['fs']['env'][0]['src'], $encryptedTreasures['fs']['env'][0]['dest'], $tv->val);
              break;
            }
          }
          $dc->labels['dynamic_treasures'] = "1";
          $dc->labels['player_id'] = (string)$p->privateNetwork->player_id;
          $dc->labels['target_id'] = (string)$p->target_id;
          foreach (str_split(base64_encode(json_encode($encryptedTreasures)), 1024) as $key => $part)
            $dc->labels['treasures_' . $key] = $part;
        }

        $dc->name = sprintf("%s_%s", $p->privateNetwork->name, $p->target->name);
        $dc->server = $server->connstr;
        $dc->net = $server->network;
        try {
          $dc->destroy();
        } catch (\Exception $e) {
        }

        if ($p->state < 2) {
          $dc->pull();
          $dc->spin();
          if ($p->privateNetwork->team_accessible === true && $p->privateNetwork->player->teamPlayer && $p->privateNetwork->player->teamPlayer->approved === 1) {
            foreach ($p->privateNetwork->player->teamPlayer->team->approvedMembers as $teamPlayer) {
              if ((int)$teamPlayer->player->last->vpn_local_address !== 0) {
                $ips[] = long2ip($teamPlayer->player->last->vpn_local_address);
              }
            }
          } else if ((int)$p->privateNetwork->player->last->vpn_local_address !== 0) {
            $ips[] = long2ip($p->privateNetwork->player->last->vpn_local_address);
          }

          if ($ips != [])
            Pf::add_table_ip($p->privateNetwork->name . '_clients', implode(' ', $ips), true);

          if ($p->ipoctet != '0.0.0.0' || $p->ipoctet == null) {
            // remove old IP in case it changed
            Pf::del_table_ip($p->privateNetwork->name, $p->ipoctet);
          }
          $p->ipoctet = $dc->container->getNetworkSettings()->getNetworks()->{$server->network}->getIPAddress();
          Pf::add_table_ip($p->privateNetwork->name, $p->ipoctet, false);
          echo " DONE\n";
        } else { // state==2 destroy
          echo " DESTROYED\n";
          $notifType = "warning";
          $notifTitle = $notifMessage = sprintf("Target %s from your private network got shut down!", $p->target->name);          // restart a target on a given server
          Pf::kill_table($p->privateNetwork->name, true);
          Pf::kill_table($p->privateNetwork->name . '_clients', true);
          $p->server_id = null;
        }

        if (!$p->save() || !$p->privateNetwork->player->notify($notifType, $notifTitle, $notifMessage)) {
          Yii::error(imploded(",", $p->getErrorSummary(true)));
          throw new UserException(imploded(",", $p->getErrorSummary(true)));
        }
      } catch (\Exception $e) {
        echo " FAILED\n";
        echo $e->getMessage(), "\n";
        continue;
      } // endcatch
    }
  }
}
