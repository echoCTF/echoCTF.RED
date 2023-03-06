<div class="col-xl-4 col-lg-5 col-md-6 col-sm-6">
  <div class="pricingTable rounded <?= $model->htmlOptions('class') ?>">
    <div class="pricingTable-header">
      <h3 class="title"><img src="/images/<?= $model->shortcode ?>.svg" width="50px"> <?= $model->htmlOptions('title') ?></h3>
    </div>
    <ul class="pricing-content">
      <?= $model->perks ?>
    </ul>
    <div class="row">
      <?php foreach ($model->prices as $price) : ?>
        <?php if ($price->active) : ?>
            <div class="col"><?php echo yii\bootstrap\Button::widget([
                                'label' => "€" . intval($price->unit_amount / 100) . '/' . $price->recurring_interval,
                                'options' => ['class' => 'btn '.$model->htmlOptions('class').' text-dark text-bold', 'id' => $price->id],
                              ]);
                              $this->registerJs('document
                              .getElementById("' . $price->id. '")
                              .addEventListener("click", function(evt) {
                                evt.preventDefault();
                                createCheckoutSession("' . $price->id . '").then(function(data) {
                                  Swal.fire("","' . \Yii::t('app', 'You will be redirected to Stripe to complete your payment') . '").then((result) => {
                                    if(data)
                                      stripe.redirectToCheckout({ sessionId: data.sessionId }).then(handleResult);
                                    });
                                });
                              });
                            ',\yii\web\View::POS_READY);
                          ?></div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</div>
