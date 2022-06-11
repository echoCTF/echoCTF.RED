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
          <td><?=intval(Yii::$app->cache->memcache->get('failed_login_username:'.$model->owner->username))?> <?= Html::a('<i class="fas fa-eraser"></i>', ['reset-key', 'id' => $model->id], [
                'class' => 'text-danger',
                'title'=>'Delete key',
                'data-toggle'=>'tooltip',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this key?'),
                    'params'=>['key'=>'failed_login_username:'.$model->owner->username],
                    'method' => 'post',
                ],
            ]) ?>
          </td>
        </tr>
        <tr>
          <td><i class="fas fa-eye"></i></td>
          <td>Last seen</td>
          <td><?=Html::encode(\Yii::$app->formatter->asDatetime(Yii::$app->cache->memcache->get('last_seen:'.$model->player_id)))?> <?= Html::a('<i class="fas fa-eraser"></i>', ['reset-key', 'id' => $model->id], [
                'class' => 'text-danger',
                'title'=>'Delete key',
                'data-toggle'=>'tooltip',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this key?'),
                    'params'=>['key'=>'last_seen:'.$model->player_id],
                    'method' => 'post',
                ],
            ]) ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-plug"></i></td>
          <td>Online</td>
          <td><?=Html::encode(\Yii::$app->formatter->asDatetime(Yii::$app->cache->memcache->get('online:'.$model->player_id)))?> <?= Html::a('<i class="fas fa-eraser"></i>', ['reset-key', 'id' => $model->id], [
                'class' => 'text-danger',
                'title'=>'Delete key',
                'data-toggle'=>'tooltip',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this key?'),
                    'params'=>['key'=>'online:'.$model->player_id],
                    'method' => 'post',
                ],
            ]) ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-id-card-alt"></i></td>
          <td>Frontend IP</td>
          <td><?=Html::encode(Yii::$app->cache->memcache->get('player_frontend_ip:'.$model->player_id))?> <?= Html::a('<i class="fas fa-eraser"></i>', ['reset-key', 'id' => $model->id], [
                'class' => 'text-danger',
                'title'=>'Delete key',
                'data-toggle'=>'tooltip',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this key?'),
                    'params'=>['key'=>'player_frontend_ip:'.$model->player_id],
                    'method' => 'post',
                ],
            ]) ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-id-card-alt"></i></td>
          <td>Session</td>
          <td><?=Html::encode(Yii::$app->cache->memcache->get('player_session:'.$model->player_id))?> <?= Html::a('<i class="fas fa-eraser"></i>', ['reset-key', 'id' => $model->id], [
                'class' => 'text-danger',
                'title'=>'Delete key',
                'data-toggle'=>'tooltip',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this key?'),
                    'params'=>['key'=>'player_session:'.$model->player_id],
                    'method' => 'post',
                ],
            ]) ?></td>
        </tr>
        <tr>
          <td><i class="fas fa-user-shield"></i></td>
          <td>Ovpn</td>
          <td><?=Html::encode(Yii::$app->cache->memcache->get('ovpn:'.$model->player_id))?> <?= Html::a('<i class="fas fa-eraser"></i>', ['reset-key', 'id' => $model->id], [
                'class' => 'text-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this key?'),
                    'params'=>['key'=>'ovpn:'.$model->player_id],
                    'method' => 'post',
                ],
            ]) ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
