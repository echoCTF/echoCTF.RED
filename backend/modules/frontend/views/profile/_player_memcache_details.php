<?php
use yii\helpers\Html;

?>
<div class="panel">
  <div class="panel-heading">
    <span class="panel-icon">
      <i class="fas fa-memory"></i>
    </span>
    <span class="panel-title"> Memcache</span>
  </div>
  <div class="panel-body pn">
    <table class="table mbn tc-icon-1 tc-med-2 tc-bold-last">
      <thead>
        <tr class="hidden">
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><i class="fas fa-sign-in-alt"></i></td>
          <td>Failed logins</td>
          <td><?=intval(Yii::$app->cache->memcache->get('failed_login_usename:'.$model->owner->username))?></td>
        </tr>
        <tr>
          <td><i class="fas fa-eye"></i></td>
          <td>Last seen</td>
          <td><?=Html::encode(Yii::$app->cache->memcache->get('last_seen:'.$model->player_id))?></td>
        </tr>
        <tr>
          <td><i class="fas fa-plug"></i></td>
          <td>Online</td>
          <td><?=Html::encode(Yii::$app->cache->memcache->get('online:'.$model->player_id))?></td>
        </tr>
        <tr>
          <td><i class="fas fa-id-card-alt"></i></td>
          <td>Session</td>
          <td><?=Html::encode(Yii::$app->cache->memcache->get('player_session:'.$model->player_id))?></td>
        </tr>
        <tr>
          <td><i class="fas fa-user-shield"></i></td>
          <td>Ovpn</td>
          <td><?=Html::encode(long2ip(Yii::$app->cache->memcache->get('ovpn:'.$model->player_id)))?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
