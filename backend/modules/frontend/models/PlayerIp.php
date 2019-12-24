<?php

namespace app\modules\frontend\models;

use Yii;

/**
 * This is the model class for table "player_ip".
 *
 * @property int $id
 * @property int $player_id
 * @property int $ip
 *
 * @property Player $player
 */
class PlayerIp extends \yii\db\ActiveRecord
{
  public $ipoctet;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_ip';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'ipoctet'], 'required'],
            [['player_id', 'ip'], 'integer'],
            [['ipoctet'], 'ip'],
            [['ip'], 'unique'],
            [['ip', 'player_id'], 'unique', 'targetAttribute' => ['ip', 'player_id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'player_id' => 'Player ID',
            'ip' => 'Ip',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::className(), ['id' => 'player_id']);
    }

    public function afterFind(){
      parent::afterFind();
      $this->ipoctet=long2ip($this->ip);
    }


    public function beforeSave($insert)
    {
      if (parent::beforeSave($insert)) {
          $this->ip = ip2long($this->ipoctet);
          return true;
      } else {
          return false;
      }
    }


}
