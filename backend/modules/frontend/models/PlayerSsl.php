<?php

namespace app\modules\frontend\models;

use Yii;

/**
 * This is the model class for table "player_ssl".
 *
 * @property int $player_id
 * @property string $subject
 * @property string $csr
 * @property string $crt
 * @property string $txtcrt
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'subject', 'csr', 'crt', 'txtcrt', 'privkey'], 'required'],
            [['player_id'], 'integer'],
            [['subject', 'csr', 'crt', 'txtcrt', 'privkey'], 'string'],
            [['ts'], 'safe'],
            [['player_id'], 'unique'],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'subject' => 'Subject',
            'csr' => 'Csr',
            'crt' => 'Crt',
            'txtcrt' => 'Txtcrt',
            'privkey' => 'Privkey',
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::className(), ['id' => 'player_id']);
    }

    /**
     * Generate SSL keys for this model
     */
     public function generate() {
         $player=$this->Player;
         Yii::$app->params['dn']['commonName'] = $player->username;
         Yii::$app->params['dn']['emailAddress']=$player->email;
         // Generate a new private (and public) key pair
         $privkey = openssl_pkey_new(Yii::$app->params['pkey_config']);

         // Generate a certificate signing request
         $csr = openssl_csr_new(Yii::$app->params['dn'], $privkey, array('digest_alg' => 'sha256', 'config'=>__DIR__ . '/../../../config/CA.cnf','encrypt_key'=>false));

         // Generate a self-signed cert, valid for 365 days
         $tmpCAcert=tempnam("/tmp", "echoCTF-OVPN-CA.crt");
         $tmpCAprivkey=tempnam("/tmp", "echoCTF-OVPN-CA.key");
         $CAcert = "file://".$tmpCAcert;
         $CAprivkey = array("file:///tmp".$tmpCAprivkey,null);
         file_put_contents($tmpCAprivkey,Sysconfig::findOne('CA.key')->val);
         file_put_contents($tmpCAcert,Sysconfig::findOne('CA.crt')->val);
         $x509 = openssl_csr_sign($csr, $CAcert, $CAprivkey, 365, array('digest_alg'=>'sha256','config'=>__DIR__ . '/../../../config/CA.cnf','x509_extensions'=>'usr_cert'), time() );
         openssl_csr_export($csr, $csrout);
         openssl_x509_export($x509, $certout,false);
         openssl_x509_export($x509, $crtout);
         openssl_pkey_export($privkey, $pkeyout);
         unlink($tmpCAcert);
         unlink($tmpCAprivkey);

         $this->subject=serialize(Yii::$app->params['dn']);
         $this->csr=$csrout;
         $this->crt=$crtout;
         $this->txtcrt=$certout;
         $this->privkey=$pkeyout;
     }

     public function getSubjectString()
     {
       $subj=unserialize($this->subject);
       foreach($subj as $key => $val)
        $subject_arr[]="$key=$val";
      return implode(", ",$subject_arr);
     }
}
