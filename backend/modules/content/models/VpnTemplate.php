<?php

namespace app\modules\content\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "vpn_template".
 *
 * @property int $id
 * @property string $name
 * @property int $client
 * @property int $server
 * @property int $active
 * @property int $visible
 * @property string|null $description
 * @property string|null $content
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class VpnTemplate extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'client' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                    'server' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                    'active' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                    'visible' => AttributeTypecastBehavior::TYPE_BOOLEAN,
                ],
                'typecastAfterValidate' => true,
                'typecastBeforeSave' => true,
                'typecastAfterFind' => true,
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
//                'createdAtAttribute' => 'create_time',
//                'updatedAtAttribute' => 'update_time',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vpn_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name','filename'], 'required'],
            [['client', 'server','active','visible'], 'integer'],
            [['client', 'server','active','visible'], 'boolean'],
            [['client', 'active','visible'], 'default','value'=>true],
            [['server'], 'default','value'=>false],
            [['description', 'content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'client' => Yii::t('app', 'Client'),
            'server' => Yii::t('app', 'Server'),
            'description' => Yii::t('app', 'Description'),
            'content' => Yii::t('app', 'Content'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return VpnTemplateQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VpnTemplateQuery(get_called_class());
    }
}
