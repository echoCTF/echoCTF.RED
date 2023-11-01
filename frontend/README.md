# echoCTF.RED participants interface

## Update instructions
```sh
cd echoCTF.RED
git pull
cd frontend/
composer update
yii migrate/up --interactive=0
```
## Installation
```sh
git clone https://github.com/echoCTF/echoCTF.RED.git
cd echoCTF.RED/frontend
composer install
cp config/db-local.php config/db.php
cp config/validationKey-local.php config/validationKey.php
cp config/memcached-local.php config/memcached.php
```

Set cookie validation key in `config/validationKey.php` file to some random secret string:

```php
return 'REPLACEME';
```

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=echoCTF',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTES:**
- There won't be an automatic creation of the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize the frontend as required.

## Testing ajax forms
```js
var url="http://localhost:8082/profile/settings";
$.post( url, { "Player[username]": "John" });
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
The minimum requirement by this project template that your Web server supports PHP 7.0.

### Security Checking
Install the needed composer packages and run phpcs
```sh
composer global require "squizlabs/php_codesniffer=*"
composer global require pheromone/phpcs-security-audit
composer global require dealerdirect/phpcodesniffer-composer-installer
phpcs  --standard=Security .
```
