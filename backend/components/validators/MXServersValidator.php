<?php
/**
 * Check email address MX Records for denied servers.
 */
namespace app\components\validators;

use yii\validators\Validator;
use yii\helpers\ArrayHelper;

class MXServersValidator extends Validator
{
    public $range;
    public $banned_message="Sorry but you are using an email server that is banned!";
    public $nxmessage="Sorry but you are using a domain that does not exist!";
    public $mxonly=false;
    public function init()
    {
        parent::init();
        if(!$this->range)
        {
          $this->range=ArrayHelper::getColumn(\app\modelscli\BannedMxServer::find()->select('name')->asArray()->all(),'name');
        }
    }

    public function validateValue($value)
    {
      $hosts=[];
      // if not email assume value is a domain
      if (preg_match('/^(?P<name>(?:"?([^"]*)"?\s)?)(?:\s+)?(?:(?P<open><?)((?P<local>.+)@(?P<domain>[^>]+))(?P<close>>?))$/i', $value, $matches))
      {
        $value=$matches['domain'];
      }

      if($this->mxonly===false && getmxrr($value, $hosts)===false)
      {
        return [$this->nxmessage, []];
      }

      foreach($this->range as $key)
      {
        if(array_search($key, $hosts)!==false)
          return [$this->banned_message, []];
      }
      return null;
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $result = $this->validateValue($value);

        if (!empty($result)) {
          $this->addError($model, $attribute, $result[0], $result[1]);
        }
    }
}
