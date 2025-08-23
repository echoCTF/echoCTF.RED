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
          $this->range=\app\modelscli\BannedMxServer::find()->select('name')->column();
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
      try{
        $getmxrr_ret=getmxrr($value, $hosts);

        if($this->mxonly===true && $getmxrr_ret===false)
        {
          return [$this->nxmessage, []];
        }
        else if ($getmxrr_ret===false) {
          return null;
        }

        foreach($this->range as $key)
        {
          if(array_search($key, $hosts)!==false)
          {
            return [$this->banned_message,[]];
          }
        }
      } catch(\Exception $e)
      {
        return [$e->getMessage(),[]];
      }
      return null;
    }
}
