<?php

namespace app\modules\speedprogramming\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class SpeedForm extends Model
{
    public $language;
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file'],
//            ['file', 'file', 'extensions' => ['c', 'cpp', 'c++','php','php7','py','py2','py3','java'], 'maxSize' => 1024 * 1024 * 1,'minSize'=>128,'tooSmall'=>'Solution too small'],
            [['language', 'file'], 'required'],
            ['language', 'string'],
            ['language', 'in', 'strict'=>true, 'range' => ['cs','c','cpp','py2','py3','php7','java']]
        ];
    }

    public function getAvailableLanguages()
    {
      return [
        'c'=>'C',
        'cpp'=>'C++',
        'cs'=>'C#',
        'py2'=>'Python 2.x',
        'py3'=>'Python 3.x',
        'php7'=>'PHP 7.x',
        'java'=>'Java'
      ];
    }
}
