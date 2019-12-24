<?php

namespace app\modules\activity\models;

use Yii;
use app\modules\frontend\models\Player;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "stream".
 *
 * @property string $id
 * @property int $player_id
 * @property string $model
 * @property int $model_id
 * @property int $points
 * @property string $title
 * @property string $message
 * @property string $pubtitle
 * @property string $pubmessage
 * @property string $ts
 *
 * @property Player $player
 */
class Stream extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stream';
    }
    public function behaviors()
    {
      return [
          [
              'class' => TimestampBehavior::className(),
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
            [['player_id', 'model_id', 'points'], 'integer'],
            [['player_id','title', 'message', 'pubtitle', 'pubmessage'], 'required'],
            [['message', 'pubmessage'], 'string'],
            [['points'],'default', 'value'=>0],
            [['ts'], 'safe'],
            [['model', 'title', 'pubtitle'], 'string', 'max' => 255],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::className(), 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'player_id' => 'Player ID',
            'model' => 'Model',
            'model_id' => 'Model ID',
            'points' => 'Points',
            'title' => 'Title',
            'message' => 'Message',
            'pubtitle' => 'Pubtitle',
            'pubmessage' => 'Pubmessage',
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::className(), ['id' => 'player_id']);
    }

    public function getFormatted($pub=true)
    {
      $icon=array(
        'headshot'=>\yii\helpers\Html::img('/images/treasure.png',['alt'=>'Treasure','width'=>'28px']),
        'treasure'=>\yii\helpers\Html::img('/images/treasure.png',['alt'=>'Treasure','width'=>'28px']),
        'finding'=>\yii\helpers\Html::img('/images/finding.png',['alt'=>'Finding','width'=>'28px']),
        'question'=>\yii\helpers\Html::img('/images/question.png',['alt'=>'Question','width'=>'28px']),
        'team_player'=>\yii\helpers\Html::img('/images/team_player.png',['alt'=>'Team Player','width'=>'28px']),
        'user'=>\yii\helpers\Html::img('/images/user.png',['alt'=>'User','width'=>'28px']),
        'report'=>\yii\helpers\Html::img('/images/report.png',['alt'=>'Report','width'=>'28px']),
        'badge'=>\yii\helpers\Html::img('/images/badge.png',['alt'=>'Badge','width'=>'28px']),
      );
      switch($this->model) {
      	case 'user':
      		$message=sprintf("%s <b>%s</b> %s", $icon[$this->model],$this->player->profile->link,$pub ? $this->pubtitle : $this->title);
      		break;
      	case 'team_player':
      		$message=sprintf("%s Team <b>%s</b> welcomes their newest member <b>%s</b> ", $icon[$this->model],$this->player->teamMembership ? $this->player->teamMembership->name: "N/A", $this->player->profile->link);
      		break;
        case 'report':
          if(Yii::app()->sys->teams)
            $message=sprintf("%s <b>%s</b> from team <b>[%s]</b> Reported <b>%s</b>",$icon[$this->model],$this->player->profile->link, $this->player->teamMembership ? $this->player->teamMembership->name: "N/A",$pub ? $this->pubtitle : $this->title);
          else
            $message=sprintf("%s <b>%s</b> Reported <b>%s</b>",$icon[$this->model],$this->player->profile->link,$pub ? $this->pubtitle : $this->title);
          break;
        case 'question':
            $message=sprintf("%s <b>%s</b> Answered a question from <b>%s</b>",$icon[$this->model],$this->player->profile->link, \app\modules\gameplay\models\Question::findOne($this->model_id )->challenge->name);
          break;
        case 'treasure':
        case 'finding':
        default:
            $message= sprintf("%s <b>%s</b> %s", $icon[$this->model],$this->player->profile->link, $pub ? $this->pubtitle : $this->title);
      }
      if($this->points!=0)
        $message=sprintf("%s for %d points", $message, $this->points);
      return $message;
    }

}
