<?php
namespace app\components;

use Yii;

use yii\base\Component;
use yii\base\InvalidConfigException;
use app\modules\settings\models\Sysconfig as dbSys;
class StopForumSpam extends Component
{
  public $url = 'http://api.stopforumspam.org/api';
  public $email;

  public function init()
  {
    parent::init();
  }

  public function check()
  {
    $data = array(
        'email' => $this->email,
        'json'=>'',
        'confidence'=>'',
    );

    $data = http_build_query($data);

    // init the request, set some info, send it and finally close it
    $ch = curl_init($this->url);

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    curl_close($ch);
    return $result;
  }
}
