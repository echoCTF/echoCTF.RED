<?php

use \yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Noti;
use app\widgets\target\TargetWidget;

/* @var $this yii\web\View */

$title = \Yii::t('app', "Configure: {perkName}", ['perkName' => $model->product->name]);
$this->title = Yii::$app->sys->event_name . " " . $title;
$this->_url = \yii\helpers\Url::to([null], 'https');
$this->_fluid = "-fluid";
if (isset($model->metadataObj->private_network_id)) {
  $privateNetwork=\app\modules\network\models\PrivateNetwork::findOne($model->metadataObj->private_network_id);
  $tmod = \app\modules\target\models\Target::find();
  $query = $tmod->forPrivateNet($model->metadataObj->private_network_id)->player_progress(Yii::$app->user->id)->private_network_player_progress_select();

  $networkTargetProvider = new \yii\data\ActiveDataProvider([
    'query' => $query,
    'pagination' => [
      'pageSizeParam' => 'target-perpage',
      'pageParam' => 'target-page',
    ]
  ]);
}

?>
<div class="team-index">
  <div class="body-content">
    <h2><?= Html::encode($title) ?></h2>
    <?php if (isset($model->product->metadataObj->private_instances) && count($privateNetwork->privateTargets) < intval($model->product->metadataObj->private_instances)): ?>
      <div class="row">
        <div class="col-5">
          <?php echo Html::beginForm(['/subscription/perk/configure', 'id' => $model->id], 'post', ['id' => 'targetAutocompleteForm', 'autocomplete' => "off"]); ?>
          <div class="input-group no-border" style="padding-right: 10px;">
            <?php echo Html::submitButton(
              '<i class="material-icons flag-claim">add</i><div class="ripple-container"></div>',
              ['class' => 'btn btn-white btn-round btn-just-icon', 'rel' => 'tooltip', 'title' => \Yii::t('app', 'Add target to the network')]
            ); ?>
            <?= Html::hiddenInput('target_id') ?>

            <?= Html::input('text', 'name', '', [
              'id' => 'targetAutocomplete',
              'class' => "form-control basicAutoComplete",
              'style' => 'text-align: center',
              'aria-label' => \Yii::t('app', 'search'),
              'placeholder' => \Yii::t('app', "add target"),
              'autocomplete' => "off",
              "data-url" => Url::toRoute(['/target/default/search'])
            ]); ?>
          </div>
          <?= Html::endForm(); ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if (isset($networkTargetProvider)): ?>
      <?php \yii\widgets\Pjax::begin(['id' => 'target-listing-pjax', 'enablePushState' => false, 'linkSelector' => '#target-pager a, #target-list th a', 'formSelector' => false]); ?>
      <?= TargetWidget::widget(['viewFile' => 'target-list', 'twitter' => false, 'buttonsTemplate' => '', 'dataProvider' => $networkTargetProvider, 'player_id' => Yii::$app->user->id, 'profile' => Yii::$app->user->identity->profile, 'title' => '', 'category' => '', 'personal' => false, 'targetRoute' => '/network/private/target', 'hidden_attributes' => ['id']]); ?>
      <?php \yii\widgets\Pjax::end() ?>
    <?php endif; ?>

  </div>
</div>
<?php
if (isset($privateNetwork) &&  isset($model->product->metadataObj->private_instances) && count($privateNetwork->privateTargets) < intval($model->product->metadataObj->private_instances)) {
  $this->registerJs("
$(document).on('autocomplete.select','#targetAutocompleteForm', function (evt, item) {
  var targetId = item.id;
  $('[name=\"target_id\"]').val(targetId);
});
$(document).on('submit','#targetAutocompleteForm', function (evt) {
  if ($('[name=\"target_id\"]').val() === '') {
    evt.preventDefault();
  }
} );
$('#targetAutocomplete').autoComplete({
  minLength: 3,
  formatResult: function (item) {
    return {
      href: item.url,
      value: item.id,
      text: item.name,
      html: [ $('<img>').attr('src', item.icon).css('height', 28).css('padding-right',8).css('margin-left',0), ' ', item.name ]
    };
  },
  events: {
    search: function (qry, callback) {
      // let's do a custom ajax call
      $.ajax(
        $('#targetAutocomplete').attr('data-url'),
        {
          data: { 'q': qry}
        }
      ).done(function (res) {
        callback(res.results)
      });
    }
  }});
", $this::POS_READY, 'target-perk-search');
}
