<?php

namespace app\modules\infrastructure\models;

use Yii;

/**
 * This is the model class for table "server".
 *
 * @property int $id
 * @property string $name
 * @property int $ip
 * @property string|null $description
 * @property string $service
 * @property string $connstr
 */
class Server extends \yii\db\ActiveRecord
{
  public $ipoctet;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'server';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'ipoctet', 'connstr','network'], 'required'],
            [['ipoctet'], 'ip'],
            [['description', 'service','provider_id'], 'string'],
            ['service','default','value'=>'docker'],
            [['name','network'], 'string', 'max' => 32],
            [['connstr'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'ip' => Yii::t('app', 'Ip'),
            'ipoctet' => Yii::t('app', 'IP'),
            'description' => Yii::t('app', 'Description'),
            'service' => Yii::t('app', 'Service'),
            'connstr' => Yii::t('app', 'Connstr'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return ServerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ServerQuery(get_called_class());
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
