<?php
Yii::setAlias('@appconfig', realpath(dirname(__FILE__)));
return [
  'bsVersion' => '5.x',
  'vpn_ranges'=>[
      '10.43.0.0'=>['127.0.0.1',11196,'myovpnadminpass'],
      '10.10.0.0'=>['127.0.0.1',11195,'myovpnadminpass'],
    ],
    'adminEmail' => 'info@echothrust.com',
    'senderEmail' => 'noreply@echothrust.com',
    'senderName' => 'Echothrust mailer',
    'dn' => [
      'countryName' => 'GR',
      'stateOrProvinceName' => 'Greece',
      'localityName'=> 'Athens',
      'organizationName' => 'echoCTF',
      'organizationalUnitName' => 'echoctf.red',
      "commonName" => "ROOT CA",
    ],
    'pkey_config' => [
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
        "encrypt_key" => false
    ],
];
