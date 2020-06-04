<?php

namespace app\models;

use Yii;

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
}
