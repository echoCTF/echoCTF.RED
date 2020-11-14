<?php
namespace app\modules\restapi\controllers;

use yii\helpers\Html;

class SyncfilesController extends \yii\rest\Controller
{

  public function actionAvailable()
  {
    \Yii::$app->response->format=\yii\web\Response:: FORMAT_JSON;
    return [
      'event_logo'=>[
        'src'=>\Yii::$app->sys->event_logo,
        'dst'=>'@web/images/logo.png',
        'hash'=>$this->hash(\Yii::$app->sys->event_logo)
      ],
      'event_logo_small'=>[
        'src'=>\Yii::$app->sys->event_logo_small,
        'dst'=>'@web/images/logo-small.png',
        'hash'=>$this->hash(\Yii::$app->sys->event_logo_small)
      ],
      'event_favicon'=>[
        'src'=>\Yii::$app->sys->event_favicon,
        'dst'=>'@web/favicon.ico',
        'hash'=>$this->hash(\Yii::$app->sys->event_favicon)
      ],
    ];
  }
  protected function hash($src)
  {
    if($src && file_exists(\Yii::getAlias($src)))
      return hash_file('sha512', \Yii::getAlias($src));
    return null;
  }
}
