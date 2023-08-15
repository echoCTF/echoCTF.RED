<?php
/**
 * Check memcache key for registrations performed by this IP.
 */
namespace app\components\validators;

use yii\validators\Validator;
use yii\helpers\ArrayHelper;

class WhoisValidator extends Validator
{
    public $message="Domain validation error.";

    public function init()
    {
        parent::init();
    }
    public function validateValue($value)
    {
      // if not email assume value is a domain
      if (preg_match('/^(?P<name>(?:"?([^"]*)"?\s)?)(?:\s+)?(?:(?P<open><?)((?P<local>.+)@(?P<domain>[^>]+))(?P<close>>?))$/i', $value, $matches))
      {
        $value=$matches['domain'];
      }
      $domain = new \overals\whois\Whois($value);
      if ($domain->isAvailable())
      {
        return [$this->message, [
            'username' => $value,
        ]];
      }
    }
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (preg_match('/^(?P<name>(?:"?([^"]*)"?\s)?)(?:\s+)?(?:(?P<open><?)((?P<local>.+)@(?P<domain>[^>]+))(?P<close>>?))$/i', $value, $matches))
        {
          $value=$matches['domain'];
        }
        $domain = new \overals\whois\Whois($matches['domain']);
        if ($domain->isAvailable())
        {
            $model->addError($attribute, $this->message);
        }
    }

}
