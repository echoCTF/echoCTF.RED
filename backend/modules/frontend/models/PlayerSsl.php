<?php

namespace app\modules\frontend\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "player_ssl".
 *
 * @property int $player_id
 * @property int $serial
 * @property string $subject
 * @property string $csr
 * @property string $crt
 * @property string $privkey
 * @property string $ts
 *
 * @property Player $player
 */
class PlayerSsl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_ssl';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'ts',
                'updatedAtAttribute' => 'ts',
                'value' => new Expression('NOW()'),
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'subject', 'csr', 'crt', 'privkey'], 'required'],
            [['player_id','serial'], 'integer'],
            [['subject', 'csr', 'crt', 'privkey'], 'string'],
            [['ts','serial'], 'safe'],
            [['player_id','serial'], 'unique'],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'serial' => 'Serial',
            'subject' => 'Subject',
            'csr' => 'Csr',
            'crt' => 'Crt',
            'privkey' => 'Privkey',
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * Generate SSL keys for this model
     */
      public function generate() {
          $player=Player::findOne(['id'=>$this->player_id]);
          $params=Yii::$app->params['dn'];
          $params['countryName']=\Yii::$app->sys->dn_countryName;
          $params['stateOrProvinceName']=\Yii::$app->sys->dn_stateOrProvinceName;
          $params['localityName']=\Yii::$app->sys->dn_localityName;
          $params['organizationName']=\Yii::$app->sys->dn_organizationName;
          $params['organizationalUnitName']=\Yii::$app->sys->dn_organizationalUnitName;
          $params['commonName']=$this->player_id;
          $params['emailAddress']=$player->email;

          // Generate a new private (and public) key pair
          $privkey=openssl_pkey_new(Yii::$app->params['pkey_config']);

          // Generate a certificate signing request
          $csr=openssl_csr_new($params, $privkey, array('digest_alg' => 'sha256', 'config'=>Yii::getAlias('@appconfig').'/CA.cnf', 'encrypt_key'=>false));

          // Generate a self-signed cert, valid for 365 days
          $tmpCAcert=tempnam("/tmp", "echoCTF-OVPN-CA.crt");
          $tmpCAprivkey=tempnam("/tmp", "echoCTF-OVPN-CA.key");
          $CAcert="file://".$tmpCAcert;
          $CAprivkey=array("file://".$tmpCAprivkey, null);
          file_put_contents($tmpCAprivkey, Yii::$app->sys->{'CA.key'});
          file_put_contents($tmpCAcert, Yii::$app->sys->{'CA.crt'});
          $serial=time();
          $x509=openssl_csr_sign($csr, $CAcert, $CAprivkey, 3650, array('digest_alg'=>'sha256', 'config'=>Yii::getAlias('@appconfig').'/CA.cnf', 'x509_extensions'=>'usr_cert'), $serial);
          openssl_csr_export($csr, $csrout);
          openssl_x509_export($x509, $certout, false);
          openssl_x509_export($x509, $crtout);
          openssl_pkey_export($privkey, $pkeyout);
          unlink($tmpCAcert);
          unlink($tmpCAprivkey);

          $this->subject=serialize($params);
          $this->serial=$serial;
          $this->csr=$csrout;
          $this->crt=$crtout;
          $this->privkey=$pkeyout;
          if(!$this->isNewRecord)
            $this->touch('ts');
      }

      public function getSubjectString()
      {
        $subj=unserialize($this->subject);
        $subject_arr=[];
        foreach($subj as $key => $val)
        $subject_arr[]="$key=$val";
      return implode(", ", $subject_arr);
      }

      /**
       * {@inheritdoc}
       * @return PlayerSslQuery the active query used by this AR class.
       */
      public static function find()
      {
          return new PlayerSslQuery(get_called_class());
      }

      public function getTxtCert()
      {
        $cert=openssl_x509_parse($this->crt);
        $l[]="Version: ".$cert['version'];
        $l[]=sprintf("Serial: %s (%s)",$cert['serialNumber'], $cert['serialNumberHex']);
        $l[]=sprintf("Subject: %s",$cert['name']);
        $l[]=sprintf("Valid: %s - %s",date_create_from_format('ymdHise', $cert['validFrom'])->format('c'),date_create_from_format('ymdHise', $cert['validTo'])->format('c'));
        return implode("\n",$l);
      }
}
