<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\modules\frontend\models\Player */

$this->title = sprintf("View player [ID:%d] %s details", $model->id, $model->username);
$this->params['breadcrumbs'][] = ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][] = ['label' => 'Players', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="player-view">

  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Generate SSL', ['generate-ssl', 'id' => $model->id], [
      'class' => 'btn btn-warning',
      'data' => [
        'confirm' => 'Are you sure you want to generate new SSL for this player?',
        'method' => 'post',
      ],
    ]) ?>

    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
      'class' => 'btn btn-danger',
      'data' => [
        'confirm' => 'Are you sure you want to delete this item?',
        'method' => 'post',
      ],
    ]) ?>
  </p>

  <div class="row">
    <div class="col-lg-6">
      <h3>Player</h3>
      <?= DetailView::widget([
        'options' => ['class' => 'table table-striped table-bordered detail-view'],
        'model' => $model,
        'attributes' => [
          'id',
          'username:linkProfile',
          'fullname',
          [
            'attribute' => 'stripe_customer_id',
            'format' => 'raw',
            'value' => function ($model) {
              if ($model->stripe_customer_id)
                return Html::a($model->stripe_customer_id, "https://dashboard.stripe.com/customers/" . $model->stripe_customer_id, ['target' => '_blank']);
              return "";
            }
          ],
          'email:email',
          'academicLong',
          'metadata.affiliation',
          'active:boolean',
          'status',
          'type',
          [
            'attribute' => 'activkey',
            'format' => 'raw',
            'value' => function ($model) {
              if (!empty($model->activkey)) return Html::a($model->activkey, '//' . Yii::$app->sys->offense_domain . '/verify-email?token=' . $model->activkey, ['target' => '_blank']);
            }
          ],
          'auth_key',
          [
            'attribute' => 'verification_token',
            'format' => 'raw',
            'value' => function ($model) {
              if (!empty($model->verification_token)) return Html::a($model->verification_token, '//' . Yii::$app->sys->offense_domain . '/verify-email?token=' . $model->verification_token, ['target' => '_blank']);
            }
          ],
          [
            'attribute' => 'password_reset_token',
            'format' => 'raw',
            'value' => function ($model) {
              if (!empty($model->password_reset_token)) return Html::a($model->password_reset_token, '//' . Yii::$app->sys->offense_domain . '/reset-password?token=' . $model->password_reset_token, ['target' => '_blank']);
            }
          ],
          'created',
          'ts',
        ],
      ]) ?>
    </div>
    <!--
-->
    <div class="col-lg-6">
      <h3>Profile</h3>
      <?= DetailView::widget([
        'model' => $model->profile,
        'attributes' => [
          'id',
          'bio:ntext',
          'country',
          'avatar',
          'visibility',
          'twitter',
          'github',
          'discord',
          'terms_and_conditions:boolean',
          'mail_optin:boolean',
          'gdpr:boolean',
          'created_at',
          'updated_at',
        ],
      ]) ?>
    </div>
  </div>
  <details>
    <summary>Extras (spins/vpn etc)</summary>
    <?= DetailView::widget([
      'model' => $model,
      'attributes' => [
        'online:boolean',
        [
          'label' => 'Headshots',
          'value' => function ($model) {
            return count($model->headshots);
          }
        ],
        [
          'attribute' => 'on_pui',
          'label' => 'Last seen on pUI',
          'value' => function ($model) {
            if ($model->last) return $model->last->on_pui == 0 ? null : $model->last->on_pui;
            else return null;
          }
        ],
        [
          'attribute' => 'on_vpn',
          'label' => 'Last seen on VPN',
          'value' => function ($model) {
            if ($model->last) return $model->last->on_vpn == 0 ? null : $model->last->on_vpn;
            else return null;
          }
        ],
        [
          'attribute' => 'vpn_local_address',
          'label' => 'VPN Local IP',
          'value' => function ($model) {
            return $model->last && $model->last->vpn_local_address ? long2ip($model->last->vpn_local_address) : null;
          }
        ],
        [
          'attribute' => 'signup_ip',
          'value' => function ($model) {
            return $model->last->signup_ip === NULL ? null : long2ip($model->last->signup_ip);
          },
        ],
        [
          'attribute' => 'signin_ip',
          'value' => function ($model) {
            return $model->last->signin_ip === NULL ? null : long2ip($model->last->signin_ip);
          },
        ],

        'playerSpin.counter',
        'playerSpin.total',
        'playerSpin.total',
      ],
    ]) ?>

  </details>
  <details>
    <summary>Sessions</summary>
    <?= GridView::widget([
      //'caption'=>'some',
      'emptyText' => 'No sessions for the user',
      'summary' => '<h2>Player Sessions: <small>{totalCount}</small></h2>',
      'layout' => "{summary}\n{items}\n{pager}",
      'dataProvider' => new yii\data\ArrayDataProvider(
        [
          'allModels' =>  $model->sessions,
          'sort' => [
            'attributes' => ['id', 'ipoctet', 'expire', 'ts'],
          ],
          'pagination' => [
            'pageSize' => 10,
          ],
        ]
      ),
      'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'ipoctet',
        'expire:dateTime',
        'ts:dateTime',

        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{delete}',
        ],
      ],
    ]); ?>
  </details>

</div>