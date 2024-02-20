<?php
namespace app\modules\restapi\controllers;

use yii\rest\ActiveController;
use Yii;

class TreasureController extends ActiveController
{
    public $modelClass='app\modules\gameplay\models\Treasure';

  public function actionCreateWithActions()
    {
      \Yii::$app->response->format=\yii\web\Response:: FORMAT_JSON;
      $connection=\Yii::$app->db;
      $transaction=$connection->beginTransaction();
      $params=Yii::$app->getRequest()->getBodyParams();
      try
      {
        if(($treasure=$this->modelClass::findOne(['target_id'=>$params['target_id'],'code'=>$params['code']]))==null)
        {
          $treasure=new $this->modelClass;
        }
        $post=\yii::$app->request->post();
        $treasure->load(Yii::$app->getRequest()->getBodyParams(), '');

        if($treasure->validate() && $treasure->save())
        {
          $this->doTreasureActions($post,$treasure);
          \Yii::$app->response->statusCode=201;
          $transaction->commit();
          return array('status' => true, 'data'=> "Saved");
        }
      }
      catch(\Throwable $e)
      {
          \Yii::$app->response->statusCode=422;
          $transaction->rollBack();
          return array('status' => false, 'data'=>  $e->getMessage());
      }
      return array('status' => false, 'data'=> 'Reached the end');
    }
    private function doTreasureActions($post,$treasure)
    {
      if(array_key_exists("actions", $post)!==false)
      {
        foreach($post['actions'] as $post_action)
        {
          $treasure_action=new \app\modules\smartcity\models\TreasureAction;
          $treasure_action->attributes=$post_action;
          $treasure_action->treasure_id=$treasure->id;
          $treasure_action->save();
        }
      }
    }
}
