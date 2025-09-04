<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\gameplay\models\Target;
use app\modules\gameplay\models\Treasure;
use app\modules\gameplay\models\Finding;
use yii\base\UserException;

/**
 * This is the model class for table "headshot".
 *
 * @property int $player_id
 * @property int $target_id
 * @property int $timer
 * @property string|null $created_at
 *
 * @property Player $player
 * @property Target $target
 */
class Headshot extends \yii\db\ActiveRecord
{
  public $created_at_ago;
  private $ratings = [
    -1 => "unrated",
    0 => "beginner",
    1 => "basic",
    2 => "intermediate",
    3 => "advanced",
    4 => "expert",
    5 => "guru",
    6 => "insane"
  ];

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'headshot';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['player_id', 'target_id'], 'required'],
      [['player_id', 'target_id', 'timer', 'rating'], 'integer'],
      [['first'], 'boolean'],
      [['first'], 'default', 'value' => 0],
      [['created_at', 'rating', 'timer'], 'safe'],
      [['player_id', 'target_id'], 'unique', 'targetAttribute' => ['player_id', 'target_id']],
      [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
      [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'player_id' => Yii::t('app', 'Player ID'),
      'target_id' => Yii::t('app', 'Target ID'),
      'created_at' => Yii::t('app', 'Created At'),
    ];
  }

  public function zero()
  {
    $db=\Yii::$app->db;
    try {
      $this->updateAttributes(['timer' => 0]);
      $treasure_ids = Treasure::find()->select('id')->where(['target_id' => $this->target_id])->column();
      $finding_ids = Finding::find()->select('id')->where(['target_id' => $this->target_id])->column();
      $db->createCommand()
        ->update(
          'stream',
          [
            'points' => 0,
            'ts' => new \yii\db\Expression('ts'),
          ],
          [
            'and',
            ['player_id' => $this->player_id],
            [
              'or',
              ['and', ['model' => 'treasure'], ['in', 'model_id', $treasure_ids]],
              ['and', ['model' => 'finding'], ['in', 'model_id', $finding_ids]],
              ['and', ['model' => 'headhost'], ['model_id' => $this->target_id]],
            ],
          ]
        )->execute();
      // Forcefully activate writeups
      $db->createCommand('SET @TRIGGER_CHECKS=false; INSERT IGNORE INTO player_target_help (player_id,target_id,created_at) VALUES (:player_id,:target_id,:created_at); SET @TRIGGER_CHECKS=true; ', [':player_id' => $this->player_id, ':target_id' => $this->target_id, ':created_at' => new \yii\db\Expression('NOW()')])->execute();

      // Update the player points
      $db->createCommand('UPDATE player_score SET points=(SELECT SUM(points) FROM stream WHERE player_id=:player_id) WHERE player_id=:player_id', [':player_id' => $this->player_id])->execute();
      // If player is member of team re-populate the team stream
      if ($this->player->teamPlayer)
        $db->createCommand('CALL repopulate_team_stream(:team_id)', [':team_id' => $this->player->teamPlayer->team_id])->execute();
    } catch (\Exception $e) {
      throw new UserException($e->getMessage());
    }
  }
  /**
   * @return \yii\db\ActiveQuery
   */
  public function getPlayer()
  {
    return $this->hasOne(Player::class, ['id' => 'player_id']);
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getTarget()
  {
    return $this->hasOne(Target::class, ['id' => 'target_id']);
  }

  /**
   * @return HeadshotQuery the active query used by this AR class.
   */
  public static function find()
  {
    return new HeadshotQuery(get_called_class());
  }

  public function getRatingString()
  {
    return $this->ratings[$this->rating];
  }

  public function getRatings()
  {
    return $this->ratings;
  }
}
