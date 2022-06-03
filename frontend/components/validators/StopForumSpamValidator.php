<?php
/**
 * Check memcache key for registrations performed by this IP.
 */
namespace app\components\validators;

use yii\validators\Validator;
use yii\helpers\ArrayHelper;

class StopForumSpamValidator extends Validator
{
    public $url = 'http://api.stopforumspam.org/api';
    public $message="SP: Spam email detected, sorry!";
    public $max=80;

    public function init()
    {
        parent::init();
    }
    public function validateValue($value)
    {
      $data = array(
          'email' => $value,
          'json'=>'',
          'confidence'=>'80',
      );

      $data = http_build_query($data);

      // init the request, set some info, send it and finally close it
      $ch = curl_init($this->url);

      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      try
      {
        $result = curl_exec($ch);
        curl_close($ch);
        $retData=json_decode($result)->email;
        if(property_exists($retData,'confidence') && $retData->confidence>$this->max)
          throw new \yii\base\UserException('StopForumSpamValidator null');
      }
      catch(\Exception $e)
      {
        return [$this->message, [
            'email' => $value,
        ]];
      }
    }
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $data = array(
            'email' => $value,
            'json'=>'',
            'confidence'=>'',
        );

        $data = http_build_query($data);

        // init the request, set some info, send it and finally close it
        $ch = curl_init($this->url);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        try
        {
          $result = curl_exec($ch);
          curl_close($ch);
          $retData=json_decode($result)->email;
          if(property_exists($retData,'confidence') && $retData->confidence>$this->max)
            throw new \yii\base\UserException('StopForumSpamValidator null');
        }
        catch(\Exception $e)
        {
            $model->addError($attribute, $this->message);
        }
    }
}
