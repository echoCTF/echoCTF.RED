<?php

use yii\db\Migration;

/**
 * Class m241103_021658_target_api_url_routes
 */
class m241103_021658_target_api_url_routes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->upsert('url_route',['source'=>'profile/generate-token','destination'=>'profile/generate-token','weight'=>339]);
      $this->upsert('url_route',['source'=>'api/targets','destination'=>'api/target/index','weight'=>642]);
      $this->upsert('url_route',['source'=>'api/target/claim','destination'=>'api/target/claim','weight'=>643]);
      $this->upsert('url_route',['source'=>'api/target/instances','destination'=>'api/target/instances','weight'=>643]);
      $this->upsert('url_route',['source'=>'api/target/<id:\d+>','destination'=>'api/target/view','weight'=>644]);
      $this->upsert('url_route',['source'=>'api/target/<id:\d+>/spin','destination'=>'api/target/spin','weight'=>645]);
      $this->upsert('url_route',['source'=>'api/target/<id:\d+>/spawn','destination'=>'api/target/spawn','weight'=>646]);
      $this->upsert('url_route',['source'=>'api/target/<id:\d+>/shut','destination'=>'api/target/shut','weight'=>647]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241103_021658_target_api_url_routes cannot be reverted.\n";
    }

}
