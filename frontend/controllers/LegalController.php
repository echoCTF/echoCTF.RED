<?php

namespace app\controllers;
use Yii;
use yii\helpers\ArrayHelper;
use app\overloads\yii\filters\AccessControl;

class LegalController extends \app\components\BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'access' => [
                'class' => AccessControl::class,
                'only' => ['privacy-policy','terms-and-conditions'],
                'rules' => [
                   'disabledRoute'=>[
                     'actions' => ['privacy-policy','terms-and-conditions'],
                   ],
                   [
                     'actions' => ['privacy-policy','terms-and-conditions'],
                     'allow' => true,
                   ],
                ],
            ],
        ]);
    }

    public function actionPrivacyPolicy()
    {
        $content=\app\modelscli\Pages::findOne(2);
        if ($content===null)
            return $this->redirect('/');
        return $this->render('privacy-policy',['content'=>$content]);
    }

    public function actionTermsAndConditions()
    {
        $content=\app\modelscli\Pages::findOne(1);
        if ($content===null)
            return $this->redirect('/');
        return $this->render('terms-and-conditions',['content'=>$content]);
    }

}
