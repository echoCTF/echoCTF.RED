<?php

namespace app\modules\activity\models;

use Yii;

/**
 * This is the model class for table "arpdat".
 *
 * @property int $ip The IP of the target
 * @property string $mac The mac associated with this IP
 */



class Arpdat extends \yii\db\ActiveRecord
{
    public $ipoctet;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'arpdat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mac', 'ipoctet'], 'required'],
            [['ip'], 'integer'],
            [['ipoctet'], 'ip'],
            [['mac'], 'string', 'max' => 30],
            [['ip', 'mac'], 'unique', 'targetAttribute' => ['ip', 'mac']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ip' => 'Ip',
            'mac' => 'Mac',
        ];
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
