<?php

namespace app\modules\infrastructure\models;

use Yii;
use app\modules\gameplay\models\Network;
use app\modules\gameplay\models\Target;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use \yii\helpers\Html as H;
use app\components\WebhookTrigger as Webhook;
use yii\helpers\Url;

/**
 * This is the model class for table "network_target_schedule".
 *
 * @property int $id
 * @property int $target_id
 * @property int $network_id
 * @property string $migration_date
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Network $network
 * @property Target $target
 */
class NetworkTargetSchedule extends \yii\db\ActiveRecord
{
    const EVENT_TARGET_MIGRATE="event_target_migrate";

    public function init()
    {
        $this->on(self::EVENT_TARGET_MIGRATE, [$this, 'addNews']);
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network_target_schedule';
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
            [['target_id', 'migration_date'], 'required'],
            [['target_id', 'network_id'], 'integer'],
            [['migration_date'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['migration_date', 'created_at', 'updated_at'], 'safe'],
            ['network_id',  'required', 'when'=>function ($model) {
                if($model->target && $model->target->network && $model->target->networkTarget->network_id===$model->network_id)
                    $this->addError('network_id', 'The target is already on the network you are trying to schedule');
            }],
            [['network_id'], 'exist', 'skipOnError' => true, 'targetClass' => Network::class, 'targetAttribute' => ['network_id' => 'id']],
            [['target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Target::class, 'targetAttribute' => ['target_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'target_id' => Yii::t('app', 'Target ID'),
            'network_id' => Yii::t('app', 'Network ID'),
            'migration_date' => Yii::t('app', 'Migration Date'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Network]].
     *
     * @return \yii\db\ActiveQuery|NetworkQuery
     */
    public function getNetwork()
    {
        return $this->hasOne(Network::class, ['id' => 'network_id']);
    }

    /**
     * Gets query for [[Target]].
     *
     * @return \yii\db\ActiveQuery|TargetQuery
     */
    public function getTarget()
    {
        return $this->hasOne(Target::class, ['id' => 'target_id']);
    }

    /**
     * {@inheritdoc}
     * @return NetworkTargetScheduleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NetworkTargetScheduleQuery(get_called_class());
    }

  public function addNews()
  {
    if($this->network!==null && ($this->network->announce===false || $this->network->active===false)) return;

    $news=new \app\modules\content\models\News;
    $news->title=sprintf(\Yii::t('app',"Target %s migrated"),$this->target->name);
    $news->category=H::img("/images/news/category/target-migration.svg",['width'=>'25px']);
    if($this->network_id===null)
    {
      $bodyPlain=sprintf(\Yii::t('app',"Hi @everyone, just a heads up, the target [**%s**] => https://%s/target/%d is now available for you to headshot in the general targets listing.\n\nHave fun and Happy Hacking :heart:"),$this->target->name,Yii::$app->sys->offense_domain,$this->target_id);
      $news->body=sprintf(\Yii::t('app',"Just a heads up, the target [%s], is now available on the general targets listing."),H::a($this->target->name,'/target/'.$this->target_id));
    }
    else
    {
      if($this->target->status=='online')
        $bodyPlain=sprintf(\Yii::t('app',"Hi @everyone, just a heads up, the target [**%s**] => https://%s/target/%d got migrated to the [**%s**] and its ready for you to headshot.\n\nHave fun and Happy Hacking :heart:"),$this->target->name,Yii::$app->sys->offense_domain,$this->target_id,$this->network->name);
      else if($this->target->scheduled_at && $this->target->status=='powerup')
        $bodyPlain=sprintf(\Yii::t('app',"Hi @everyone, just a heads up, the target [**%s**] => https://%s/target/%d got migrated to the [**%s**] and will become available at %s.\n\nHave fun and Happy Hacking :heart:"),$this->target->name,Yii::$app->sys->offense_domain,$this->target_id,$this->network->name,$this->target->scheduled_at);


      $news->body=sprintf(\Yii::t('app',"Just a heads up, the target [%s], got migrated to a new network [%s]."),H::a($this->target->name,'/target/'.$this->target_id),H::a($this->network->name,'/network/'.$this->network_id));
    }
    if(Yii::$app->sys->discord_news_webhook!==false)
    {
      $data["avatar_url"]=sprintf("https://%s/images/appicon.png",Yii::$app->sys->offense_domain);
      $data['username']='echoCTF.RED';
      $data['content']=$bodyPlain;
      $client = new Webhook(['url' => Yii::$app->sys->discord_news_webhook,'data'=>json_encode($data)]);
      $client->run();
    }

    if($news->save()===false)
      throw new \Exception('Failed to create news entry.');
  }
}
