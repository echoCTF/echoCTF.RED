<?php

namespace app\modules\speedprogramming\models;

use Yii;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\gameplay\models\Target;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\modules\activity\models\Stream;
/**
 * This is the model class for table "speed_solution".
 *
 * @property int $id
 * @property int $player_id
 * @property string|null $language
 * @property resource|null $sourcecode
 * @property string|null $status
 * @property int|null $points
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Player $player
 */
class SpeedSolution extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'speed_solution';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
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
            [['player_id','points'], 'filter','filter'=>'intval'],
            [['player_id'], 'required'],
            [['player_id', 'points'], 'integer'],
            [['sourcecode'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
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
            'language' => 'Language',
            'sourcecode' => 'Sourcecode',
            'status' => 'Status',
            'points' => 'Points',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
     * Gets query for [[Player]].
     *
     * @return \yii\db\ActiveQuery|PlayerQuery
     */
    public function getProblem()
    {
        return $this->hasOne(SpeedProblem::class, ['id' => 'problem_id']);
    }

    /**
     * {@inheritdoc}
     * @return SpeedSolutionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SpeedSolutionQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
      parent::afterSave($insert, $changedAttributes);
      if((isset($changedAttributes['status']) || isset($changedAttributes['points']))
      && (($this->status==='approved' && $this->points>0) || $this->status==='rejected'))
      {
        $stream=new Stream();
        $stream->player_id=$this->player_id;
        $stream->model='solution';
        $stream->model_id=$this->problem_id;
        $stream->points=$this->points;
        $stream->title='submission for <code>'.$this->problem->name.'</code> got '.$this->status;
        $stream->message=$stream->pubmessage=$stream->pubtitle=$stream->title;
        return $stream->save();
      }
    }
    public static function getLanguages()
    {
      return [
        'c'=>'C',
        'cpp'=>'C++',
        'cs'=>'C#',
        'py2'=>'Python 2.x',
        'py3'=>'Python 3.x',
        'php7'=>'PHP 7.x',
        'java'=>'Java'
      ];
    }
    public static function getStatuses()
    {
      return ['pending'=>'pending','approved'=>'approved','rejected'=>'rejected','invalid'=>'invalid'];
    }

    public function approve()
    {
      $this->status='approved';
      return $this->save();
    }
    public function reject()
    {
      $this->status='rejected';
      $this->points=0;
      return $this->save();
    }

}
