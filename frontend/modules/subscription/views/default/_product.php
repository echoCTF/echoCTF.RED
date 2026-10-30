<div class="col-xl-5 col-lg-5 col-md-6 col-sm-6">
  <?php \app\widgets\Card::begin([
    'header' => 'header-icon',
    'type' => 'card-stats',
    'icon' => '<img src="/images/' . $model->shortcode . '.svg" width="50px">',
    'color' => 'rose',
    'encode' => false,
    'title' => '<b>' . $model->htmlOptions('title') . '</b>',
    'footer' =>  null,
  ]); ?>
  <?= $model->description ?>
  <?= $model->metadataObj->perks ?>
  <?php foreach ($model->prices as $price) : ?>
    <?php if ($price->active) : ?>
    <?php
    if ($price->recurring_interval === 'one-off' || $price->recurring_interval === 'payment') {
      // One-off prices with a duration show it, eg "5 days"
      $days = intval($price->metadataObj->days ?? 0);
      $period = $days > 0 ? Yii::t('app', '{n, plural, =1{# day} other{# days}}', ['n' => $days]) : $price->recurring_interval;
    } elseif (intval($price->interval_count) > 1) {
      // Recurring prices billed every N intervals, eg "6 months"
      $period = intval($price->interval_count) . ' ' . $price->recurring_interval . 's';
    } else {
      $period = $price->recurring_interval;
    }
    ?>
      <div class="col">
        <?php echo yii\bootstrap\Button::widget([
          'label' => Yii::$app->formatter->asCurrency(intval($price->unit_amount / 100)) . '/' . $period,
          'options' => ['class' => 'btn ' . $model->htmlOptions('class') . ' text-dark text-bold', 'id' => $price->id],
        ]);
        $this->registerJs('document.getElementById("' . $price->id . '")
                          .addEventListener("click", function(evt) {
                            evt.preventDefault();
                            createCheckoutSession("' . $price->id . '").then(function(data) {
                              Swal.fire({
                                title: "",
                                html: "' . \Yii::t('app', 'You will be redirected to Stripe to complete your payment') . '",
                                allowOutsideClick: false
                              }).then((result) => {
                                if(data)
                                  stripe.redirectToCheckout({ sessionId: data.sessionId }).then(handleResult);
                                });
                            });
                          });
                        ', \yii\web\View::POS_READY);
        ?>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
  <?php \app\widgets\Card::end(); ?>
</div>
