<?php

namespace app\controllers;
use Yii;
use app\models\Profile;
use yii\data\ActiveDataProvider;
use \app\modules\target\models\Target;
use \app\modules\target\models\TargetQuery;
use yii\data\SqlDataProvider;


class ProfileController extends \yii\web\Controller
{
    public function actionMe()
    {
      $profile=$this->findModel(Yii::$app->user->id);


      $command = Yii::$app->db->createCommand('select * from player_spin WHERE player_id=:player_id');
      $playerSpin=$command->bindValue(':player_id',$profile->player_id)->query()->read();
      $model=\app\models\Stream::find()->select('stream.*,TS_AGO(ts) as ts_ago')
      ->where(['player_id'=>$profile->player_id])
      ->orderBy(['ts'=>SORT_DESC]);
      $streamProvider = new ActiveDataProvider([
          'query' => $model,
          'pagination' => [
              'pageSizeParam'=>'stream-perpage',
              'pageParam'=>'stream-page',
              'pageSize' => 10,
          ]
        ]);

        $profileForm=$profile;
        $profileForm->scenario='me';
        $accountForm=$profile->owner;
        $accountForm->scenario='profile';
        $accountForm->password=null;
        $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM (SELECT t.*,inet_ntoa(t.ip) as ipoctet,count(distinct t2.id) as total_treasures,count(distinct t4.treasure_id) as player_treasures, count(distinct t3.id) as total_findings, count(distinct t5.finding_id) as player_findings FROM target AS t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=:player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=:player_id GROUP BY t.id HAVING player_treasures>0 or player_findings>0 ORDER BY t.ip,t.fqdn,t.name) as tt'
        , [':player_id' => $profile->player_id])->queryScalar();


        $targetProgressProvider = new SqlDataProvider([
            'sql' => 'SELECT t.*,inet_ntoa(t.ip) as ipoctet,count(distinct t2.id) as total_treasures,count(distinct t4.treasure_id) as player_treasures, count(distinct t3.id) as total_findings, count(distinct t5.finding_id) as player_findings FROM target AS t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=:player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=:player_id GROUP BY t.id HAVING player_treasures>0 or player_findings>0 ORDER BY t.ip,t.fqdn,t.name',
            'params' => [':player_id' => $profile->player_id],
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => [
                    'name',
                    'fqdn',
                    'ip',
                ],
            ],
        ]);
        //$targetProgressProvider = Target::find()->player_progress(Yii::$app->user->id);

        return $this->render('index',[
          'profile'=>$profile,
          'playerSpin'=>$playerSpin,
          'streamProvider'=>$streamProvider,
          'accountForm'=>$accountForm,
          'profileForm'=>$profileForm,
          'targetProgressProvider'=>$targetProgressProvider
        ]);
    }
    public function actionIndex($id)
    {
      if($id==Yii::$app->user->id)
        return $this->redirect(['/profile/me']);

      $profile=$this->findModel($id);
      if(Yii::$app->user->isGuest && $profile->visibility!='public')
        			return $this->redirect(['/']);

      if($profile->visibility!='public' && $profile->visibility!='ingame')
        			return $this->redirect(['/']);

      $command = Yii::$app->db->createCommand('select * from player_spin WHERE player_id=:player_id');
      $playerSpin=$command->bindValue(':player_id',$profile->player_id)->query()->read();
      $model=\app\models\Stream::find()->select('stream.*,TS_AGO(ts) as ts_ago')
      ->where(['player_id'=>$profile->player_id])
      ->orderBy(['ts'=>SORT_DESC]);
      $streamProvider = new ActiveDataProvider([
          'query' => $model,
          'pagination' => [
              'pageSizeParam'=>'stream-perpage',
              'pageParam'=>'stream-page',
              'pageSize' => 10,
          ]
        ]);

        $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM (SELECT t.*,inet_ntoa(t.ip) as ipoctet,count(distinct t2.id) as total_treasures,count(distinct t4.treasure_id) as player_treasures, count(distinct t3.id) as total_findings, count(distinct t5.finding_id) as player_findings FROM target AS t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=:player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=:player_id GROUP BY t.id HAVING player_treasures>0 or player_findings>0 ORDER BY t.ip,t.fqdn,t.name) as tt'
        , [':player_id' => $profile->player_id])->queryScalar();


        $targetProgressProvider = new SqlDataProvider([
            'sql' => 'SELECT t.*,inet_ntoa(t.ip) as ipoctet,count(distinct t2.id) as total_treasures,count(distinct t4.treasure_id) as player_treasures, count(distinct t3.id) as total_findings, count(distinct t5.finding_id) as player_findings FROM target AS t left join treasure as t2 on t2.target_id=t.id left join finding as t3 on t3.target_id=t.id LEFT JOIN player_treasure as t4 on t4.treasure_id=t2.id and t4.player_id=:player_id left join player_finding as t5 on t5.finding_id=t3.id and t5.player_id=:player_id GROUP BY t.id HAVING player_treasures>0 or player_findings>0 ORDER BY t.ip,t.fqdn,t.name',
            'params' => [':player_id' => $profile->player_id],
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => [
                    'name',
                    'fqdn',
                    'ip',
                ],
            ],
        ]);

        return $this->render('index',[
          'profile'=>$profile,
          'playerSpin'=>$playerSpin,
          'streamProvider'=>$streamProvider,
          'accountForm'=>null,
          'profileForm'=>null,
          'targetProgressProvider'=>$targetProgressProvider
        ]);
    }

    public function actionUpdate()
    {
        $profile=$this->findModel(Yii::$app->user->id);

        $errors=$success=null;
        $profileForm=$profile;
        $profileForm->scenario='me';
        die(var_dump(Yii::$app->request->post()));
        if ($profileForm->load(Yii::$app->request->post()) && $profileForm->save())
          $success[]="Profile updated";
        else
          $errors[]='Failed to update profile';

        $accountForm=$profile->owner;
        $accountForm->scenario='profile';
        if ($accountForm->load(Yii::$app->request->post()) && $accountForm->save())
          $success[]="Player updated";
        else
          $errors[]='Failed to update player';

        if($errors!==null)
          Yii::$app->session->setFlash('error',$errors);
        if($success!==null)
          Yii::$app->session->setFlash('success',$errors);

        return $this->render('index',[
          'profile'=>$profile,
          'playerSpin'=>$playerSpin,
          'streamProvider'=>$streamProvider,
          'accountForm'=>$accountForm,
          'profileForm'=>$profileForm,
        ]);
    }
    public function actionOvpn()
  	{
  		$model = Yii::$app->user->identity->sSL;
  		$content=\Yii::$app->view->renderFile('@app/views/profile/ovpn.php',array('model'=>$model),true);
      \Yii::$app->response->data=$content;
      \Yii::$app->response->setDownloadHeaders('echoCTF.ovpn','application/octet-stream',false,strlen($content));
      return Yii::$app->response->send();

  	}

    /**
     * Finds the Profile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Profile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Profile::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
