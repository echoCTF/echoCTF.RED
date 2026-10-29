<?php

namespace app\modules\infrastructure\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\Expression;
use Docker\Docker;
use Docker\DockerClientFactory;
use Docker\API\Model\RestartPolicy;
use Docker\API\Model\HostConfig;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\EndpointSettings;
use Docker\API\Model\EndpointIPAMConfig;

/**
 * This is the model class for table "target_instance".
 *
 */
class TargetInstance extends TargetInstanceAR
{
  public $ipoctet;
  const ACTION_START = 0;
  const ACTION_RESTART = 1;
  const ACTION_DESTROY = 2;
  const ACTION_EXPIRED = 3;



  /**
   * Gets name tha the target will have
   */
  public function getName()
  {
    return sprintf('%s_%d', strtolower($this->target->name), $this->player_id);
  }

  /**
   * Maximum number of concurrent instances allowed for a player, from
   * `player_spin.max_instances`.
   * Keep in sync with the frontend `Player::getMaxTargetInstances()`.
   *
   * @param Player $player
   * @return int
   */
  public static function maxInstancesFor(Player $player): int
  {
    return $player->playerSpin !== null ? max(1, intval($player->playerSpin->max_instances)) : 1;
  }

  /**
   * Schedule for destruction the instances of players that have more instances
   * than they are allowed to (eg. their subscription expired or the limit was
   * lowered). The oldest instances are kept, the most recent ones get destroyed.
   *
   * @return TargetInstance[] the instances that got scheduled for destruction
   */
  public static function enforceLimits(): array
  {
    $scheduled = [];
    $playerIds = Yii::$app->db->createCommand('SELECT player_id FROM target_instance WHERE reboot<2 GROUP BY player_id HAVING COUNT(*)>1')->queryColumn();
    foreach ($playerIds as $player_id) {
      $player = Player::findOne($player_id);
      if ($player === null) {
        continue;
      }
      $max = self::maxInstancesFor($player);
      $instances = self::find()
        ->where(['player_id' => $player_id])
        ->andWhere(['<', 'reboot', 2])
        ->orderBy(['created_at' => SORT_ASC, 'target_id' => SORT_ASC])
        ->all();
      foreach (array_slice($instances, $max) as $instance) {
        $instance->reboot = 2;
        if ($instance->save(false)) {
          $scheduled[] = $instance;
        }
      }
    }
    return $scheduled;
  }

  public function getRebootVal()
  {
    if ($this->reboot === 0 && $this->ip === null) {
      return 'Start';
    } elseif ($this->reboot === 0) {
      return 'Do nothing';
    } elseif ($this->reboot === 1) {
      return 'Restart';
    } elseif ($this->reboot === 2) {
      return 'Destroy';
    }
  }

  public function connectAPI($params = null)
  {
    if ($params === null) {
      if ($this->server !== null) {
        $params['remote_socket'] = $this->server->connstr;
        $params['ssl'] = (bool) $this->server->ssl;
        $params['timeout'] = $this->server->timeout ? (int) $this->server->timeout : 5000;
      }
    }
    try {
      $client = DockerClientFactory::create($params);
      return Docker::create($client);
    } catch (\Throwable $e) {
      return false;
    }
  }

  public function restart()
  {
    $dc = new DockerContainer($this->target);
    $dc->targetVolumes = $this->target->targetVolumes;
    $dc->targetVariables = $this->target->targetVariables;
    $dc->name = $this->name;
    $dc->server = $this->server->connstr;
    $dc->ssl = (bool) $this->server->ssl;
    $dc->net = $this->server->network;
    // Check If target supports dynamic_treasures
    if ($this->target->dynamic_treasures) {
      // Fetch the encrypted env flag
      $encryptedTreasures = $this->encryptedTreasures;

      // Check existing environment variables for ETSCTF_FLAG keys
      foreach ($dc->targetVariables as $key => $tv) {
        // Replace the old key with the encrypted treasure
        if ($tv->key == 'ETSCTF_FLAG') {
          $tv->val = str_replace($encryptedTreasures['fs']['env'][0]['src'], $encryptedTreasures['fs']['env'][0]['dest'], $tv->val);
          break;
        }
      }
      $dc->labels['dynamic_treasures'] = '1';
      $dc->labels['player_id'] = (string)$this->player_id;
      $dc->labels['target_id'] = (string)$this->target_id;
      foreach (str_split(base64_encode(json_encode($encryptedTreasures)), 1024) as $key => $part) {
        $dc->labels['treasures_' . $key] = $part;
      }
    }

    // pull before removing the running container, a failed pull keeps it up
    $dc->pull();
    try {
      $dc->destroy();
    } catch (\Throwable $e) {
    }
    $dc->spin();
    $this->ipoctet = $dc->container->getNetworkSettings()->getNetworks()->{$this->server->network}->getIPAddress();
    $this->reboot = 0;
  }

  public function getEncryptedTreasures()
  {
    $query = \app\modules\gameplay\models\Treasure::find()
      ->select([
        'id',
        'code',
        new \yii\db\Expression(
            'MD5(HEX(AES_ENCRYPT(CONCAT(code, :playerId), :secretKey))) AS encrypted_code',
            [':playerId' => $this->player_id, ':secretKey' => Yii::$app->sys->treasure_secret_key]
        ),
        'target_id',
        'location',
        'category',
      ])
      ->where(['target_id' => $this->target_id]);
    $treasures = [];
    foreach ($query->all() as $t) {
      if ($t->category == 'env' && ($t->location == 'environment' || $t->location == '')) {
        $treasures['env'][] = ['src' => $t->code, 'dest' => $t->encrypted_code];
      } elseif (str_contains($t->location, $t->code)) {
        $treasures['mv'][] = ['src' => $t->location, 'dest' => str_replace($t->code, $t->encrypted_code, $t->location)];
      } else {
        $treasures['sed'][] = ['src' => $t->code, 'dest' => $t->encrypted_code, 'file' => $t->location];
      }
    }

    return ['fs' => $treasures];
  }

  /**
   * Get the list of findings for the given instance, excluding findings that the player already found
   */
  public function getFindings()
  {
    $subQuery = \app\modules\activity\models\PlayerFinding::find()
      ->alias('pf')
      ->select('pf.finding_id')
      ->innerJoin(['f' => \app\modules\gameplay\models\Finding::tableName()], 'f.id = pf.finding_id')
      ->where([
        'pf.player_id' => $this->player_id,
        'f.target_id'  => $this->target_id,
      ]);

    return $this->hasMany(\app\modules\gameplay\models\Finding::class, ['target_id' => 'target_id'])
      ->andWhere(['not in', \app\modules\gameplay\models\Finding::tableName() . '.id', $subQuery]);
  }
}
