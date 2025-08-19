client
nobind
proto udp
dev tun
compress
verb 1
mssfix 1460
cipher AES-128-GCM
data-ciphers AES-128-GCM
auth-nocache
mute-replay-warnings

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