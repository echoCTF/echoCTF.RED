<?php
namespace app\components;
use yii\web\AssetManager;
class echoCTFAssetManager extends AssetManager
{
  public $nullPublish=false;
  
  public function publish($path,$options=null){
    if($this->nullPublish!==true)
      return parent::publish($path,$options);
  }
}
