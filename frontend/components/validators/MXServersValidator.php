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
      // if not email assume value is a domain
      if (preg_match('/^(?P<name>(?:"?([^"]*)"?\s)?)(?:\s+)?(?:(?P<open><?)((?P<local>.+)@(?P<domain>[^>]+))(?P<close>>?))$/i', $value, $matches))
      {
        $value=$matches['domain'];
      }

      if(getmxrr($value, $hosts)===false && $this->mxonly===false)
      {
        return [$this->nxmessage, [
            'domain' => $value,
        ]];
      }

      foreach($this->range as $key)
      {
        if(array_search($key, $hosts)!==false)
          return [$this->banned_message, [
              'domain' => $value,
          ]];
      }
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $hosts=[];
        // if not email assume value is a domain
        if (preg_match('/^(?P<name>(?:"?([^"]*)"?\s)?)(?:\s+)?(?:(?P<open><?)((?P<local>.+)@(?P<domain>[^>]+))(?P<close>>?))$/i', $value, $matches))
        {
          $value=$matches['domain'];
        }

        if(getmxrr($value, $hosts)===false && $this->mxonly===false)
        {
          $model->addError($attribute, $this->nxmessage);
          return;
        }

        foreach($this->range as $key)
        {
          if(array_search($key, $hosts)!==false)
          {
            $model->addError($attribute, $this->banned_message);
            return;
          }
        }
    }
}
