<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class FlagForm extends Model
{
    public $code;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Flag',
        ];
    }

}
