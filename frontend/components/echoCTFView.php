<?php
namespace app\components;

class echoCTFView extends \yii\web\View
{
  public $_title,
          $_description="An online platform to train your offensive and defensive IT security skills.",
          $_url,
          $_image,
          $_image_width="1200",
          $_image_height="628",
          $_card='summary_large_image';

  public $_fluid;

  public function init()
  {
//      if($this->_url===null)
//        $this->_url=\yii\helpers\Url::to([null],'https');
    if($this->_image === null)
      $this->_image=\yii\helpers\Url::to('/images/logotw.png', 'https');

    $this->title=sprintf("%s - %s: %s", \Yii::$app->sys->event_name, ucfirst(\Yii::$app->controller->id), \Yii::$app->controller->action->id);
    if(!\Yii::$app->user->isGuest)
    {
        \Yii::$app->cache->Memcache->set("last_seen:".\Yii::$app->user->id, time());
        \Yii::$app->cache->Memcache->set("online:".\Yii::$app->user->id, time(), 0, \Yii::$app->sys->online_timeout);
        $this->saveSession();
    }
    parent::init();
  }

  public function saveSession()
  {
    if(\Yii::$app->cache->Memcache->get("player_session:".\Yii::$app->user->id)!==false)
      return;

    \Yii::$app->cache->Memcache->set("player_session:".\Yii::$app->user->id, \Yii::$app->session->id, 0, \Yii::$app->sys->online_timeout);
    $session=\app\models\Sessions::findOne(\Yii::$app->session->id);
    if($session===null)
      $session=new \app\models\Sessions;
    $session->player_id=\Yii::$app->user->id;
    $session->ipoctet=\Yii::$app->getRequest()->userIP;
    $session->id=\Yii::$app->session->id;
    $session->data=json_encode($_SESSION);
    $session->expire=new \yii\db\Expression('UNIX_TIMESTAMP(NOW()+INTERVAL 15 DAY)');
    $session->save(false);
    unset($session);
  }
  public function getOg_title()
  {
    if($this->_title === null)
      return ['property'=>'og:title', 'content'=>trim($this->title)];
  }

  public function getOg_description()
  {
    return ['property'=>'og:description', 'content'=>trim($this->_description)];
  }

  public function getOg_site_name()
  {
    return ['property'=>'og:site_name', 'content'=>trim(\Yii::$app->sys->event_name)];
  }

  public function getOg_image()
  {
    return ['property'=>'og:image', 'content'=>sprintf("%s?%s", $this->_image, \Yii::$app->security->generateRandomString(5))];
  }

  public function getOg_url()
  {
    return ['property'=>'og:url', 'content'=>$this->_url];
  }

  public function getTwitter_card()
  {
    return ['name'=>'twitter:card', 'content'=>$this->_card];
  }

  public function getTwitter_site()
  {
    return ['name'=>'twitter:site', 'content'=>"@echoCTF"];
  }

  public function getTwitter_title()
  {
    return ['name'=>'twitter:title', 'content'=>$this->title];
  }

  public function getTwitter_description()
  {
    return ['name'=>'twitter:description', 'content'=>trim($this->_description)];
  }

  public function getTwitter_image()
  {
    return ['name'=>'twitter:image', 'content'=>sprintf("%s?%s", $this->_image, \Yii::$app->security->generateRandomString(5))];
  }

  public function getTwitter_image_width()
  {
    return ['name'=>'twitter:image:width', 'content'=>$this->_image_width];
  }

  public function getTwitter_image_height()
  {
    return ['name'=>'twitter:image:height', 'content'=>$this->_image_height];
  }
}
