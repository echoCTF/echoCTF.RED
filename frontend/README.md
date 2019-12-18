# echoCTF.RED participants interface

## Update instructions
```
cd echoCTF.RED
git pull
cd frontend/
composer update
yii migrate/up --interactive=0
```
## Installation
```
git clone https://github.com/echoCTF/echoCTF.RED.git
cd echoCTF.RED/frontend
composer install
cp config/db-local.php config/db.php
cp config/memcached-local.php config/memcached.php
```

## General Information

echoCTF.RED/frontend is based on Yii 2 Basic Project Template.

### DIRECTORY STRUCTURE

    action/             contains controller actions
    assets/             contains assets definition
    commands/           contains console commands (controllers)
    config/             contains application configurations
    components/         contains application component classes
    controllers/        contains Web controller classes
    mail/               contains view files for e-mails
    models/             contains model classes
    modules/            contains modules of model class collections
    runtime/            contains files generated during runtime
    themes/             contains themes for the Web application
    vendor/             contains dependent 3rd-party packages
    views/              contains view files for the Web application
    web/                contains the entry script and Web resources
    widgets/            contains custom widgets



### REQUIREMENTS

The minimum requirement by this project template that your Web server supports PHP 5.4.0.


### INSTALLATION
#### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

Set cookie validation key in `config/web.php` file to some random secret string:

```php
'request' => [
    // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
    'cookieValidationKey' => '<secret random string goes here>',
],
```

You can then access the application through the following URL:

~~~
http://localhost/basic/web/
~~~

### CONFIGURATION

#### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTES:**
- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.
