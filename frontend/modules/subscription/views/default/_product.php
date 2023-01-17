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
            <div class="col"><?= yii\bootstrap\Button::widget([
                                'label' => "€" . intval($price->unit_amount / 100) . '/' . $price->recurring_interval,
                                'options' => ['class' => 'btn '.$model->htmlOptions('class').' text-dark text-bold', 'id' => $model->shortcode . '_' . $price->recurring_interval],
                              ]); ?></div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php
if ($mine && $model->inPrices($mine->price_id) !== null && $mine->active)
  $this->registerJs('document
    .getElementById("mySub")
    .addEventListener("click", function(evt) {
      evt.preventDefault();
      return false;
    });
  ');
else
  foreach ($model->prices as $price)
    $this->registerJs('document
    .getElementById("' . $model->shortcode . '_' . $price->recurring_interval . '")
    .addEventListener("click", function(evt) {
      evt.preventDefault();
      createCheckoutSession("' . $price->id . '").then(function(data) {
        Swal.fire("","' . \Yii::t('app', 'You will be redirected to Stripe to complete your payment') . '").then((result) => {
          if(data)
            stripe.redirectToCheckout({ sessionId: data.sessionId }).then(handleResult);
          });
      });
    });
  ');
