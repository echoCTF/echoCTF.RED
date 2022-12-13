<?php

namespace app\modules\content\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\components\WebhookTrigger as Webhook;

/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $body
 * @property string|null $category
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class News extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
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
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'category'], 'string', 'max' => 255],
            [['body'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'body' => Yii::t('app', 'Body'),
            'category' => Yii::t('app', 'Category'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Post a message to a discord webhook
     */
    public function toDiscord()
    {
      if(Yii::$app->sys->discord_news_webhook!==false){
        $data["avatar_url"]=sprintf("https://%s/images/appicon.png",Yii::$app->sys->offense_domain);
        $data['username']='echoCTF.RED';
        $data['content']=$this->body;
        $client = new Webhook(['url' => Yii::$app->sys->discord_news_webhook,'data'=>json_encode($data)]);
        return $client->run();
      }
    }
    /**
     * {@inheritdoc}
     * @return NewsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NewsQuery(get_called_class());
    }
}
