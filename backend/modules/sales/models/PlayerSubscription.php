<?php

namespace app\modules\sales\models;

use Yii;
use app\modules\frontend\models\Player;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "player_subscription".
 *
 * @property int $player_id
 * @property string|null $subscription_id
 * @property string|null $session_id
 * @property string|null $price_id
 * @property int|null $active
 * @property timestamp|null $starting
 * @property timestamp|null $ending
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class PlayerSubscription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player_subscription';
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
            [['player_id'], 'required'],
            [['player_id', 'active'], 'integer'],
            [['created_at', 'updated_at','starting','ending'], 'safe'],
            [['subscription_id', 'session_id', 'price_id'], 'string', 'max' => 255],
            [['player_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'player_id' => Yii::t('app', 'Player ID'),
            'subscription_id' => Yii::t('app', 'Subscription ID'),
            'session_id' => Yii::t('app', 'Session ID'),
            'price_id' => Yii::t('app', 'Price ID'),
            'active' => Yii::t('app', 'Active'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery|ProductQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['price_id' => 'price_id']);
    }

    /**
     * {@inheritdoc}
     * @return PlayerSubscriptionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PlayerSubscriptionQuery(get_called_class());
    }
}
