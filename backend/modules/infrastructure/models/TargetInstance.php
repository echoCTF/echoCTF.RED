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
    return sprintf("%s_%d", strtolower($this->target->name), $this->player_id);
  }

  public function afterFind()
  {
    parent::afterFind();
    if ($this->ip)
      $this->ipoctet = long2ip($this->ip);
  }

  public function beforeSave($insert)
  {
    if (parent::beforeSave($insert)) {
      if ($this->ipoctet)
        $this->ip = ip2long($this->ipoctet);
      else $this->ip = null;
      return true;
    } else {
      return false;
    }
  }

  public function afterSave($insert, $changedAttributes)
  {
    parent::afterSave($insert, $changedAttributes);
    if ($insert) {
      return;
    }
    if ($this->ip !== null && array_key_exists('ip', $changedAttributes) && $changedAttributes['ip'] === null) {
      $this->notify('started');
    } elseif ($this->reboot === 0 && @$changedAttributes['reboot'] === 1) {
      $this->notify('restarted');
    }
  }

  /**
   * Send notif after model deletion
   */
  public function afterDelete()
  {
    $this->notify('destroyed');
    parent::afterDelete();
  }

  public function notify($what)
  {
    $n = new \app\modules\activity\models\Notification;
    $n->player_id = $this->player_id;
    $n->title = Yii::t('app', "Your instance for target [{target_name}] got {what}.", ['target_name' => $this->target->name, 'what' => $what]);
    $n->body = $n->title;
    $n->archived = 0;
    $n->created_at = new \yii\db\Expression('NOW()');
    $n->updated_at = new \yii\db\Expression('NOW()');
    $n->save();
  }

  public function getRebootVal()
  {
    if ($this->reboot === 0 && $this->ip === null) {
      return "Start";
    } elseif ($this->reboot === 0) {
      return "Do nothing";
    } elseif ($this->reboot === 1) {
      return "Restart";
    } elseif ($this->reboot === 2) {
      return "Destroy";
    }
  }

  public function connectAPI($params = null)
  {
    if ($params === null) {
      if ($this->server !== null) {
        $params['remote_socket'] = $this->server->connstr;
        $params['ssl'] = false;
        $params['timeout'] = 5000;
      }
    }
    try {
      $client = DockerClientFactory::create($params);
      return Docker::create($client);
    } catch (\Exception $e) {
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
      $dc->labels['dynamic_flags'] = 1;
      $dc->labels['player_id'] = $this->player_id;
      $dc->labels['target_id'] = $this->target_id;
      foreach (str_split(base64_encode(json_encode($encryptedTreasures)), 1024) as $key => $part)
        $dc->labels['treasures_' . $key] = $part;
    }

    try {
      $dc->destroy();
    } catch (\Exception $e) {
    }
    $dc->pull();
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
          "MD5(HEX(AES_ENCRYPT(CONCAT(code, :playerId), :secretKey))) AS encrypted_code",
          [':playerId' => $this->player_id, ':secretKey' => Yii::$app->sys->treasure_secret_key]
        ),
        'target_id',
        'location',
        'category',
      ])
      ->where(['target_id' => $this->target_id]);
    $treasures = [];
    foreach ($query->all() as $t) {
      if ($t->category == 'env' && $t->location == 'environment') {
        $treasures['env'][] = ["src" => $t->code, 'dest' => $t->encrypted_code];
      } else if (str_contains($t->location, $t->code)) {
        $treasures['mv'][] = ["src" => $t->location, 'dest' => str_replace($t->code, $t->encrypted_code, $t->location)];
      } else {
        $treasures['sed'][] = ["src" => $t->code, 'dest' => $t->encrypted_code, 'file' => $t->location];
      }
    }

    return ['fs' => $treasures];
  }
}
