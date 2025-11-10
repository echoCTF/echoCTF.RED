# Sales module for backend
<https://github.com/stripe/stripe-php>

1. (optional) Install stripe php bindings on the `echoCTF.RED/backend` folder `composer require stripe/stripe-php`. Its not being used currently but soon it will

2. copy the `sales` folder, into `echoCTF.RED/backend/modules`

3. add the following into `backend/config/web.php` modules section

```php
'modules' => [
      'sales' => [
          'class' => 'app\modules\sales\Module',
      ],
  ],
```

Configuration files under `config/`:

* `main.php` common configuration parameters for both console and web applications
* `web.php` web application configuration
* `console.php` console application configuration

## Testing

1. Enable testing mode on your stripe dashboard and copy the details
2. Install Stripe Shell (<https://github.com/stripe/stripe-cli/releases/latest>)
3. Set your testing keys
   * `stripe_publicApiKey`: `pk_test_STRIPE_PUBLIC_KEY`
   * `stripe_apiKey`: `sk_test_STRIPE_DEV_API_KEY`
   * `stripe_webhookSecret`: `whsec_STRIPE_DEV_SECRET`
4. Login using stripe `stripe login --api-key sk_test_STRIPE_DEV_API_KEY`
5. Listen for event and forward to your local webhooks url `stripe listen --forward-to localhost:8082/subscription/secreturl/webhook`
