<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
/**
 * This is the model class for table "sessions".
 *
 * @property string $id
 * @property int $expire
 * @property string $data
 * @property int $player_id
 * @property string $ip
 * @property string $ts
 * @property array $decodedData
 * @property string $decodedDataAsString
 *
 * @property Player $player
 */
class Sessions extends \yii\db\ActiveRecord
{
  public $ipoctet;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sessions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['expire', 'player_id', 'ip'], 'integer'],
            [['data'], 'string'],
            [['ipoctet'], 'ip'],
            [['ts'], 'safe'],
            [['id'], 'string', 'max' => 32],
            [['id'], 'unique'],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'sessionID',
            'expire' => 'Expire',
            'data' => 'Data',
            'player_id' => 'Player ID',
            'ip' => 'IP',
            'ipoctet'=>'IP',
            'ts' => 'TS',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    public function afterFind() {
      parent::afterFind();
      $this->ipoctet=long2ip($this->ip);
    }

    public function beforeSave($insert)
    {
      if(parent::beforeSave($insert))
      {
          $this->ip=ip2long($this->ipoctet);
          return true;
      }
      else
      {
          return false;
      }
    }

    public function getDecodedData()
    {
      if(trim($this->data) == "") return "";
      $r=array();
      $str=$this->data;
      $sessid=null;
      while($i=strpos($str, '|'))
      {
          $k=substr($str, 0, $i);
          $sessid=explode("__", $k)[0];
          $v=unserialize(substr($str, 1 + $i));
          $str=substr($str, 1 + $i + strlen(serialize($v)));
          $r[$k]=$v;
      }
      $r['PHPSESSION_ID']=$sessid;
      return $r;
    }

    public function getDecodedDataAsString()
    {
      if(trim($this->data) == "") return "";
      $r=array();
      foreach($this->decodedData as $key=>$val)
      {
        if(!is_array($val))
          $r[]=sprintf("%s => %s", $key, $val);
      }
      return '<pre>'.implode("\n", $r).'</pre>';
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        $key="memc.sess.$this->id";
        Yii::$app->cache->memcache->delete($key);
        return true;
    }
}
