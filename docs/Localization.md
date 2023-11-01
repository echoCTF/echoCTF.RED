# Project Localization
The frontend (and soon backend also) are fully localized.
In order to activate translations for your instance you have to edit the config/web.php and set the appropriate language
```php
$config=[
    'id' => 'pui2',
    'language' => 'el-GR',
```

You can create translations to your language by copying the existing english folder
```sh
cp -pr frontend/messages/en frontend/messages/el
```

Edit the files and translate the text accordingly. More information regarding the translations and the message formats can be found at
https://www.yiiframework.com/doc/guide/2.0/en/tutorial-i18n
