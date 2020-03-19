#!/bin/bash
if [ ! -f /etc/openvpn/.configured ]; then
    echo "OpenVPN not configured, sleeping 30 seconds for mysql to come up"
    sleep 30
    mkdir -p /etc/openvpn/certs /etc/openvpn/client_confs /var/log/openvpn /etc/openvpn/crl /etc/openvpn/ccd
    install -d -m 700 /etc/openvpn/private
    cp contrib/openvpn_tun0.conf /etc/openvpn
    echo "<?php return [ 'class' => 'yii\db\Connection', 'dsn' => 'mysql:host=${MYSQL_HOST};dbname=${MYSQL_DATABASE}', 'username' => '${MYSQL_USER}', 'password' => '${MYSQL_PASSWORD}', 'charset' => 'utf8',  ];">backend/config/db.php
    sed -e "s#{{db.host}}#${MYSQL_HOST}#g" contrib/echoctf_updown_mysql.sh > /etc/openvpn/echoctf_updown_mysql.sh
    sed -e "s#ksh#bash#" -e "s#127.0.0.1#${MYSQL_HOST}#g" -e "s#{{db.user}}#${MYSQL_USER}#g" -e "s#{{db.pass}}#${MYSQL_PASSWORD}#g" -i /etc/openvpn/echoctf_updown_mysql.sh
    chmod 555 /etc/openvpn/echoctf_updown_mysql.sh
    cp contrib/crl_openssl.conf /etc/openvpn/crl/
    touch /etc/openvpn/crl/index.txt
    echo "00" > /etc/openvpn/crl/number
    echo "OPENVPN_ADMIN_PASSWORD">/etc/openvpn/private/mgmt.pwd
    openssl dhparam -out /etc/openvpn/dh.pem 2048
    openvpn --genkey --secret /etc/openvpn/private/vpn-ta.key
    /var/www/echoCTF.RED/backend/yii migrate --interactive=0
    /var/www/echoCTF.RED/backend/yii init_data --interactive=0
    /var/www/echoCTF.RED/backend/yii ssl/create-ca
    /var/www/echoCTF.RED/backend/yii ssl/get-ca 1
    /var/www/echoCTF.RED/backend/yii ssl/create-cert "VPN Server"
    mv echoCTF-OVPN-CA.crt /etc/openvpn/private/echoCTF-OVPN-CA.crt
    mv echoCTF-OVPN-CA.key /etc/openvpn/private/echoCTF-OVPN-CA.key
    mv VPN\ Server.crt /etc/openvpn/private/VPN\ Server.crt
    mv VPN\ Server.key /etc/openvpn/private/VPN\ Server.key
    chmod 400 /etc/openvpn/private/*
    /var/www/echoCTF.RED/backend/yii ssl/create-crl
    /var/www/echoCTF.RED/backend/yii ssl/load-vpn-ta
    touch /etc/openvpn/.configured
fi
openvpn --dev tun0 --config /etc/openvpn/openvpn_tun0.conf
$@
