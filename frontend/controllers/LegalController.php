<?php

namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use app\overloads\yii\filters\AccessControl;

class LegalController extends \app\components\BaseController
{
  public function behaviors()
  {
    $parent = parent::behaviors();
    unset($parent['access']['rules']['teamsAccess']);
    unset($parent['access']['rules']['eventStartEnd']);
    unset($parent['access']['rules']['eventStart']);

    return ArrayHelper::merge($parent, [
      'access' => [
        'class' => AccessControl::class,
        'only' => ['privacy-policy', 'terms-and-conditions'],
        'rules' => [
          'disabledRoute' => [
            'actions' => ['privacy-policy', 'terms-and-conditions'],
          ],
          [
            'actions' => ['privacy-policy', 'terms-and-conditions'],
            'allow' => true,
          ],
        ],
      ],
    ]);
  }

  public function actionPrivacyPolicy()
  {
    $content = \app\modelscli\Pages::findOne(['slug'=>'privacy-policy']);
    if ($content === null)
      return $this->redirect('/');
    return $this->render('privacy-policy', ['content' => $content]);
  }

  public function actionTermsAndConditions()
  {
    $content = \app\modelscli\Pages::findOne(['slug'=>'terms-and-conditions']);
    if ($content === null)
      return $this->redirect('/');
    return $this->render('terms-and-conditions', ['content' => $content]);
  }
}
