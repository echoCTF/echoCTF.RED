<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class StaticPageController extends \yii\web\Controller
{
  public function actionView($slug)
  {
    $model=$this->findModel($slug);
    return $this->render('view',['content'=>$model]);
  }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Page the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
      if(($model=\app\modelscli\Pages::findOne(['slug'=>$id])) === null )
      {
        throw new NotFoundHttpException(\Yii::t('app','The requested page does not exist.'));
      }

      return $model;
    }

}