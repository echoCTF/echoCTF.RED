<div class="col-xl-4 col-lg-5 col-md-6 col-sm-6">
    <div class="pricingTable rounded <?=$model->htmlOptions('class')?>">
        <div class="pricingTable-header">
            <h3 class="title"><img src="/images/<?=$model->shortcode?>.svg" width="50px"> <?=$model->htmlOptions('title')?></h3>
        </div>
        <div class="price-value">
            <span class="amount">€<?=$model->unit_amount/100?></span>
            <span class="duration">/<?=$model->interval?></span>
        </div>
        <ul class="pricing-content">
          <?=$model->perks?>
        </ul>
        <div class="pricingTable-signup">
<?php if($mine && $mine->price_id===$model->price_id && $mine->active):?>
          <a href="#" id="<?=$model->shortcode?>">Signed Up</a>
<?php else:?>
          <a href="#" id="<?=$model->shortcode?>">Sign Up</a>
<?php endif;?>
        </div>
    </div>
</div>
<?php
if($mine && $mine->price_id===$model->price_id && $mine->active)
  $this->registerJs('document
    .getElementById("'.$model->shortcode.'")
    .addEventListener("click", function(evt) {
      evt.preventDefault();
      return false;
    });
  ');
else
  $this->registerJs('document
    .getElementById("'.$model->shortcode.'")
    .addEventListener("click", function(evt) {
      evt.preventDefault();
      createCheckoutSession("'.$model->price_id.'").then(function(data) {
        Swal.fire("","You will be redirected to Stripe to complete your payment").then((result) => {
          if(data)
            stripe.redirectToCheckout({ sessionId: data.sessionId }).then(handleResult);
          });
      });
    });
  ');
