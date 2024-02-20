<?php
namespace app\modules\restapi\controllers;

use Yii;
use yii\helpers\Url;
use yii\rest\ActiveController;
use yii\web\ServerErrorHttpException;

class FindingController extends ActiveController
{
    public $modelClass='app\modules\gameplay\models\Finding';
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }

    public function actionCreate()
    {
        /* @var $model \yii\db\ActiveRecord */
        $params=Yii::$app->getRequest()->getBodyParams();
        if(($model=$this->modelClass::findOne(['target_id'=>$params['target_id'],'protocol'=>$params['protocol'],'port'=>$params['port']]))==null)
        {
            $model = new $this->modelClass([ ]);
        }

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', $model->getPrimaryKey(true));
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

}
