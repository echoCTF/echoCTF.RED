<?php

namespace app\modules\team\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\models\Player;

/**
 * This is the model class for table "team_player".
 *
 * @property int $id
 * @property int $team_id
 * @property int $player_id
 * @property int $approved
 * @property string $ts
 *
 * @property Team $team
 * @property Player $player
 */
class TeamPlayer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team_player';
    }

    public function behaviors()
    {
      return [
          [
              'class' => TimestampBehavior::class,
              'createdAtAttribute' => 'ts',
              'updatedAtAttribute' => 'ts',
              'value' => new Expression('NOW()'),
          ],
      ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id', 'player_id'], 'required'],
            [['team_id', 'player_id', 'approved'], 'integer'],
            [['player_id'], 'unique'],
            [['team_id', 'player_id'], 'unique', 'targetAttribute' => ['team_id', 'player_id']],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => Team::class, 'targetAttribute' => ['team_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'team_id' => 'Team ID',
            'player_id' => 'Player ID',
            'approved' => 'Approved',
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'team_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }
    private function sendNotification($id,$msg="")
    {
        $n=new \app\models\Notification;
        $n->player_id=$id;
        $n->archived=0;
        $n->body=$n->title=$msg;
        return $n->save();
    }

    public function notifyJoinOwner()
    {
      $msg=sprintf(\Yii::t('app','Hi there, [%s] just joined your team. Go to your team page and approve the player.'),$this->player->username);
      return $this->sendNotification($this->team->owner_id,$msg);
    }
    public function notifyPartOwner()
    {
      $msg=sprintf(\Yii::t('app','Hi there, [%s] just left your team.'),$this->player->username);
      return $this->sendNotification($this->team->owner_id,$msg);
    }

    public function notifyRejectPlayer()
    {
      // Don't notify the players for deleting their own team
      if($this->player_id===$this->team->owner_id) return;
      $msg=\Yii::t('app','Hi there, your team membership got rejected. Find another team to join.');
      return $this->sendNotification($this->player_id,$msg);
    }
    public function notifyApprovePlayer()
    {
      $msg=\Yii::t('app','Hi there, your team membership got approved.');
      return $this->sendNotification($this->player_id,$msg);
    }

}
