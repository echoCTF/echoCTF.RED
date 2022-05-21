<?php

namespace app\modules\frontend\models;

use Yii;

/**
 * This is the model class for table "crl".
 *
 * @property int $id
 * @property int $player_id
 * @property string $subject
 * @property string $csr
 * @property string $crt
 * @property string $privkey
 * @property string $ts
 */
class Crl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'crl';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id'], 'integer'],
            [['csr', 'crt', 'privkey'], 'string'],
            [['ts'], 'safe'],
            [['subject'], 'string', 'max' => 255],
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
            'subject' => 'Subject',
            'csr' => 'Csr',
            'crt' => 'Crt',
            'privkey' => 'Privkey',
            'ts' => 'Ts',
        ];
    }
}
