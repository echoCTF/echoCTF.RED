# Stripe frontend/subscriptions module
https://github.com/stripe/stripe-php

1. Install stripe php bindings on the `echoCTF.RED/frontend` folder `composer require stripe/stripe-php`

2. copy the folder `subscriptions` into `echoCTF.RED/frontend/modules`.

3. edit `echoCTF.RED/frontend/modules/subscriptions/config.php` and update the products and the keys.

4. add to `frontend/config/web.php` modules section the following
```
'modules' => [
  'subscription' => [
      'class' => 'app\modules\subscription\Module',
  ],
...
```

5. add the urlManager rules (aliases can be whatever)
```
'subscriptions' => 'subscription/default/index',
'subscription/success'=>'subscription/default/success',
'subscription/default/create-checkout-session'=>'subscription/default/create-checkout-session',
'subscription/default/checkout-session'=>'subscription/default/checkout-session',
'subscription/default/customer-portal'=>'subscription/default/customer-portal',
'subscription/default/redirect-customer-portal'=>'subscription/default/redirect-customer-portal',
'subscription/default/webhook'=>'subscription/default/webhook',
'subscription/default/inquiry'=>'subscription/default/inquiry',
'subscription/default/testing'=>'subscription/default/testing',
```

And you are ready.

Currently the following webhook types are used
* `customer.subscription.updated`
* `customer.deleted`
* `customer.created`
* `customer.subscription.deleted`
* _`invoice.payment_failed`_ currently disabled
* _`checkout.session.completed`_ currently disabled
* _`invoice.paid`_ currently disabled


## Testing:
* Go to ngrok URL
* Login with your credentials
* Go to networks
* Click on subscribe
* Pick a subscription
* Provide the following credit card details
  - CC number 4242 4242 4242 4242
  - expiration: Any date in the future
  - CC verification code: any number

You are now redirected to the echoCTF site with a button about your billing details. Clicking that takes you to stripe and from there back to your profile.

Your profile now also has a button that redirects you to your stripe customer
portal where you can download invoices or upgrade/downgrade or cancel your
existing subscriptions.
