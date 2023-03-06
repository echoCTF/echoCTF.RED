<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Noti;
?>
<?php echo Html::beginForm(['#'], 'post', ['class'=>'navbar-form', 'id'=>'search', 'autocomplete'=>"off"]);?>
  <div class="input-group no-border" style="padding-right: 10px;">
  <?php echo Html::submitButton('<i class="material-icons flag-claim">search</i><div class="ripple-container"></div>',
                      ['class' => 'btn btn-white btn-round btn-just-icon','rel'=>'tooltip','title'=>\Yii::t('app','Search for a Target')]
      );?>
    <?=Html::input('text', 'name', '', [
      'id'=>'autocomplete',
      'class'=>"form-control basicAutoComplete",
      'style'=>'text-align: center',
      'aria-label'=>\Yii::t('app','search'),
      'placeholder'=>\Yii::t('app',"search target"),
      'autocomplete'=>"off",
      "data-url"=>Url::toRoute(['/target/default/search'])]);?>
  </div>
<?php
echo Html::endForm();
$this->registerJs("
$(document).on('autocomplete.select','#autocomplete', function (evt, item) { this.value=''; window.location=item.url; });
$(document).on('submit','#search', function (evt) { evt.preventDefault(); } );
$('#autocomplete').autoComplete({
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
        $('#autocomplete').attr('data-url'),
        {
          data: { 'q': qry}
        }
      ).done(function (res) {
        callback(res.results)
      });
    }
  }});",$this::POS_READY,'searching');
