<?php
namespace app\components;

use Yii;

use yii\base\Component;
use yii\base\InvalidConfigException;
use app\modules\settings\models\Sysconfig as dbSys;

class WebhookTrigger extends Component
{
  public $url;
  public $data;
  public $headers=['Content-Type: application/json'];

  public function init()
  {
    parent::init();
  }

  public function run()
  {
    if (trim($this->url)=="") return;


    // init the request, set some info, send it and finally close it
    $ch = curl_init($this->url);

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }
}