<?php

namespace app\modules\settings\models;

use Yii;

/**
 * This is the model class for table "sysconfig".
 *
 * @property string $id
 * @property string $val
 */
class Sysconfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sysconfig';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['val'], 'string'],
            [['id'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'val' => 'Val',
        ];
    }

    public function afterFind(){
        parent::afterFind();
        switch($this->id){
            case "event_start":
            case "event_end":
            case "registrations_start":
            case "registrations_end":
              if($this->val==0)
                $this->val="";
              else
                $this->val=\Yii::$app->formatter->asDate($this->val,'php:Y-m-d H:i:s');
              break;
            default:
              break;
        }
    }
    public function beforeSave($insert){
        switch($this->id){
            case "event_end":
              if(empty($this->val))
              {
                $this->val=0;
                $Q=sprintf("DROP EVENT IF EXISTS event_end_notification");
                \Yii::$app->db->createCommand($Q)->execute();
              }
              else
              {
                $Q=sprintf("CREATE EVENT event_end_notification ON SCHEDULE AT '%s' DO INSERT INTO `notification`(player_id,category,title,body,archived) SELECT id,'swal:info',memc_get('sysconfig:event_end_notification_title'),memc_get('sysconfig:event_end_notification_body'),0 FROM player WHERE status=10",$this->val);
                \Yii::$app->db->createCommand($Q)->execute();
                $this->val=\Yii::$app->formatter->asTimestamp($this->val);
              }
              break;
            case "event_start":
            case "registrations_start":
            case "registrations_end":
              if(empty($this->val))
              {
                $this->val=0;
              }
              else
              {
                $this->val=\Yii::$app->formatter->asTimestamp($this->val);
              }
              break;
            default:
              break;
        }
        return true;
    }

    public static function findOneNew($id)
    {
      if(($model=self::findOne($id))!==null)
        return $model;
      $model=new self;
      $model->id=$id;
      return $model;
    }
}
