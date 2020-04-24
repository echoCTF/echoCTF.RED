<?php
/**
 * @author Pantelis Roditis <proditis@echothrust.com>
 * @copyright 2019
 * @since 0.1
 */

namespace app\commands;

use Yii;
use yii\console\Exception as ConsoleException;
use yii\helpers\Console;
use yii\console\Controller;
use app\modules\settings\models\Sysconfig;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\PlayerIp;
use app\modules\frontend\models\PlayerSsl;
/**
 * Manages users.
 *
 * @author proditis
 */
class PlayerController extends Controller {

  /**
   * Player list.
   * @param string $filter filter: all, enabled, disabled.
   */
  public function actionIndex($filter = 'all')
  {
      $filters = ['all', 'active', 'inactive'];
      if (!in_array($filter, $filters)) {
          throw new ConsoleException(Yii::t('app', 'Filter accepts values: {values}', ['values' => implode(',', $filters)]));
      }

      $players = Player::find();
      switch ($filter) {
          case 'active':
              $players->where(['active' => 1]);
              break;

          case 'inactive':
              $players->where(['active' => 0]);
              break;

      }

      $this->playerList($players->all());
  }

  /**
   * Regenerate player activkeys
   */
   public function actionGenerateTokens($filter = 'all')
   {
       $filters = ['all', 'active', 'inactive'];
       if (!in_array($filter, $filters)) {
           throw new ConsoleException(Yii::t('app', 'Filter accepts values: {values}', ['values' => implode(',', $filters)]));
       }

       $players = Player::find();
       switch ($filter) {
           case 'active':
               $players->where(['active' => 1]);
               break;

           case 'inactive':
               $players->where(['active' => 0]);
               break;

       }

       foreach($players->all() as $player)
       {
        $player->activkey=substr(hash('sha512',Yii::$app->security->generateRandomString(64)),0,32);;
        if(!$player->save())
        {
          throw new ConsoleException('Failed to save player:'.$player->username.'. '.$player->getErrors());
        }
       }
   }

  /**
   * @param Player[] $players
   */
  protected function playerList(array $players)
  {
      if (empty($players)) {
          return $this->p('No players found.');
      }

      $this->stdout(sprintf("%4s %-32s %-24s %-16s %-8s\n", 'ID', 'Email address', 'User name', 'Created', 'Status'), Console::BOLD);
      $this->stdout(str_repeat('-', 94) . PHP_EOL);

      foreach ($players as $player) {
          printf("%4d %-32s %-24s %-16s %-8s\n",
                  $player->id,
                  $player->email,
                  $player->username,
                  $player->created,
                  $player->active == 1 ? 'ACTIVE' : 'INACTIVE'
          );
      }
  }

  /*
    Mail Users their activation URL
  */
  public function actionMail($baseURL="https://echoctf.red/index.php?r=site/activate&key=", $active=false,$email=false)
  {
    // Get innactive players
    $failedSend=$okSend=[];
    if($email!==false)
    {
      $players=Player::find()->where(['active'=>$active,'email'=>trim(str_replace(array("\xc2\xa0","\r\n","\r"),"",$email))])->all();
      $this->stdout("Mailing user: ".trim(str_replace(array("\xc2\xa0","\r\n","\r"),"",$email))."\n", Console::BOLD);
    }
    else
    {
      $players=Player::find()->where(['active'=>$active])->all();
      $this->stdout("Mailing Registered users:\n", Console::BOLD);
    }
    $event_name=Sysconfig::findOne('event_name')->val;
    if(Sysconfig::findOne('mail_host'))
      \Yii::$app->mailer->transport->setHost(Sysconfig::findOne('mail_host')->val);

    if(Sysconfig::findOne('mail_port'))
      \Yii::$app->mailer->transport->setPort(Sysconfig::findOne('mail_port')->val);

    if(Sysconfig::findOne('mail_username'))
      \Yii::$app->mailer->transport->setUserName(Sysconfig::findOne('mail_username')->val);

    if(Sysconfig::findOne('mail_password'))
      \Yii::$app->mailer->transport->setPassword(Sysconfig::findOne('mail_password')->val);

    foreach($players as $player)
    {
      // Generate activation URL
      $activationURL=sprintf("%s%s",$baseURL,$player->activkey);
      $MAILCONTENT=$this->renderFile('mail/layouts/activation.php', ['activationURL'=>$activationURL,'player'=>$player,'event_name'=>$event_name], true);
      $this->stdout($player->email);
      $numSend=\Yii::$app->mailer->compose()
          ->setFrom([Sysconfig::findOne('mail_from')->val => Sysconfig::findOne('mail_fromName')->val])
          ->setTo($player->email)
          ->setSubject(Sysconfig::findOne('event_name')->val.' account activation details')
          ->setTextBody($MAILCONTENT)
          ->send();
      $this->stdout($numSend? " Ok\n": "Not Ok\n");
    }
  }

  /*
    Register Users and generate OpenVPN keys and settings
  */
  public function actionRegister($username,$email,$fullname,$password=false,$player_type="offense",$active=false,$academic=false,$team_name=false, $baseIP="10.10.0.0",$skip=0)
  {
    $player_network=ip2long($baseIP)+((Player::find()->count()+$skip)*4);
    echo "Registering: ", $email,"\n";
    echo "Player Network: ",long2ip($player_network),"\n";
    $trans=Yii::$app->db->beginTransaction();
    try {
      $player=new Player;
//      $player->id=$player_network+1;
      $player->academic=intval(boolval($academic));
      $player->username=trim(str_replace(array("\xc2\xa0","\r\n","\r"),"",$username));
      $player->email=trim(str_replace(array("\xc2\xa0","\r\n","\r"),"",$email));
      $player->fullname=trim(str_replace(array("\xc2\xa0","\r\n","\r"),"",$fullname));
      $player->type=$player_type;
      if($password===false or $password==='0')
      {

        $password=Yii::$app->security->generateRandomString(8);
        printf("Autogenerated password: %s\n",$password);
      }

      $player->password_hash = Yii::$app->security->generatePasswordHash($password);
      $player->password = Yii::$app->security->generatePasswordHash($password);

      $player->active=intval($active);
      $player->status=10;
      $player->auth_key = Yii::$app->security->generateRandomString();
      $player->activkey=Yii::$app->security->generateRandomString(20);
      if(!$player->save())
        throw new ConsoleException('Failed to save player:'.$player->username.'. '.$player->getErrors());
      $playerSsl=new PlayerSsl();
      $playerSsl->player_id=$player->id;
      $playerSsl->generate();
      if($playerSsl->save()!==false)
        $playerSsl->refresh();

      if($team_name!==false)
      {
        $team=Team::findOne(['name'=>$team_name]);
        if(!$team)
        {
          $team=new Team();
          $team->name=$team_name;
          $team->academic=$academic;
          $team->token=Yii::$app->security->generateRandomString(20);
          $team->owner_id=$player->id;
          $team->description=$player->username;
          $team->save(false);
        }

        if($team && $team->id && $team->owner_id!=$player->id)
        {
          $tp=new TeamPlayer;
          $tp->player_id=$player->id;
          $tp->team_id=$team->id;
          $tp->approved=0;
          if(!$tp->save())
            printf("Error saving team player\n");
        }
      }

//      $pi=new PlayerIp();
//      $pi->player_id=$player->id;
//      $pi->ipoctet=long2ip($player->id);
//      if(!$pi->save())
//        printf("Error saving Player IP\n");
      $trans->commit();
    }
    catch (\Exception $e)
    {
      print $e->getMessage();
      $trans->rollback();
    }
  }

  /**
   * Change password for a userid or email
   */
  public function actionPassword($emailORid,$password)
  {
    $p=Player::find();
    if(intval($emailORid)===-1)
      return Player::updateAll(['password'=>Yii::$app->security->generatePasswordHash($password)])===false;
    if($emailORid==='all')
      $players=$p->all();
    else
      $players=$p->where(['id'=>$emailORid])->orWhere(['email'=>$emailORid])->all();
    $trans=Yii::$app->db->beginTransaction();
    try {
      foreach($players as $player)
      {
        $player->password=Yii::$app->security->generatePasswordHash($password);
        if(!$player->update(['password']))
          $this->p("Failed to change password for [{player}]",['player'=>$player->username]);
        else
          $this->p("Password for [{player}] changed",['player'=>$player->username]);
      }
      $trans->commit();
    }
    catch (\Exception $e)
    {
      print $e->getMessage();
      $trans->rollback();
    }
  }

  public function p($message, array $params = [])
  {
      $this->stdout(Yii::t('app', $message, $params) . PHP_EOL);
  }
}
