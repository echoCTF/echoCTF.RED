<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Html;
use yii\behaviors\AttributeTypecastBehavior;
use app\modules\target\models\Target;
use app\modules\game\models\Badge;
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
 * @property string $prefix
 * @property string $suffix
 *
 * @property Player $player
 */
class Stream extends \yii\db\ActiveRecord
{
  const MODEL_ICONS=[
    'headshot'=>'<i class="fas fa-skull" style="font-size: 1.5em;"></i>',
    'challenge_solve'=>'<i class="fas fa-tasks" style="font-size: 1.5em;"></i>',
    'treasure'=>'<i class="fas fa-flag" style="font-size: 1.5em;"></i>',
    'finding'=>'<i class="fas fa-fingerprint" style="font-size: 1.5em;"></i>',
    'question'=>'<i class="fas fa-list-ul" style="font-size: 1.5em;"></i>',
    'team_player'=>'<i class="fas fa-users" style="font-size: 1.5em;"></i>',
    'user'=>'<i class="fas fa-user-ninja" style="font-size: 1.5em;"></i>',
    'report'=>'<i class="fas fa-clipboard-list" style="font-size: 1.5em;"></i>',
    'badge'=>'<i class="fas fa-trophy" style="font-size: 1.5em;"></i>',
  ];

  public $ts_ago;

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
        [['player_id', 'title', 'message', 'pubtitle', 'pubmessage'], 'required'],
        [['message', 'pubmessage'], 'string'],
        [['points'], 'default', 'value' => 0],
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

  public function getPrefix()
  {
    return sprintf("%s <b>%s</b>", $this->icon, $this->player->profile->link);
  }

  public function Title(bool $pub=true)
  {
    return $pub ? $this->pubtitle : $this->title;
  }

  public function getFormatted(bool $pub=true)
  {
    if(!Yii::$app->user->isGuest && Yii::$app->user->id === $this->player_id)
      $pub=false;

    switch($this->model)
    {
      case 'badge':
        $message=sprintf("%s got the badge [<code>%s</code>]%s", $this->prefix, Badge::findOne(['id'=>$this->model_id])->name, $this->suffix);
        break;
      case 'headshot':
        $message=$this->headshotMessage;
        break;
//      case 'team_player':
//        $message=sprintf("%s Team <b>%s</b> welcomes their newest member <b>%s</b> ", $this->icon,$this->player->teamMembership ? $this->player->teamMembership->name: "N/A", $this->player->profile->link);
//        break;
      case 'report':
//        if(Yii::$app->sys->teams)
//          $message=sprintf("%s from team <b>[%s]</b> Reported <b>%s</b>",$this->prefix, $this->player->teamMembership ? $this->player->teamMembership->name: "N/A",$this->Title($pub));
//        else
        $message=sprintf("%s Reported <b>%s</b>%s", $this->prefix, $this->Title($pub), $this->suffix);
        break;
      case 'question':
        $message=sprintf("%s Answered the question of <b>%s</b> [%s] %s", $this->prefix, \app\modules\challenge\models\Question::findOne($this->model_id)->challenge->name,\app\modules\challenge\models\Question::findOne($this->model_id)->name, $this->suffix);
        break;
      default:
        $message=sprintf("%s %s%s", $this->prefix, $this->Title($pub), $this->suffix);
    }

    return $message;
  }

  public function getSuffix()
  {
    if($this->points != 0)
      return sprintf(" for %d points", $this->points);
    return "";
  }

  public function getHeadshotMessage()
  {
    $headshot=\app\modules\game\models\Headshot::findOne(['target_id'=>$this->model_id, 'player_id'=>$this->player_id]);
    if($headshot->target->timer===0 || $headshot->timer===0)
      return sprintf("%s managed to headshot [<code>%s</code>]%s", $this->prefix, Html::a(Target::findOne(['id'=>$this->model_id])->fqdn, ['/target/default/index', 'id'=>$this->model_id]), $this->suffix);

    return sprintf("%s managed to headshot [<code>%s</code>] in <i data-toggle='tooltip' title='%s' class='fas fa-stopwatch'></i> %s minutes%s", $this->prefix, Html::a(Target::findOne(['id'=>$this->model_id])->fqdn, ['/target/default/index', 'id'=>$this->model_id]), Yii::$app->formatter->asDuration($headshot->timer), number_format($headshot->timer / 60), $this->suffix);
  }
}
