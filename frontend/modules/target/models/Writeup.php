<?php

namespace app\modules\target\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\AttributeTypecastBehavior;
use yii\base\NotSupportedException;
use app\models\Player;
use app\modules\game\models\Headshot;
use app\modules\game\models\WriteupRating;
/**
 * This is the model class for table "writeup".
 *
 * @property int $id
 * @property int $player_id
 * @property int $target_id
 * @property resource|null $content
 * @property int|null $approved
 * @property string|null $status
 * @property string|null $formatter
 * @property string $language_id
 * @property resource|null $comment
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Language $language
 * @property Player $player
 * @property Target $target
 */
class Writeup extends \yii\db\ActiveRecord
{
  const SCENARIO_SUBMIT = 'submit';
  public $cnt;
  public $_ratings=[
    [ 'id'=>0, 'name' => "Not rated!", 'icon'=>null],
    [ 'id'=>1, 'name' => "1 - Ok", 'icon'=>'fa-battery-quarter red-success',],
    [ 'id'=>2, 'name' => "2 - Nice", 'icon'=>'fa-battery-half text-secondary',],
    [ 'id'=>3, 'name' => "3 - Good", 'icon'=>'fa-battery-three-quarters text-warning',],
    [ 'id'=>4, 'name' => "4 - Well written", 'icon'=>'fa-battery-full',],
    [ 'id'=>5, 'name' => "5 - Excellent", 'icon'=>'fa-battery-full',],
  ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'writeup';
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_SUBMIT => ['player_id', 'target_id','approved','status','content'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content'], 'filter', 'filter' => function ($value) {
              if ($value === null || $value === '') {
                  return null;
              }

              $value = str_replace("\r\n", "\n", $value);
              $value = str_replace("\r", "", $value);

              $cleaned = preg_replace('/[\x{2028}\x{2029}]/u', "\n", $value);
              if ($cleaned !== null && $cleaned !== false) {
                  $value = $cleaned;
              }

              $value = str_replace("\u{00A0}", ' ', $value);

              $cleaned = preg_replace('/[\x{200B}\x{200C}\x{200D}\x{2060}\x{FEFF}\x{00AD}]/u', '', $value);
              if ($cleaned !== null && $cleaned !== false) {
                  $value = $cleaned;
              }

              return $value;
            }],

            [['approved'], 'default', 'value' => false],
            ['formatter', 'default', 'value' => 'text'],
            ['language_id', 'default', 'value' => 'en'],
            ['status', 'default', 'value' => 'PENDING'],

            [['player_id', 'target_id','content'], 'required'],

            [['player_id', 'target_id'], 'integer'],
            [['approved'], 'boolean'],
            [['status', 'comment'], 'string'],
            [['content'], 'filter','filter'=>'trim'],
            [['content'], 'string', 'min'=>20],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Language::class, 'targetAttribute' => ['language_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'id' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'player_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'target_id' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'approved' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                ],
                'typecastAfterValidate' => true,
                'typecastBeforeSave' => true,
                'typecastAfterFind' => true,
          ],
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
    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'target_id' => 'Target ID',
            'content' => 'Content',
            'approved' => 'Approved',
            'status' => 'Status',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Player]].
     *
     * @return \yii\db\ActiveQuery|\app\models\PlayerQuery|Player
     */
    public function getPlayer()
    {
        return $this->hasOne(Player::class, ['id' => 'player_id']);
    }

    /**
     * Gets query for [[Target]].
     *
     * @return \yii\db\ActiveQuery|TargetQuery|Target
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }

    /**
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery|LanguageQuery|Language
     */
    public function getLanguage()
    {
        return $this->hasOne(\app\models\Language::class, ['id' => 'language_id']);
    }

    /**
     * Gets query for [[Headshot]].
     *
     * @return \yii\db\ActiveQuery|HeadhostQuery|Headshot
     */
    public function getHeadshot()
    {
        return $this->hasOne(Headshot::class, ['player_id'=>'player_id','target_id'=>'target_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRatings()
    {
      return $this->hasMany(WriteupRating::class, ['writeup_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAverageRating()
    {
      return $this->getRatings()->average('rating');
    }

    public function getAverageRatingName()
    {
      return $this->_ratings[intval($this->getRatings()->average('rating'))]['name'];
    }

    /**
     * {@inheritdoc}
     * @return WriteupQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WriteupQuery(get_called_class());
    }

    public function getFormatted()
    {
      return $this->{$this->formatter};
    }


    public function getText()
    {
      return '<pre style="color: #bdbdbd">'.\yii\helpers\Html::encode($this->content).'</pre>';
    }

    public function getMarkdown()
    {
      return Yii::$app->formatter->asMarkdown($this->content);
    }

}
