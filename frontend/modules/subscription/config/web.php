<?php
return [
  'components' => [
    'urlManager'=>[
      'class' => 'yii\web\UrlManager',
      'rules' => [
        'subscriptions' => 'subscription/default/index',
        'subscription/success'=>'subscription/default/success',
        'subscription/default/create-checkout-session'=>'subscription/default/create-checkout-session',
        'subscription/default/checkout-session'=>'subscription/default/checkout-session',
        'subscription/default/customer-portal'=>'subscription/default/customer-portal',
        'subscription/default/redirect-customer-portal'=>'subscription/default/redirect-customer-portal',
        'subscription/default/webhook'=>'subscription/default/webhook',
        'subscription/default/inquiry'=>'subscription/default/inquiry',
      ]
    ]
  ],
];
