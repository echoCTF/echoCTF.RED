<?php

namespace app\modules\infrastructure\models;

use Yii;
use app\modules\gameplay\models\Target;

/**
 * This is the model class for table "target_state".
 *
 * @property int $id
 * @property int $total_headshots
 * @property int $total_findings
 * @property int $total_treasures
 * @property int $player_rating
 * @property int $timer_avg
 * @property int $total_writeups
 * @property int $approved_writeups
 * @property int $finding_points
 * @property int $treasure_points
 * @property int $total_points
 * @property int $on_network
 * @property int $on_ondemand
 * @property int $ondemand_state
 *
 * @property Target $target
 */
class TargetState extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'target_state';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id'], 'required'],
      [['id', 'total_headshots', 'total_findings', 'total_treasures', 'player_rating', 'timer_avg', 'total_writeups', 'approved_writeups', 'finding_points', 'treasure_points', 'total_points', 'on_network', 'on_ondemand', 'ondemand_state'], 'integer'],
      [['id'], 'unique'],
      [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'total_headshots' => Yii::t('app', 'Headshots'),
      'total_findings' => Yii::t('app', 'Findings'),
      'total_treasures' => Yii::t('app', 'Treasures'),
      'player_rating' => Yii::t('app', 'Rating'),
      'timer_avg' => Yii::t('app', 'Timer Avg'),
      'total_writeups' => Yii::t('app', 'Total Writeups'),
      'approved_writeups' => Yii::t('app', 'Approved Writeups'),
      'finding_points' => Yii::t('app', 'Finding pts'),
      'treasure_points' => Yii::t('app', 'Treasure pts'),
      'total_points' => Yii::t('app', 'Total pts'),
      'on_network' => Yii::t('app', 'Network'),
      'on_ondemand' => Yii::t('app', 'OnDemand'),
      'ondemand_state' => Yii::t('app', 'State'),
    ];
  }

  /**
   * Gets query for [[Id0]].
   *
   * @return \yii\db\ActiveQuery|TargetQuery
   */
  public function getTarget()
  {
    return $this->hasOne(Target::class, ['id' => 'id']);
  }

  /**
   * {@inheritdoc}
   * @return TargetStateQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new TargetStateQuery(get_called_class());
  }

  public function sync()
  {
    // select player_findings count
    $fpts=$tpts=0;
    foreach ($this->target->findings as $f) {
      $fpts += $f->points;
    }
    foreach ($this->target->treasures as $f) {
      $tpts += $f->points;
    }
    $this->total_headshots=count($this->target->headshots);
    $this->total_findings=count($this->target->findings);
    $this->total_treasures=count($this->target->treasures);
    $this->player_rating=(int) \app\modules\activity\models\Headshot::find()->select(['avg(rating) as rating'])->where(['target_id'=>$this->id])->andWhere(['>','rating',-1])->one()->rating;
    $this->timer_avg=\app\modules\activity\models\Headshot::find()->select(['round(avg(timer)) as timer'])->where(['target_id'=>$this->id])->andwhere(['>','timer',0])->one()->timer;
    $this->total_writeups=count($this->target->writeups);
    $this->approved_writeups=count($this->target->approvedWriteups);
    $this->finding_points=$fpts;
    $this->treasure_points=$tpts;
    $this->total_points=$fpts+$tpts;
    $this->on_network=intval(\app\modules\gameplay\models\NetworkTarget::find()->where(['target_id'=>$this->id])->exists());
    $this->on_ondemand=intval(\app\modules\gameplay\models\TargetOndemand::find()->where(['target_id'=>$this->id])->exists());
    if ($this->on_ondemand===1) {
      $this->ondemand_state=\app\modules\gameplay\models\TargetOndemand::find()->where(['target_id'=>$this->id])->one()->state;
    } else {
      $this->ondemand_state=-1;
    }
    return $this->save();
  }
}
