<?php
return [
  'admin_ids' => [1],
  'adminEmail' => 'info@echothrust.com',
  'senderEmail' => 'noreply@echothrust.com',
  'senderName' => 'Echothrust mailer',
  'dn' => [
    'countryName' => 'GR',
    'stateOrProvinceName' => 'Greece',
    'localityName' => 'Athens',
    'organizationName' => 'echoCTF',
    'organizationalUnitName' => 'echoctf.red',
    "commonName" => "ROOT CA",
  ],
  'pkey_config' => [
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
    "encrypt_key" => false
  ],
  'serverPublisher' => [
    'url'        => 'http://localhost:8888',
    'wsEndpoint' => 'ws://localhost:8888/ws',
    'token'      => 'server123token',
    'timeout'    => 5,
    'maxRetries' => 3,
    'backoffMs'  => 200,
  ],
];
