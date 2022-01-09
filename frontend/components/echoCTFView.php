<?php
namespace app\components;
/**
 * @property string image
 * @property string description
 * @property string twitterHandle
 */
class echoCTFView extends \yii\web\View
{
  public $_title;
  public $_url;
  public $_fluid;
  public $_image;
  public $_description="An online platform to train your offensive and defensive IT security skills.";
  public $_image_width="1200";
  public $_image_height="628";
  public $_card='summary_large_image';
  public $_twitter_handle="@echoCTF";

  public function init()
  {

    if(!empty(\Yii::$app->controller->id) && !empty(\Yii::$app->controller->action))
    {
      $this->title=sprintf("%s - %s: %s", \Yii::$app->sys->event_name, ucfirst(\Yii::$app->controller->id), \Yii::$app->controller->action->id);
    }
    else {
      $this->title=sprintf("%s", \Yii::$app->sys->event_name);
    }
    parent::init();
  }

  public function getTwitterHandle()
  {
    if(\Yii::$app->sys->twitter_account!==null && \Yii::$app->sys->twitter_account!==false)
    {
      return '@'.\Yii::$app->sys->twitter_account;
    }
    return $this->_twitter_handle;
  }
  public function getDescription()
  {
    if(\Yii::$app->sys->site_description !== null && \Yii::$app->sys->site_description !== false)
    {
      return \Yii::$app->sys->site_description;
    }
    return $this->_description;
  }

  public function getImage()
  {
    if($this->_image === null)
      return \yii\helpers\Url::to('/images/logotw.png', 'https');
    return $this->_image;
  }

  public function getOg_title()
  {
    if($this->_title === null)
      return ['property'=>'og:title', 'content'=>trim($this->title)];
  }

  public function getOg_description()
  {
    return ['property'=>'og:description', 'content'=>trim($this->description)];
  }

  public function getOg_site_name()
  {
    return ['property'=>'og:site_name', 'content'=>trim(\Yii::$app->sys->event_name)];
  }

  public function getOg_image()
  {
    return ['property'=>'og:image', 'content'=>sprintf("%s?%s", $this->image, \Yii::$app->security->generateRandomString(5))];
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
    return ['name'=>'twitter:site', 'content'=>$this->twitterHandle];
  }

  public function getTwitter_title()
  {
    return ['name'=>'twitter:title', 'content'=>$this->title];
  }

  public function getTwitter_description()
  {
    return ['name'=>'twitter:description', 'content'=>trim($this->description)];
  }

  public function getTwitter_image()
  {
    return ['name'=>'twitter:image', 'content'=>sprintf("%s?%s", $this->image, \Yii::$app->security->generateRandomString(5))];
  }

  public function getTwitter_image_width()
  {
    return ['name'=>'twitter:image:width', 'content'=>$this->_image_width];
  }

  public function getTwitter_image_height()
  {
    return ['name'=>'twitter:image:height', 'content'=>$this->_image_height];
  }

  public function renderPhpContent($_content_, $_params_ = [])
  {
      $_obInitialLevel_ = ob_get_level();
      ob_start();
      ob_implicit_flush(false);
      extract($_params_, EXTR_OVERWRITE);
      try {
          eval($_content_);
          return ob_get_clean();
      } catch (\Exception $e) {
          while (ob_get_level() > $_obInitialLevel_) {
              if (!@ob_end_clean()) {
                  ob_clean();
              }
          }
          throw $e;
      } catch (\Throwable $e) {
          while (ob_get_level() > $_obInitialLevel_) {
              if (!@ob_end_clean()) {
                  ob_clean();
              }
          }
          throw $e;
      }
  }
}
