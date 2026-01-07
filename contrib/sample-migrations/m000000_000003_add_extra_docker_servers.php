<?php

use yii\db\Migration;

/**
 * Class m000000_000003_add_extra_docker_servers
 */
class m000000_000003_add_extra_docker_servers extends Migration
{
  public $minsrv = 1;
  public $maxsrv = 4;
  public $server =  [
    'network' => 'AAnet',
    'service' => 'docker',
    'provider_id' => 'vultr'
  ];
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    for ($i = $this->minsrv; $i <= $this->maxsrv; $i++) {
      $this->server['name'] = sprintf("docker%02d", $i);
      $this->server['ip'] = ip2long('10.0.0.' . $i);
      $this->server['connstr'] = sprintf("tcp://10.0.0.%d:2376", $i);
      $this->upsert('server', $this->server);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    for ($i = $this->minsrv; $i <= $this->maxsrv; $i++) {
      $this->server['name'] = sprintf("docker%02d", $i);
      $this->server['ip'] = ip2long('10.0.0.' . $i);
      $this->server['connstr'] = sprintf("tcp://10.0.0.%d:2376", $i);
      $this->delete('server', $this->server);
    }
  }
}
