<?php
/**
 * Validate email with Verifymail.io
 */
namespace app\components\validators;

use yii\validators\Validator;
use yii\helpers\ArrayHelper;

class VerifymailValidator extends Validator
{
    public $url = 'https://verifymail.io/api/';
    public $message="Verifymail.io: Disposable email detected! Please use a different email address.";

    public function init()
    {
        parent::init();
    }

    public function validateValue($value)
    {
      if(\Yii::$app->sys->signup_ValidatemailValidator===false)
        return;

      $data = http_build_query(['key'=>\Yii::$app->sys->verifymail_key]);

      $domain=$value;

      if(str_contains($value,'@'))
      {
        $parts=explode("@",$value);
        if(count($parts)<2)
        {
          return ['Verifymail.io: Failed to parse email!', [
            'email' => $value,
          ]];
        }
        $domain=end($parts);
      }

      $req=$this->url.$domain.'?'.$data;
      $ch = curl_init($req);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 40);

      try
      {
        $result = curl_exec($ch);
        curl_close($ch);
        $retData=json_decode($result);
        if($retData != null && $retData->disposable===true)
        {
          return [$this->message, [
            'email' => $value,
          ]];
        }
      }
      catch(\Exception $e)
      {
        if(curl_errno($ch)===0)
          return [$this->message, ['email' => $value]];
      }
    }

    public function validateAttribute($model, $attribute)
    {
      if(\Yii::$app->sys->signup_ValidatemailValidator===false)
        return;
      $value = $model->$attribute;

      $data = http_build_query(['key'=>\Yii::$app->sys->verifymail_key]);

      if(str_contains($value,'@'))
      {
        $parts=explode("@",$value);
        if(count($parts)<2)
        {
          return ['Verifymail.io: Failed to parse email!', [
            'email' => $value,
          ]];
        }
        $domain=end($parts);
      }

      $req=$this->url.$domain.'?'.$data;
      $ch = curl_init($req);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 40);

      try
      {
        $result = curl_exec($ch);
        curl_close($ch);
        $retData=json_decode($result);
        if($retData && $retData->message)
        {
          \Yii::error("Verifymail.io return message: ".$retData->message);
        }
        if($retData != null && $retData->disposable===true)
        {
          return [$this->message, ['email' => $value]];
        }
      }
      catch(\Exception $e)
      {
        if(curl_errno($ch)===0)
          return [$this->message, ['email' => $value]];
      }
    }
}
