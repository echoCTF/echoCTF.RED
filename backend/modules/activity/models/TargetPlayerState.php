<?php

namespace app\modules\activity\models;

use Yii;

use app\modules\gameplay\models\Target;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "target_player_state".
 *
 * @property int $id
 * @property int $player_id
 * @property int $player_treasures
 * @property int $player_findings
 * @property int $player_points
 *
 * @property Target $target
 * @property Player $player
 */
class TargetPlayerState extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'target_player_state';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'player_id'], 'required'],
      [['id', 'player_id', 'player_treasures', 'player_findings', 'player_points'], 'integer'],
      [['id', 'player_id'], 'unique', 'targetAttribute' => ['id', 'player_id']],
      [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::className(), 'targetAttribute' => ['id' => 'id']],
      [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['player_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'player_id' => Yii::t('app', 'Player ID'),
      'player_treasures' => Yii::t('app', 'Player Treasures'),
      'player_findings' => Yii::t('app', 'Player Findings'),
      'player_points' => Yii::t('app', 'Player Points'),
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
   * Gets query for [[Player]].
   *
   * @return \yii\db\ActiveQuery|PlayerQuery
   */
  public function getPlayer()
  {
    return $this->hasOne(Player::class, ['id' => 'player_id']);
  }

  /**
   * {@inheritdoc}
   * @return TargetPlayerStateQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new TargetPlayerStateQuery(get_called_class());
  }

  public function sync()
  {
    // select player_findings count
    foreach ($this->target->findings as $f) {
      $fids[] = $f->id;
    }
    foreach ($this->target->treasures as $f) {
      $tids[] = $f->id;
    }
    $pfs = \app\modules\activity\models\PlayerFinding::find()->where(['player_id' => $this->player_id, 'finding_id' => $fids])->all();
    $pts = \app\modules\activity\models\PlayerTreasure::find()->where(['player_id' => $this->player_id, 'treasure_id' => $tids])->all();
    $streamPts = \app\modules\activity\models\Stream::find()->select(['sum(points) as points'])->where(['player_id' => $this->player_id])->andWhere(['model' => 'treasure', 'model_id' => $tids])->column();
    $points = $streamPts[0];
    $streamPts = \app\modules\activity\models\Stream::find()->select(['sum(points) as points'])->where(['player_id' => $this->player_id])->andWhere(['model' => 'finding', 'model_id' => $fids])->column();
    $points += intval($streamPts[0]);
    $this->player_treasures = count($pts);
    $this->player_findings = count($pfs);
    $this->player_points = $points;
    return $this->save();
  }
}
