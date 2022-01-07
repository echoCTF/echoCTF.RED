<?php

namespace app\modules\activity\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "player_rank".
 *
 * @property int $id
 * @property int $player_id
 *
 * @property Team $player
 */
class TeamRank extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team_rank';
    }
    public function behaviors()
    {
      return [
        'typecast' => [
            'class' => AttributeTypecastBehavior::class,
            'attributeTypes' => [
                'id' => AttributeTypecastBehavior::TYPE_INTEGER,
                'team_id' => AttributeTypecastBehavior::TYPE_INTEGER,
            ],
            'typecastAfterValidate' => true,
            'typecastBeforeSave' => false,
            'typecastAfterFind' => true,
        ],
      ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'team_id'], 'required'],
            [['id', 'team_id'], 'integer'],
            [['team_id'], 'unique'],
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
            'team_id' => 'team ID',
        ];
    }
  function getOrdinalPlace() {
    if($this->id===null) return '?th';
    if(!in_array(($this->id % 100), array(11, 12, 13)))
    {
      switch($this->id % 10) {
        // Handle 1st, 2nd, 3rd
        case 1:  return $this->id.'st';
        case 2:  return $this->id.'nd';
        case 3:  return $this->id.'rd';
      }
    }
    return $this->id.'th';
  }
  public function getScore()
  {
    return $this->hasOne(TeamScore::class, ['team_id' => 'team_id']);
  }
  public function getTeam()
  {
    return $this->hasOne(Team::class, ['id' => 'team_id']);
  }
  public function getUsername()
  {
    return $this->player->username;
  }

  public function getAvatar()
  {
    return '/images/avatars/'.$this->player->profile->avatar;
  }
  public static function find()
  {
      return new TeamRankQuery(get_called_class());
  }

}
