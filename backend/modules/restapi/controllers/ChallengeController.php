<?php
namespace app\modules\restapi\controllers;

use yii\rest\ActiveController;
use yii\helpers\Html;

class ChallengeController extends ActiveController
{
    public $modelClass='app\modules\gameplay\models\Challenge';
    public function actionUpload(int $challenge_id)
    {
      \Yii::$app->response->format=\yii\web\Response:: FORMAT_JSON;
      if(($challenge=\app\modules\gameplay\models\Challenge::findOne($challenge_id)) == null)
      {
        \Yii::$app->response->statusCode=422;
        return array('status' => false, 'data'=> "Challenge not found");

      }

      $putdata=fopen("php://input", "r");
      /* Open a file for writing */
      $tmpfname=tempnam("/tmp", "challenge_upload");
      $fp=fopen($tmpfname, "w");
      if($fp === false || $putdata === false)
      {
        \Yii::$app->response->statusCode=422;
        return ['status' => false, 'data' => 'Failed to open temporary file'];
      }

      $ret=stream_copy_to_stream($putdata, $fp);
      if(rename($tmpfname, "uploads/".$challenge->filename) === FALSE)
      {
        \Yii::$app->response->statusCode=422;
        return array('status' => false, 'data'=> "Failed to move challenge from the temporary file");
      }
      fclose($fp);
      fclose($putdata);
      \Yii::$app->response->statusCode=201;
      return array('status' => true, 'data'=> "Saved", 'bytes'=>intval($ret), 'hash'=>hash_file("sha512", "uploads/".$challenge->filename));
    }

    public function actionCreateBundle()
    {
      \Yii::$app->response->format=\yii\web\Response:: FORMAT_JSON;
      $connection=\Yii::$app->db;
      $transaction=$connection->beginTransaction();
      try
      {
        $challenge=new \app\modules\gameplay\models\Challenge;
        $post=\yii::$app->request->post();
        $challenge->attributes=$post;
        if(array_key_exists('author', $post))
        {
          $author=\app\modules\frontend\models\Profile::findOne($post['author']);
          $description=sprintf('%s<p><i>Author: <a href="/profile/%d">%s</a></i></p>', $challenge->description."\n", $author->id, $author->owner->username);
          $challenge->description=$description;
        }
        if($challenge->save())
        {
          $this->doQuestions($post,$challenge);
          \Yii::$app->response->statusCode=201;
          $transaction->commit();
          return array('status' => true, 'data'=> "Saved", 'challenge_id'=>$challenge->id);
        }
      }
      catch(\Throwable $e)
      {
          \Yii::$app->response->statusCode=422;
          $transaction->rollBack();
          return array('status' => false, 'data'=>  $e->getMessage());
      }
      \Yii::$app->response->statusCode=422;
      return array('status' => false, 'data'=> 'Reached the end');
    }

    public function actionDownload(int $id)
    {
      if(($model=\app\modules\gameplay\models\Challenge::findOne($id))!==null && file_exists(\Yii::getAlias('@web/uploads/challenges/'.$model->filename)))
        return \Yii::$app->response->sendFile(\Yii::getAlias('@web/uploads/challenges/'.$model->filename));
    }
    private function doQuestions($post,$challenge)
    {
      if(array_key_exists("questions", $post))
      {
        foreach($post['questions'] as $q)
        {
          $question=new \app\modules\gameplay\models\Question;
          $question->attributes=$q;
          $question->challenge_id=$challenge->id;
          $question->save();
        }
      }
    }
}
