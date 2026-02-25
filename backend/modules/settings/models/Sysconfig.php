<?php

namespace app\modules\settings\models;

use Yii;
use yii\base\UserException;
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
      [['id'], 'filter', 'filter' => 'trim'],
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

  public function afterFind()
  {
    parent::afterFind();
    switch ($this->id) {
      case "event_start":
      case "event_end":
      case "registrations_start":
      case "registrations_end":
        if ($this->val == 0 || $this->val == "")
          $this->val = "";
        else
          $this->val = date('Y-m-d H:i:s', $this->val);
        break;
      default:
        break;
    }
  }

  public function beforeSave($insert)
  {
    switch ($this->id) {
      case "event_end":
        $Q = sprintf("DROP EVENT IF EXISTS event_end_notification");
        \Yii::$app->db->createCommand($Q)->execute();
        if (!empty($this->val)) {
          try {
            $Q = sprintf("CREATE EVENT event_end_notification ON SCHEDULE AT '%s' DO BEGIN INSERT INTO `notification`(player_id,category,title,body,archived) SELECT id,'swal:info',memc_get('sysconfig:event_end_notification_title'),memc_get('sysconfig:event_end_notification_body'),0 FROM player WHERE status=10; DO memc_set('event_finished',1); SELECT sleep(1) INTO OUTFILE '/tmp/event_finished';END", $this->val);
            \Yii::$app->db->createCommand($Q)->execute();
          } catch (\Throwable $e) {
            throw new UserException('Failed to create event_end_notification EVENT: '.$e->getMessage());
          }
          $this->val = strtotime($this->val);
        } else {
          \Yii::$app->db->createCommand("DROP EVENT IF EXISTS event_end_notification")->execute();
        }
        break;
      case "event_start":
      case "registrations_start":
      case "registrations_end":
        if (empty($this->val)) {
          $this->val = 0;
        } else {
          $this->val = strtotime($this->val);
        }
        break;
      default:
        break;
    }
    return true;
  }

  public function afterSave($insert, $changedAttributes)
  {
    parent::afterSave($insert, $changedAttributes);

    // $insert is true if it’s a new record
    // $changedAttributes is an array of old values
    if ($this->id === 'stripe_webhookLocalEndpoint' && array_key_exists('val', $changedAttributes)) {
      $oldVal = $changedAttributes['val'];
      $newVal = $this->val;
      if (($u = UrlRoute::findOne(['destination' => 'subscription/default/webhook'])) !== NULL) {
        $u->updateAttributes(['source' => $newVal]);
      }
    }
  }

  public static function findOneNew($id)
  {
    if (($model = self::findOne($id)) !== null)
      return $model;
    $model = new self;
    $model->id = $id;
    return $model;
  }
}
