# Sales module for backend
https://github.com/stripe/stripe-php

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
