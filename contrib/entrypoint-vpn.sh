#!/bin/bash
echo "<?php return [ 'class' => 'yii\db\Connection', 'dsn' => 'mysql:host=${MYSQL_HOST};dbname=${MYSQL_DATABASE}', 'username' => '${MYSQL_USER}', 'password' => '${MYSQL_PASSWORD}', 'charset' => 'utf8mb4',  ];">/var/www/echoCTF.RED/backend/config/db.php
cp /var/www/echoCTF.RED/backend/config/cache-local.php /var/www/echoCTF.RED/backend/config/cache.php
sed -ie "s/127.0.0.1/${MYSQL_HOST}/g" /var/www/echoCTF.RED/backend/config/cache.php

if [ ! -f /etc/openvpn/.configured ]; then
    echo "OpenVPN not configured"
    openssl dhparam -out /etc/openvpn/dh.pem 2048
    echo "Sleeping 30 seconds" && sleep 30
    mkdir -p /etc/openvpn/certs /etc/openvpn/client_confs /var/log/openvpn /etc/openvpn/crl /etc/openvpn/ccd
    install -d -m 700 /etc/openvpn/private
    cp contrib/openvpn_tun0.conf /etc/openvpn
    sed -e "s#{{db.host}}#${MYSQL_HOST}#g" contrib/echoctf_updown_mysql.sh > /etc/openvpn/echoctf_updown_mysql.sh
    sed -e "s#ksh#bash#" -e "s#127.0.0.1#${MYSQL_HOST}#g" -e "s#{{db.user}}#${MYSQL_USER}#g" -e "s#{{db.pass}}#${MYSQL_PASSWORD}#g" -i /etc/openvpn/echoctf_updown_mysql.sh
    chmod 555 /etc/openvpn/echoctf_updown_mysql.sh
    cp contrib/crl_openssl.conf /etc/openvpn/crl/
    touch /etc/openvpn/crl/index.txt
    echo "00" > /etc/openvpn/crl/number
    echo "${OPENVPN_ADMIN_PASSWORD}">/etc/openvpn/private/mgmt.pwd
    openvpn --genkey secret /etc/openvpn/private/vpn-ta.key
    /var/www/echoCTF.RED/backend/yii migrate --interactive=0
    /var/www/echoCTF.RED/backend/yii migrate-sales/up --interactive=0
    /var/www/echoCTF.RED/backend/yii init_data --interactive=0
    /var/www/echoCTF.RED/backend/yii ssl/create-ca
    /var/www/echoCTF.RED/backend/yii ssl/get-ca 1
    /var/www/echoCTF.RED/backend/yii ssl/create-cert "VPN Server"
    /var/www/echoCTF.RED/backend/yii vpn/load /etc/openvpn/openvpn_tun0.conf
    mv echoCTF-OVPN-CA.crt /etc/openvpn/private/echoCTF-OVPN-CA.crt
    mv echoCTF-OVPN-CA.key /etc/openvpn/private/echoCTF-OVPN-CA.key
    mv VPN\ Server.crt /etc/openvpn/private/VPN\ Server.crt
    mv VPN\ Server.key /etc/openvpn/private/VPN\ Server.key
    chmod 400 /etc/openvpn/private/*
    /var/www/echoCTF.RED/backend/yii ssl/create-crl
    /var/www/echoCTF.RED/backend/yii ssl/load-vpn-ta
    touch /etc/openvpn/.configured
    echo "***************************************"
    echo "*** The systems are now configured. ***"
    echo "***************************************"
fi
while ! mysqlshow -h db > /dev/null 2>&1
do
    echo "Failed to connect to [db], waiting 1 second" && sleep 1
done

echo "Attempting to spin up targets"
backend target/spin


$@
