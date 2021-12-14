<?php

namespace app\modules\infrastructure\models;


use Yii;
use yii\base\Model;

class TargetExecCommandForm extends Model
{
    public $command;

    public function rules()
    {
        return [
            [['command'], 'trim'],
            [['command'], 'required'],
        ];
    }
    public function getCommandArray()
    {
      return explode(" ",$this->command);
    }
}
