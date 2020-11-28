<?php

namespace app\modules\game\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * Default controller for the `game` module
 */
class DefaultController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['rate'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['rate'],
                        'roles' => ['@'],
                        'verbs'=>['post'],
                        'matchCallback' => function ($rule, $action) {
                          return !Yii::$app->DisabledRoute->disabled($action);
                        },
                    ],
                ],
            ],
        ];
    }



    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Accepts a rating value from a player
     * @return string
     */
    public function actionRate($id)
    {
      $headshot=\app\modules\game\models\Headshot::findOne(['target_id'=>$id,'player_id'=>Yii::$app->user->id]);
      if($headshot===NULL)
      {
        throw new NotFoundHttpException('You dont have a headshot for the given target.');
      }
      if(Yii::$app->request->isPost && Yii::$app->request->post('rating')!==null)
      {
        $rating=(int)Yii::$app->request->post('rating');
        $headshot->updateAttributes(['rating' => $rating]);
      }
    }


}
