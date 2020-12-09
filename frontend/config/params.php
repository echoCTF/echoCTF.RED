<?php
return [
  'admin_ids' => [1],
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
