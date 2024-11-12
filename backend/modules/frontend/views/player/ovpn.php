client
nobind
proto udp
dev tun
comp-lzo
verb 1
mssfix 1400
cipher AES-256-CBC
data-ciphers AES-256-CBC
auth SHA256
auth-nocache

remote-cert-tls server

remote <?php echo Yii::$app->sys->vpngw;?> 1194 udp

<key>
<?php echo $model->privkey;?>
</key>
<cert>
<?php echo $model->crt;?>
</cert>
<ca>
<?php echo Yii::$app->sys->{'CA.crt'}; ?>
</ca>
key-direction 1
<tls-auth>
<?php echo Yii::$app->sys->{'vpn-ta.key'}; ?>
</tls-auth>