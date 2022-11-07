<?php

namespace app\modules\smartcity\models;

use Yii;
use app\modules\gameplay\models\Treasure;

/**
 * This is the model class for table "treasure_action".
 *
 * @property int $id
 * @property int $treasure_id
 * @property int $ip
 * @property int $port
 * @property string $command
 * @property int $weight
 *
 * @property Treasure $treasure
 */
class TreasureAction extends \yii\db\ActiveRecord
{
  public $ipoctet;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'treasure_action';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ipoctet'], 'ip'],
            [['treasure_id'], 'required'],
            [['treasure_id', 'ip', 'port', 'weight'], 'integer'],
            [['command'], 'string'],
            [['treasure_id'], 'exist', 'skipOnError' => true, 'targetClass' => Treasure::class, 'targetAttribute' => ['treasure_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'treasure_id' => 'Treasure ID',
            'ip' => 'IP',
            'port' => 'Port',
            'command' => 'Command',
            'weight' => 'Weight',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTreasure()
    {
        return $this->hasOne(Treasure::class, ['id' => 'treasure_id']);
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
