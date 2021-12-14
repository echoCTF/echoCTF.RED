<?php

namespace app\modules\infrastructure\models;


use Yii;
use yii\base\Model;

class TargetExecCommandForm extends Model
{
    public $command;
    public $tty;
    public $stdout;
    public $stderr;
    public function rules()
    {
        return [
            [['tty','stdout','stderr'], 'default','value'=>1],
            [['tty','stdout','stderr'], 'boolean'],
            [['command'], 'trim'],
            [['command'], 'required'],
        ];
    }
    public function getCommandArray()
    {
      return explode(" ",$this->command);
    }
}
