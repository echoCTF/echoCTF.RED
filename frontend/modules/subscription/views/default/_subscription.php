<div class="col-xl-5 col-lg-5 col-md-6 col-sm-6 d-flex mb-5">
  <div class="pricingTable rounded w-100 <?= $model->htmlOptions('class') ?>">
    <div class="pricingTable-header">
      <h3 class="title"><img src="/images/<?= $model->shortcode ?>.svg" width="50px"> <?= $model->htmlOptions('title') ?></h3>
    </div>
    <ul class="pricing-content">
      <?= $model->perks ?>
    </ul>
    <div class="row pricingTable-buttons">
      <?php
      // Read into a variable, empty() on \Yii::$app->sys->key is always true
      // because Sysconfig has no __isset()
      $salesEmail = \Yii::$app->sys->sales_email;
      ?>
      <?php if (intval($model->metadataObj->contact_only ?? 0) === 1 && !empty($salesEmail)) : ?>
        <?php
        // Contact only products have no price, the player emails us instead
        $player = \Yii::$app->user->identity;
        $mailto = 'mailto:' . $salesEmail
          . '?subject=' . rawurlencode($model->name)
          . '&body=' . rawurlencode(implode("\n", [
            'Hello,',
            '',
            'I would like to request the ' . $model->name . '.',
            '',
            'Institution: ',
            'Number of students: ',
            'Campus IP range(s): ',
            '',
            'Player: ' . $player->username . ' (id ' . $player->id . ')',
            'Name: ' . $player->fullname,
            'Email: ' . $player->email,
          ]));
        $contactId = 'contact-' . $model->id;
        ?>
        <div class="col">
          <?= yii\bootstrap\Button::widget([
            'label' => \Yii::t('app', 'Contact us'),
            'options' => ['class' => 'btn ' . $model->htmlOptions('class') . ' text-dark text-bold', 'id' => $contactId],
          ]) ?>
        </div>
        <?php $this->registerJs('document.getElementById(' . json_encode($contactId) . ').addEventListener("click", function(evt) {
            evt.preventDefault();
            Swal.fire({
              title: ' . json_encode($model->htmlOptions('title')) . ',
              html: ' . json_encode(\Yii::t('app', 'We will be contacting you with regards to this shortly. Press OK to open your email client with your details filled in, then send the email.')) . ',
              showCancelButton: true,
              confirmButtonText: ' . json_encode(\Yii::t('app', 'OK')) . ',
              cancelButtonText: ' . json_encode(\Yii::t('app', 'Cancel')) . '
            }).then(function(result) {
              if (result.value) {
                window.location.href = ' . json_encode($mailto, JSON_UNESCAPED_SLASHES) . ';
              }
            });
          });', \yii\web\View::POS_READY); ?>
      <?php endif; ?>
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
            $this->registerJs('document
                              .getElementById("' . $price->id . '")
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
    </div>
  </div>
</div><!--//col-->