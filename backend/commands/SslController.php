<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\Crl;
use app\modules\frontend\models\PlayerSsl;
use app\modules\frontend\models\PlayerIp;
use app\modules\gameplay\models\Target;
use app\modules\settings\models\Sysconfig;

class SslController extends Controller {

  public $ssl_params= "-config /etc/openvpn/crl/crl_openssl.conf -keyfile /etc/openvpn/private/echoCTF-OVPN-CA.key -cert /etc/openvpn/private/echoCTF-OVPN-CA.crt";

  public function actionCreateCa($fileout=false) {
    $privkey = openssl_pkey_new(Yii::$app->params['pkey_config']);

    // Generate a certificate signing request
    $csr = openssl_csr_new(Yii::$app->params['dn'], $privkey, array('digest_alg' => 'sha256', 'x509_extensions'=>'v3_ca', 'config'=>__DIR__ . '/../config/CA.cnf','encrypt_key'=>false));

    // sign csr
    $x509 = openssl_csr_sign($csr, null, $privkey, $days=365, array('digest_alg' => 'sha256', 'config'=>__DIR__ . '/../config/CA.cnf','encrypt_key'=>false));

    openssl_csr_export($csr, $csrout);
    openssl_x509_export($x509, $crtout);
    openssl_pkey_export($privkey, $pkeyout);
    openssl_x509_export($x509, $certout, false);
    $cacsr=Sysconfig::findOne('CA.csr') ;
    $cacrt=Sysconfig::findOne('CA.crt') ;
    $catxtcrt=Sysconfig::findOne('CA.txt.crt') ;
    $cakey=Sysconfig::findOne('CA.key');
    if(!$cacsr)  $cacsr=new Sysconfig;
    if(!$cacrt)  $cacrt=new Sysconfig;
    if(!$catxtcrt)  $catxtcrt=new Sysconfig;
    if(!$cakey)  $cakey=new Sysconfig;

    $cacsr->id='CA.csr';
    $cacrt->id='CA.crt';
    $catxtcrt->id='CA.txt.crt';
    $cakey->id='CA.key';

    $cacsr->val=$csrout;
    $cacrt->val=$crtout;
    $catxtcrt->val=$certout;
    $cakey->val=$pkeyout;

    $cacsr->save();
    $cacrt->save();
    $catxtcrt->save();
    $cakey->save();
    if($fileout)
    {
      file_put_contents("echoCTF-OVPN-CA.csr", $csrout);
      file_put_contents("echoCTF-OVPN-CA.crt", $crtout);
      file_put_contents("echoCTF-OVPN-CA.txt.crt", $certout);
      file_put_contents("echoCTF-OVPN-CA.key",$pkeyout);
    }

  }

  public function actionCreateCert($commonName="VPN Server",$emailAddress=null, $subjectAltName='IP:0.0.0.0', $CAcert = "file://echoCTF-OVPN-CA.crt", $CAkey="file://echoCTF-OVPN-CA.key"){
    Yii::$app->params['dn']['commonName'] = $commonName;
    if($emailAddress!==null) Yii::$app->params['dn']['emailAddress']=$emailAddress;
    if($subjectAltName!=='IP:0.0.0.0') Yii::$app->params['dn']['subjectAltName']=$subjectAltName;

    // Generate a new private (and public) key pair
    $privkey = openssl_pkey_new(Yii::$app->params['pkey_config']);

    // Generate a certificate signing request
    $csr = openssl_csr_new(Yii::$app->params['dn'], $privkey, array('digest_alg' => 'sha256', 'config'=>__DIR__ . '/../config/CA.cnf','encrypt_key'=>false));
    // Generate a self-signed cert, valid for 365 days
    $CAprivkey = array($CAkey,null);
    //$CAcert = "file:///etc/openvpn/certs/echoCTF-OVPN-CA.crt";
    //$CAprivkey = array("file:///etc/openvpn/private/echoCTF-OVPN-CA.key");
    $x509 = openssl_csr_sign($csr, $CAcert, $CAprivkey, 365, array('digest_alg'=>'sha256','config'=>__DIR__ . '/../config/CA.cnf','x509_extensions'=>'server_cert'), 0 );

    openssl_csr_export($csr, $csrout);
    openssl_x509_export($x509, $certout,false);
    openssl_x509_export($x509, $crtout);
    openssl_pkey_export($privkey, $pkeyout);

    file_put_contents($commonName.".csr", $csrout);
    file_put_contents($commonName.".txt.crt", $certout);
    file_put_contents($commonName.".crt", $crtout);
    file_put_contents($commonName.".key",$pkeyout);
  }

  /* Generate certificates for a particular $player_id */
  public function actionGenPlayerCerts($email,$fileout=false, $ccd=false) {
    $player=Player::findOne(['email'=>$email]);
    $this->genPlayer($player,$fileout,$ccd);
  }

  /* Generate certificates for a all players */
  public function actionGenAllPlayerCerts($fileout=false, $ccd=false) {
    foreach (Player::find()->all() as $player)
    {
        $this->genPlayer($player,$fileout,$ccd);
    }
  }

  public function actionGetPlayerCerts($email=false,$fileout=false,$ccd=false) {
    $players=Player::find();
    if($email!==false)
      $players->where(['email'=>$email]);
    foreach ($players->all() as $player)
    {
      printf("Processing %d:%s\n",$player->id,$player->username);
      if($player->playerSsl==NULL)
      {
        echo "Player SSL not found for ",$player->username,"\n";
        continue;
      }
      $ps=$player->playerSsl;
      if($ccd)
      {
        printf ("Generating /etc/openvpn/ccd/%s\n",$player->username);
        $ccd_file=sprintf("/etc/openvpn/ccd/%s",$player->username);
        $ccd_content=sprintf("ifconfig-push %s %s\n",long2ip($player->playerIp->ip),long2ip($player->playerIp->ip+1));

        if(is_dir("/etc/openvpn/ccd"))
          file_put_contents($ccd_file,$ccd_content);
      }
      if($fileout)
      {
        file_put_contents($player->username.".txt.crt", $ps->txtcrt);
        file_put_contents($player->username.".crt", $ps->crt);
        file_put_contents($player->username.".key",$ps->privkey);
      }
    }
  }
  public function actionGetCa($fileout=false) {
    $crt=Sysconfig::findOne('CA.crt')->val;
    $key=Sysconfig::findOne('CA.key')->val;
    $csr=Sysconfig::findOne('CA.csr')->val;
    $txtcrt=Sysconfig::findOne('CA.txt.crt')->val;
    if($fileout)
    {
        file_put_contents("echoCTF-OVPN-CA.csr", $csr);
        file_put_contents("echoCTF-OVPN-CA.crt", $crt);
        file_put_contents("echoCTF-OVPN-CA.txt.crt", $txtcrt);
        file_put_contents("echoCTF-OVPN-CA.key",$key);
    }
    else
    {
      printf("echoCTF-OVPN-CA.csr\n%s", $csr);
      printf("echoCTF-OVPN-CA.crt\n%s", $crt);
      printf("echoCTF-OVPN-CA.txt.crt\n%s", $txtcrt);
      printf("echoCTF-OVPN-CA.key\n%s",$key);
    }
  }

  public function actionLoadVpnTa($file='/etc/openvpn/private/vpn-ta.key')
  {
    $vpnta=Sysconfig::findOne('vpn-ta.key');
    if($vpnta==null)
    {
      $vpnta=new Sysconfig;
      $vpnta->id='vpn-ta.key';
    }

    if(file_exists($file))
    {
      $vpnta->val=file_get_contents($file);
      return $vpnta->save();
    }
    else printf("File not found: %s\n",$file);
    return -1;
  }

  public function actionCreateCrl()
  {

    $cmd=sprintf("openssl ca -gencrl %s -out /var/openvpn/chrootjail/etc/openvpn/crl.pem",$this->ssl_params);
    shell_exec($cmd);
    echo "Not implemented\n";
  }

  public function actionGenerateCrl()
  {
    $CERTS=Crl::find()->all();
    foreach($CERTS as $cert)
    {
      $tmpcrt=tempnam( '/tmp', 'crt' );
      file_put_contents($tmpcrt,$cert->crt);
      $cmd=sprintf("openssl ca -revoke %s %s ", $tmpcrt,$this->ssl_params);
      shell_exec($cmd);
      unlink($tmpcrt);
    }
    $this->actionCreateCrl();
  }


  public function actionRevoke($player_id)
  {
    $player=Player::findOne($player_id);
    $tmpcrt=tempnam( '/tmp', 'crt' );
    file_put_contents($tmpcrt,$player->playerSsl->crt);
    $cmd=sprintf("openssl ca -revoke %s %s ", $tmpcrt,$this->ssl_params);
    shell_exec($cmd);
    $this->actionCreateCrl();
    unlink($tmpcrt);
    echo "Not implemented\n";
  }

  public function genPlayer(Player $player,$fileout,$ccd,$CAcert = "file://echoCTF-OVPN-CA.crt",$CAkey="file://echoCTF-OVPN-CA.key")
  {
    printf("Processing %d:%s\n",$player->id,$player->username);
    Yii::$app->params['dn']['commonName'] = $player->id;
    Yii::$app->params['dn']['emailAddress']=$player->email;
    if($player->playerIp!==NULL)
      Yii::$app->params['dn']['subjectAltName']=sprintf('IP:%s',$player->playerIp->ipoctet);
    else if($player->teamPlayer!==NULL)
      Yii::$app->params['dn']['subjectAltName']=sprintf('IP:10.10.%d.%d',$player->teamPlayer->id, $player->id);
    else
      Yii::$app->params['dn']['subjectAltName']=sprintf('IP:10.10.250.%d', $player->id);

    // Generate a new private (and public) key pair
    $privkey = openssl_pkey_new(Yii::$app->params['pkey_config']);

    // Generate a certificate signing request
    $csr = openssl_csr_new(Yii::$app->params['dn'], $privkey, array('digest_alg' => 'sha256', 'config'=>__DIR__ . '/../config/CA.cnf','encrypt_key'=>false));
    // Generate a self-signed cert, valid for 365 days
    $CAprivkey = array($CAkey,null);
    $x509 = openssl_csr_sign($csr, $CAcert, $CAprivkey, 365, array('digest_alg'=>'sha256','config'=>__DIR__ . '/../config/CA.cnf','x509_extensions'=>'usr_cert'), time() );

    openssl_csr_export($csr, $csrout);
    openssl_x509_export($x509, $certout,false);
    openssl_x509_export($x509, $crtout);
    openssl_pkey_export($privkey, $pkeyout);
    if($player->playerSsl!==NULL)
      $ps=$player->playerSsl;
    else
      $ps=new PlayerSsl;
    $ps->player_id=$player->id;
    $ps->subject=serialize(Yii::$app->params['dn']);
    $ps->csr=$csrout;
    $ps->crt=$crtout;
    $ps->txtcrt=$certout;
    $ps->privkey=$pkeyout;
    $ps->save();
    if($fileout)
    {
      file_put_contents($player->username.".txt.crt", $certout);
      file_put_contents($player->username.".crt", $crtout);
      file_put_contents($player->username.".key",$pkeyout);
    }
    if($ccd)
    {
      $ccd_file=sprintf("/etc/openvpn/ccd/%s",$player->username);
      $ccd_content=sprintf("ifconfig-push %s %s\n",long2ip($player->id),long2ip($player->id+1));

      if(is_dir("/etc/openvpn/ccd"))
        file_put_contents($ccd_file,$ccd_content);
    }

  }
}
