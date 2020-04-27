<?php

namespace app\modules\activity\models;

use Yii;
use yii\helpers\Html;
use app\modules\frontend\models\Player;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\Expression;
use app\modules\gameplay\models\Badge;
use app\modules\gameplay\models\Target;

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
 * @property string $icon
 *
 * @property Player $player
 */
class Stream extends \yii\db\ActiveRecord
{
  const MODEL_ICONS=[
    'headshot'=>'<i class="fas fa-skull" style="font-size: 1.5em;"></i>',
    'treasure'=>'<i class="fas fa-flag" style="font-size: 1.5em;"></i>',
    'finding'=>'<i class="fas fa-fingerprint" style="font-size: 1.5em;"></i>',
    'question'=>'<i class="fas fa-puzzle-piece" style="font-size: 1.5em;"></i>',
    'team_player'=>'<i class="fas fa-users" style="font-size: 1.5em;"></i>',
    'user'=>'<i class="fas fa-user-ninja" style="font-size: 1.5em;"></i>',
    'report'=>'<i class="fas fa-clipboard-list" style="font-size: 1.5em;"></i>',
    'badge'=>'<i class="fas fa-trophy" style="font-size: 1.5em;"></i>',
  ];


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
        'typecast' => [
          'class' => AttributeTypecastBehavior::class,
          'attributeTypes' => [
            'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
            'points' => AttributeTypecastBehavior::TYPE_FLOAT,
          ],
          'typecastAfterValidate' => true,
          'typecastBeforeSave' => false,
          'typecastAfterFind' => true,
        ],
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
            [['player_id', 'model_id', 'points'], 'integer'],
            [['player_id','title', 'message', 'pubtitle', 'pubmessage'], 'required'],
            [['message', 'pubmessage'], 'string'],
            [['points'],'default', 'value'=>0],
            [['ts'], 'safe'],
            [['model', 'title', 'pubtitle'], 'string', 'max' => 255],
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
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    public function getIcon()
    {
      return self::MODEL_ICONS[$this->model];
    }

    public function prefix()
    {
      return sprintf("%s <b>%s</b>",$this->icon,$this->player->profile->link);
    }

    public function Title(bool $pub=true)
    {
      return $pub ? $this->pubtitle : $this->title;
    }

    public function getFormatted($pub=true)
    {
      switch($this->model)
      {
        case 'badge':
          $message=sprintf("%s got the badge [<code>%s</code>]",$this->prefix(),Badge::findOne(['id'=>$this->model_id])->name);
          break;
        case 'headshot':
          $headshot=Headshot::findOne(['target_id'=>$this->model_id,'player_id'=>$this->player_id]);
          if($headshot->timer>0)
            $message=sprintf("%s managed to headshot [<code>%s</code>] in <i class='fas fa-stopwatch'></i> %s minutes",$this->prefix(),Html::a(Target::findOne(['id'=>$this->model_id])->fqdn,['/target/default/index','id'=>$this->model_id]),number_format($headshot->timer/60));
          else
            $message=sprintf("%s managed to headshot [<code>%s</code>]",$this->prefix(),Html::a(Target::findOne(['id'=>$this->model_id])->fqdn,['/target/default/index','id'=>$this->model_id]));
          break;
  //      case 'team_player':
  //        $message=sprintf("%s Team <b>%s</b> welcomes their newest member <b>%s</b> ", $this->icon,$this->player->teamMembership ? $this->player->teamMembership->name: "N/A", $this->player->profile->link);
  //        break;
        case 'report':
  //        if(Yii::$app->sys->teams)
  //          $message=sprintf("%s from team <b>[%s]</b> Reported <b>%s</b>",$this->prefix(), $this->player->teamMembership ? $this->player->teamMembership->name: "N/A",$this->Title($pub));
  //        else
          $message=sprintf("%s Reported <b>%s</b>",$this->prefix(),$this->Title($pub));
          break;
        case 'question':
          $message=sprintf("%s Answered a question from <b>%s</b>",$this->prefix(), \app\modules\gameplay\models\Question::findOne($this->model_id )->challenge->name);
          break;
        case 'treasure':
        case 'finding':
        case 'user':
        default:
          $message=sprintf("%s %s",$this->prefix(), $this->Title($pub));
      }

      if($this->points!=0)
        $message=sprintf("%s for %d points", $message, $this->points);

      return $message;
    }

}
