<?php

use yii\db\Migration;

class m261017_162254_create_default_csp_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $csp="default-src 'self'; script-src 'self' 'unsafe-inline' https://*.googletagmanager.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data: https://*.google-analytics.com https://*.googletagmanager.com; font-src 'self' https://fonts.gstatic.com; connect-src 'self' wss://{host} https://*.google-analytics.com https://*.analytics.google.com https://*.googletagmanager.com; frame-ancestors 'none'; base-uri 'self'; form-action 'self';";
      $this->upsert("sysconfig",['id'=>'offense_csp','val'=>$csp]);
      $this->upsert("sysconfig",['id'=>'moderator_csp','val'=>$csp]);
      $this->upsert("sysconfig",['id'=>'offense_csp_header','val'=>'Content-Security-Policy-Report-Only']);
      $this->upsert("sysconfig",['id'=>'moderator_csp_header','val'=>'Content-Security-Policy-Report-Only']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete("sysconfig",['id'=>'offense_csp']);
      $this->delete("sysconfig",['id'=>'moderator_csp']);
      $this->delete("sysconfig",['id'=>'offense_csp_header']);
      $this->delete("sysconfig",['id'=>'moderator_csp_header']);
    }
}
