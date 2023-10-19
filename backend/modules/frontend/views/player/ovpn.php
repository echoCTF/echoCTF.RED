client
nobind
proto udp
dev tun
comp-lzo
verb 3
mssfix 1400
cipher AES-256-CBC
auth SHA256
auth-nocache

remote-cert-tls server

remote <?php echo Yii::$app->sys->vpngw;?> 1194 udp
remote <?php echo Yii::$app->sys->vpngw;?> 443 tcp

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
