<?php
/* @var $this yii\web\View */
use \yii\helpers\Html;
$this->title=Yii::$app->sys->event_name ." Subscriptions";
$this->_url=\yii\helpers\Url::to([null],'https');
//$this->registerJsFile("https://js.stripe.com/v3/",['position'=>1]);
//$this->registerCss(file_get_contents(__DIR__."/pricing.css"));
?>
<div class="site-index">
    <div class="body-content">

      <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
        <h1 class="display-4 text-primary">Subscription completed</h1>
        <p class="lead">Your echoCTF.RED Subscription is now completed. You will receive a notification informing you about your its activation shortly.</p>
      </div>
      <div class="container">
          <div class="row d-flex justify-content-center">
            <form id="manage-billing-form">
              <button class="btn btn-block btn-info btn-large h4 font-weight-bold">Manage Billing</button>
            </form>
          </div>
          <div class="row">
            <pre class="text-light"></pre>
          </div>
      </div>
    </div>
</div>
<?php
$this->registerJs('
var sessionId="'.$success_session['id'].'";
let customerId;

  fetch("'.\yii\helpers\Url::to(['/subscription/default/checkout-session','sessionId'=>$success_session['id']]).'")
    .then(function(result){
      return result.json()
    })
    .then(function(session){
    })
    .catch(function(err){
      console.log("Error when fetching Checkout session", err);
    });
    const manageBillingForm = document.querySelector("#manage-billing-form");
    manageBillingForm.addEventListener("submit", function(e) {
      e.preventDefault();
      fetch("'.\yii\helpers\Url::to(['/subscription/default/customer-portal']).'", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          sessionId: sessionId,
          "'.Yii::$app->request->csrfParam.'": "'.Yii::$app->request->csrfToken.'"
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          window.location.href = data.url;
        })
        .catch((error) => {
          console.error("Error:", error);
        });
    });
');
