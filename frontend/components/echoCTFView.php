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
      if((\Yii::$app->cache instanceof \yii\caching\MemCache) && (\Yii::$app->cache->memcache instanceof \Memcache))
      {
        \Yii::$app->cache->Memcache->set("last_seen:".\Yii::$app->user->id, time());
        \Yii::$app->cache->Memcache->set("online:".\Yii::$app->user->id, time(), 0, \Yii::$app->sys->online_timeout);
      }
    }
    parent::init();
  }

  public function getOg_title()
  {
    //<meta property="og:title" content="Key Concepts: Dependency Injection Container" />
    if($this->_title === null)
      return ['property'=>'og:title', 'content'=>trim($this->title)];
  }

  public function getOg_description()
  {
    //<meta property="og:description" content="" />
    return ['property'=>'og:description', 'content'=>trim($this->_description)];
  }

  public function getOg_site_name()
  {
    // <meta property="og:site_name" content="Yii Framework" />
    return ['property'=>'og:site_name', 'content'=>trim(\Yii::$app->sys->event_name)];
  }

  public function getOg_image()
  {
    //<meta property="og:image" content="https://www.yiiframework.com/image/facebook_cover.png" />
    return ['property'=>'og:image', 'content'=>sprintf("%s?%s", $this->_image, \Yii::$app->security->generateRandomString(5))];
  }

  public function getOg_url()
  {
    //<meta property="og:url" content="/doc/guide/2.0/en/concept-di-container" />
    return ['property'=>'og:url', 'content'=>$this->_url];
  }

  public function getTwitter_card()
  {
    //<meta name="twitter:card" content="summary" />
    return ['name'=>'twitter:card', 'content'=>$this->_card];
  }

  public function getTwitter_site()
  {
    return ['name'=>'twitter:site', 'content'=>"@echoCTF"];
  }

  public function getTwitter_title()
  {
//    <meta name="twitter:title" content="Key Concepts: Dependency Injection Container" />
    return ['name'=>'twitter:title', 'content'=>$this->title];
  }

  public function getTwitter_description()
  {
//    <meta name="twitter:description" content="" />
    return ['name'=>'twitter:description', 'content'=>trim($this->_description)];
  }

  public function getTwitter_image()
  {
//    <meta name="twitter:image" content="https://www.yiiframework.com/image/twitter_cover.png" />
    return ['name'=>'twitter:image', 'content'=>sprintf("%s?%s", $this->_image, \Yii::$app->security->generateRandomString(5))];
  }

  public function getTwitter_image_width()
  {
//    <meta name="twitter:image:width" content="120" />
    return ['name'=>'twitter:image:width', 'content'=>$this->_image_width];
  }

  public function getTwitter_image_height()
  {
//    <meta name="twitter:image:height" content="120" />
    return ['name'=>'twitter:image:height', 'content'=>$this->_image_height];
  }

/*
  <meta name="msapplication-config" content="/favico/browserconfig.xml">
  <meta name="theme-color" content="#ffffff">

*/
}
