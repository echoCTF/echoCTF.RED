<?php

namespace app\modules\content\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "email_template".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $title
 * @property string|null $html
 * @property string|null $txt
 */
class EmailTemplate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_template';
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
            [['html', 'txt'], 'string'],
            [['name'], 'string', 'max' => 32],
            [['title'], 'string', 'max' => 255],
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
            'title' => Yii::t('app', 'Title'),
            'html' => Yii::t('app', 'Html'),
            'txt' => Yii::t('app', 'Txt'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return EmailTemplateQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EmailTemplateQuery(get_called_class());
    }
}
