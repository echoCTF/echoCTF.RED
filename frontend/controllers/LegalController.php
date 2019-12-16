<?php

namespace app\controllers;

class LegalController extends \yii\web\Controller
{
    public function actionPrivacyPolicy()
    {
        return $this->render('privacy-policy');
    }

    public function actionTermsAndConditions()
    {
        return $this->render('terms-and-conditions');
    }

}
