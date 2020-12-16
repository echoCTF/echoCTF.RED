<?php

namespace app\controllers;
use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

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
        return $this->render('privacy-policy');
    }

    public function actionTermsAndConditions()
    {
        return $this->render('terms-and-conditions');
    }

}
