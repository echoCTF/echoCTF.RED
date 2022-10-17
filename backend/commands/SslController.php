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
use yii\console\Exception as ConsoleException;

class SslController extends Controller {

  public $ssl_params="-config /etc/openvpn/crl/crl_openssl.conf -keyfile /etc/openvpn/private/echoCTF-OVPN-CA.key -cert /etc/openvpn/private/echoCTF-OVPN-CA.crt";

  /*
   * Create Certification Authority keys
   * If fileout param is provided then also store the keys into files under the current working directory
   * @param int $fileout.
   */
  public function actionCreateCa($fileout=false) {
    $privkey=openssl_pkey_new(Yii::$app->params['pkey_config']);
    $params=Yii::$app->params['dn'];
    $params['countryName']=\Yii::$app->sys->dn_countryName;
    $params['stateOrProvinceName']=\Yii::$app->sys->dn_stateOrProvinceName;
    $params['localityName']=\Yii::$app->sys->dn_localityName;
    $params['organizationName']=\Yii::$app->sys->dn_organizationName;
    $params['organizationalUnitName']=\Yii::$app->sys->dn_organizationalUnitName;

    // Generate a certificate signing request
    $csr=openssl_csr_new($params, $privkey, array('digest_alg' => 'sha256', 'x509_extensions'=>'v3_ca', 'config'=>__DIR__.'/../config/CA.cnf', 'encrypt_key'=>false));

    // sign csr
    $x509=openssl_csr_sign($csr, null, $privkey, $days=3650, array('digest_alg' => 'sha256', 'config'=>__DIR__.'/../config/CA.cnf', 'encrypt_key'=>false));

    openssl_csr_export($csr, $csrout);
    openssl_x509_export($x509, $crtout);
    openssl_pkey_export($privkey, $pkeyout);
    openssl_x509_export($x509, $certout, false);
    $cacsr=Sysconfig::findOneNew('CA.csr');
    $cacrt=Sysconfig::findOneNew('CA.crt');
    $catxtcrt=Sysconfig::findOneNew('CA.txt.crt');
    $cakey=Sysconfig::findOneNew('CA.key');

    $cacsr->val=$csrout;
    $cacrt->val=$crtout;
    $catxtcrt->val=$certout;
    $cakey->val=$pkeyout;

    $cacsr->save();
    $cacrt->save();
    $catxtcrt->save();
    $cakey->save();
    if((bool) $fileout)
    {
      file_put_contents("echoCTF-OVPN-CA.csr", $csrout);
      file_put_contents("echoCTF-OVPN-CA.crt", $crtout);
      file_put_contents("echoCTF-OVPN-CA.txt.crt", $certout);
      file_put_contents("echoCTF-OVPN-CA.key", $pkeyout);
    }
  }

  /*
   * Create a server certificate for the OpenVPN server and sign it with our CA
   */
  public function actionCreateCert($commonName="VPN Server", $emailAddress=null, $subjectAltName='IP:0.0.0.0') {
    $params=Yii::$app->params['dn'];
    $params['countryName']=\Yii::$app->sys->dn_countryName;
    $params['stateOrProvinceName']=\Yii::$app->sys->dn_stateOrProvinceName;
    $params['localityName']=\Yii::$app->sys->dn_localityName;
    $params['organizationName']=\Yii::$app->sys->dn_organizationName;
    $params['organizationalUnitName']=\Yii::$app->sys->dn_organizationalUnitName;
    $params['commonName']=$commonName;
    if($emailAddress !== null) $params['emailAddress']=$emailAddress;
    if($subjectAltName !== 'IP:0.0.0.0') $params['subjectAltName']=$subjectAltName;

    // Generate a new private (and public) key pair
    $privkey=openssl_pkey_new(Yii::$app->params['pkey_config']);

    // Generate a certificate signing request
    $csr=openssl_csr_new($params, $privkey, array('digest_alg' => 'sha256', 'config'=>\Yii::getAlias('@appconfig').'/CA.cnf', 'encrypt_key'=>false));
    $tmpCAcert=tempnam("/tmp", "echoCTF-OVPN-CA.crt");
    $tmpCAprivkey=tempnam("/tmp", "echoCTF-OVPN-CA.key");
    $CAcert="file://".$tmpCAcert;
    $CAprivkey=array("file://".$tmpCAprivkey, null);
    file_put_contents($tmpCAprivkey, Yii::$app->sys->{'CA.key'});
    file_put_contents($tmpCAcert, Yii::$app->sys->{'CA.crt'});

    // Generate a self-signed cert, valid for 365 days
    $x509=openssl_csr_sign($csr, $CAcert, $CAprivkey, 3650, array('digest_alg'=>'sha256', 'config'=>\Yii::getAlias('@appconfig').'/CA.cnf', 'x509_extensions'=>'server_cert'), 0);

    openssl_csr_export($csr, $csrout);
    openssl_x509_export($x509, $certout, false);
    openssl_x509_export($x509, $crtout);
    openssl_pkey_export($privkey, $pkeyout);

    unlink($tmpCAcert);
    unlink($tmpCAprivkey);

    file_put_contents($commonName.".csr", $csrout);
    file_put_contents($commonName.".txt.crt", $certout);
    file_put_contents($commonName.".crt", $crtout);
    file_put_contents($commonName.".key", $pkeyout);
  }

  /**
   * Generate certificates for a given player email
   * @param string $email Email to generate keys for.
   * @param int $fileout Flag to store cert details on local files
   */
  public function actionGenPlayerCerts($email, $fileout=false) {
    $player=Player::findOne(['email'=>$email]);
    if($player === NULL)
    {
      throw new ConsoleException(Yii::t('app', 'Player email {email} not found.', ['email' => $email]));
    }

    if($player->playerSsl === null)
    {
      $playerSsl=new PlayerSsl;
      $playerSsl->player_id=$player->id;
    }
    else
    {
      $playerSsl=$player->playerSsl;
    }

    $playerSsl->generate();
    $playerSsl->save();

    if((bool) $fileout)
    {
      file_put_contents($player->username.".csr", $playerSsl->csr);
      file_put_contents($player->username.".crt", $playerSsl->crt);
      file_put_contents($player->username.".key", $playerSsl->privkey);
    }
  }

  /* Generate certificates for a all players */
  public function actionGenAllPlayerCerts($fileout=false)
  {
    foreach(Player::find()->all() as $player)
    {
      if($player->playerSsl instanceof PlayerSsl)
      {
        $playerSsl=$player->playerSsl;
      }
      else
      {
        $playerSsl=new PlayerSsl;
        $playerSsl->player_id=$player->id;
      }
      $playerSsl->generate();
      $playerSsl->save();
      if((bool) $fileout)
      {
        file_put_contents($player->username.".csr", $playerSsl->csr);
        file_put_contents($player->username.".crt", $playerSsl->crt);
        file_put_contents($player->username.".key", $playerSsl->privkey);
      }
      sleep(1);
    }
  }

  /*
   * Get CA cert files
   */
  public function actionLoadCa()
  {
    $ocrt=Sysconfig::findOne('CA.crt');
    $okey=Sysconfig::findOne('CA.key');
    $ocsr=Sysconfig::findOne('CA.csr');
    $otxtcrt=Sysconfig::findOne('CA.txt.crt');
    $ocsr->val=file_get_contents("echoCTF-OVPN-CA.csr");
    $ocrt->val=file_get_contents("echoCTF-OVPN-CA.crt");
    $otxtcrt->val=file_get_contents("echoCTF-OVPN-CA.txt.crt");
    $okey->val=file_get_contents("echoCTF-OVPN-CA.key");
    $ocsr->save();
    $ocrt->save();
    $otxtcrt->save();
    $okey->save();
  }
  /*
   * Get CA cert files
   */
  public function actionGetCa($fileout=false)
  {
    $crt=Sysconfig::findOne('CA.crt')->val;
    $key=Sysconfig::findOne('CA.key')->val;
    $csr=Sysconfig::findOne('CA.csr')->val;
    $txtcrt=Sysconfig::findOne('CA.txt.crt')->val;
    if($fileout)
    {
        file_put_contents("echoCTF-OVPN-CA.csr", $csr);
        file_put_contents("echoCTF-OVPN-CA.crt", $crt);
        file_put_contents("echoCTF-OVPN-CA.txt.crt", $txtcrt);
        file_put_contents("echoCTF-OVPN-CA.key", $key);
    }
    else
    {
      printf("echoCTF-OVPN-CA.csr\n%s", $csr);
      printf("echoCTF-OVPN-CA.crt\n%s", $crt);
      printf("echoCTF-OVPN-CA.txt.crt\n%s", $txtcrt);
      printf("echoCTF-OVPN-CA.key\n%s", $key);
    }
  }

  /*
   * Load a given vpn-ta.key file onto the database
   */
  public function actionLoadVpnTa($file='/etc/openvpn/private/vpn-ta.key')
  {
    $vpnta=Sysconfig::findOneNew('vpn-ta.key');
    if(file_exists($file))
    {
      $vpnta->val=file_get_contents($file);
      return $vpnta->save() ? 0 : 1;
    }
    throw new ConsoleException(Yii::t('app', 'File not found: {file}', ['file' => $file]));
  }

  /*
   * Creaet certificates revocation list
   */
  public function actionCreateCrl()
  {
    try
    {
      $cmd=sprintf("openssl ca -gencrl %s -out /etc/openvpn/crl.pem", $this->ssl_params);
      shell_exec($cmd);
    }
    catch(\Exception $e)
    {
      echo $e->getMessage();
      return 1;
    }
    return 0;
  }

  /*
   * Generate CRL based on revoked certificates on the database
   */
  public function actionGenerateCrl($clean=false)
  {
    $CERTS=Crl::find()->all();
    foreach($CERTS as $cert)
    {
      try
      {
        $tmpcrt=tempnam('/tmp', 'crt');
        file_put_contents($tmpcrt, $cert->crt);
        $cmd=sprintf("openssl ca -revoke %s %s ", $tmpcrt, $this->ssl_params);
        shell_exec($cmd);
        unlink($tmpcrt);
        if($clean!==false) $cert->delete();
      }
      catch(\Exception $e)
      {

      }
    }
    if(!empty($CERTS)) $this->actionCreateCrl();
    return 0;
  }

  /*
   * Revoke the certificate of $player_id
   */
  public function actionRevoke($player_id)
  {
    $player=Player::findOne($player_id);
    $tmpcrt=tempnam('/tmp', 'crt');
    file_put_contents($tmpcrt, $player->playerSsl->crt);
    $cmd=sprintf("openssl ca -revoke %s %s ", $tmpcrt, $this->ssl_params);
    shell_exec($cmd);
    $this->actionCreateCrl();
    unlink($tmpcrt);
    echo "Not implemented\n";
  }

}
