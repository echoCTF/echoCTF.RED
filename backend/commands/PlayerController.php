<?php
/**
 * @author Pantelis Roditis <proditis@echothrust.com>
 * @copyright 2019
 * @since 0.1
 */

namespace app\commands;

use app\modules\activity\models\PlayerVpnHistory;
use Yii;
use yii\console\Exception as ConsoleException;
use yii\helpers\Console;
use yii\console\Controller;
use app\modules\settings\models\Sysconfig;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\Profile;
use app\modules\frontend\models\Team;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\PlayerIp;
use app\modules\frontend\models\PlayerSsl;
use yii\helpers\ArrayHelper;
use yii\console\widgets\Table;

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
  public function actionIndex($filter='all')
  {
      $filters=['all', 'active', 'inactive'];
      if(!in_array($filter, $filters))
      {
          throw new ConsoleException(Yii::t('app', 'Filter accepts values: {values}', ['values' => implode(',', $filters)]));
      }

      $players=Player::find();
      switch($filter) {
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
    public function actionGenerateActivKeys($filter='all')
    {
        $filters=['all', 'active', 'inactive'];
        if(!in_array($filter, $filters))
        {
            throw new ConsoleException(Yii::t('app', 'Filter accepts values: {values}', ['values' => implode(',', $filters)]));
        }

        $players=Player::find();
        switch($filter) {
            case 'active':
               $players->where(['active' => 1]);
                break;

            case 'inactive':
               $players->where(['active' => 0]);
                break;

        }

        foreach($players->all() as $player)
        {
        $player->activkey=substr(hash('sha512', Yii::$app->security->generateRandomString(64)), 0, 32);;
        if(!$player->save())
        {
          throw new ConsoleException('Failed to save player:'.$player->username.'. '.implode(', ', $player->getErrors()));
        }
        }
    }

  /**
   * Regenerate player auth keys
   */
  public function actionGenerateAuthKeys($filter='all')
  {
      $filters=['all', 'active', 'inactive'];
      if(!in_array($filter, $filters))
      {
          throw new ConsoleException(Yii::t('app', 'Filter accepts values: {values}', ['values' => implode(',', $filters)]));
      }

      $players=Player::find();
      switch($filter) {
          case 'active':
             $players->where(['active' => 1]);
              break;

          case 'inactive':
             $players->where(['active' => 0]);
              break;

      }

      foreach($players->all() as $player)
      {
      $player->auth_key=Yii::$app->security->generateRandomString();
      if(!$player->save())
      {
        throw new ConsoleException('Failed to save player:'.$player->username.'. '.implode(', ', $player->getErrors()));
      }
      }
  }

  /**
   * @param Player[] $players
   */
  protected function playerList(array $players)
  {
      if(empty($players))
      {
          $this->p('No players found.');
          return;
      }

      $this->stdout(sprintf("%4s %-32s %-24s %-16s %-8s\n", 'ID', 'Email address', 'User name', 'Created', 'Status'), Console::BOLD);
      $this->stdout(str_repeat('-', 94).PHP_EOL);

      foreach($players as $player)
      {
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
  public function actionMail($active=false, $email=false,$status=9)
  {
    // Get innactive players
    if($email !== false)
    {
      $players=Player::find()->where(['active'=>$active, 'status'=>$status, 'email'=>trim(str_replace(array("\xc2\xa0", "\r\n", "\r"), "", $email))])->all();
      $this->stdout("Mailing user: ".trim(str_replace(array("\xc2\xa0", "\r\n", "\r"), "", $email))."\n", Console::BOLD);
    }
    else
    {
      $players=Player::find()->where(['active'=>$active,'status'=>$status])->all();
      $this->stdout("Mailing Registered users:\n", Console::BOLD);
    }
    $event_name=Sysconfig::findOne('event_name')->val;

    foreach($players as $player)
    {
      // Generate activation URL
      $activationURL=sprintf("https://%s/verify-email?token=%s",\Yii::$app->sys->offense_domain, $player->verification_token);

      $this->stdout($player->email);
      $numSend=Yii::$app
        ->mailer
        ->compose(
                  ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                  ['user' => $player,'verifyLink'=>$activationURL]
        )
        ->setFrom([Yii::$app->sys->mail_from => Yii::$app->sys->mail_fromName])
        ->setBcc([Yii::$app->sys->mail_from => Yii::$app->sys->mail_fromName])
        ->setTo([$player->email => $player->fullname])
        ->setSubject(trim(Yii::$app->sys->event_name). ' Account approved')
        ->send();
      $this->stdout($numSend ? " Ok\n" : "Not Ok\n");
    }
  }

  /*
    Register Users and generate OpenVPN keys and settings
  */
  public function actionRegister($username, $email, $fullname, $password=false, $player_type="offense", $active=false, $academic=false, $team_name=false, $approved=0)
  {
    echo "Registering: ", $email, "\n";
    $trans=Yii::$app->db->beginTransaction();
    try
    {
      $player=new Player;
      $player->academic=intval($academic);
      if($username==='')
        $player->username=substr(md5($email),0,10);
      else
        $player->username=trim(str_replace(array("\xc2\xa0", "\r\n", "\r"), "", $username));

      $player->email=trim(str_replace(array("\xc2\xa0", "\r\n", "\r"), "", $email));
      $player->fullname=trim(str_replace(array("\xc2\xa0", "\r\n", "\r"), "", $fullname));
      $player->type=$player_type;
      if($password==="")
        $password=false;
      $password=$player->genPassword($password);

      $player->password_hash=Yii::$app->security->generatePasswordHash($password);
      $player->password=Yii::$app->security->generatePasswordHash($password);

      $player->active=intval($active);
      $player->status=10;
      if(!$player->active)
      {
        $player->verification_token=str_replace('_','-',Yii::$app->security->generateRandomString().'-'.(time()));
        $player->status=9;
      }

      $player->auth_key=Yii::$app->security->generateRandomString();
      //$player->activkey=Yii::$app->security->generateRandomString(20);

      if(!$player->saveWithSsl())
      {
        print_r($player->getErrors());
        throw new ConsoleException('Failed to save player:'.$player->username);
      }

      $player->createTeam($team_name,$approved);
      $trans->commit();
    }
    catch(\Exception $e)
    {
      print $e->getMessage();
      $trans->rollback();
    }
  }

  /**
   * Change password for a userid or email
   */
  public function actionPassword($emailORid, $password)
  {
    $p=Player::find();
    if(intval($emailORid) === -1)
      return Player::updateAll(['password'=>Yii::$app->security->generatePasswordHash($password)]) === false;
    if($emailORid === 'all')
      $players=$p->all();
    else
      $players=$p->where(['id'=>$emailORid])->orWhere(['email'=>$emailORid])->all();
    $trans=Yii::$app->db->beginTransaction();
    try
    {
      foreach($players as $player)
      {
        $player->password=Yii::$app->security->generatePasswordHash($password);
        if(!$player->update(true, ['password']))
          $this->p("Failed to change password for [{player}]", ['player'=>$player->username]);
        else
          $this->p("Password for [{player}] changed", ['player'=>$player->username]);
      }
      $trans->commit();
    }
    catch(\Exception $e)
    {
      print $e->getMessage();
      $trans->rollback();
    }
  }

  /**
   * List number of pending avatars.
   * @param boolean $full full listing of pending avatar profile ids and profile owner
   */
  public function actionPendingAvatars($full=false)
  {
    $pendingProfiles=Profile::find()->where(['approved_avatar'=>false]);
    if($full!==false)
    {
      foreach($pendingProfiles->all() as $p)
      {
        echo $p->id," ",$p->owner->username,"\n";
      }
    }
    else if($pendingProfiles->count()>0)
      echo $pendingProfiles->count()," pending profile avatar",$pendingProfiles->count()>1 ? 's': '',"\n";
  }

  /**
   * Check mails for known spammers.
   */
  public function actionCheckStopforumspam($interval=null,$confidence=90)
  {
    $players=Player::find();
    if($interval!==null)
    {
      $exp=new \yii\db\Expression('created >= NOW() - INTERVAL '.$interval);
      $players->where($exp);
    }
    foreach($players->all() as $p)
    {
      $SFS=new \app\components\StopForumSpam();
      //echo "Processing ",$p->email,"\n";
      $SFS->email=$p->email;
      $result=$SFS->check();
      $retData=json_decode($result)->email;
      if(property_exists($retData,'confidence') && $retData->confidence>=intval($confidence))
      {
        printf("Banning %d: %s %d %d => %s\n",$p->id,$p->email,$p->active,$p->status,floatval($retData->confidence));
        $p->ban();
      }

    }
  }

  /**
   * Check mails for known spam and disposable hosters.
   */
  public function actionCheckSpammy($domains=false)
  {
    $skip_domains=[];
    $players=Player::find()->select(["SUBSTRING_INDEX(email,'@',-1) as email"])->distinct();
    foreach($skip_domains as $d)
      $players->andWhere(['not like','email', $d]);
      echo "Found ",$players->count()," distinct domains.\n";
    foreach($players->all() as $p)
    {
      try{
        $DNS_NS=dns_get_record($p->email, DNS_NS);
        $DNS_MX=dns_get_record($p->email, DNS_MX);
        $DNS_A=dns_get_record($p->email, DNS_A);
      }
      catch(\Exception $e)
      {
        echo "Error: Failed to resolve [",$p->email,"]",$e->getMessage(),"\n";
      }
      if($DNS_NS===[] && $DNS_MX===[])
      {
        echo "Domain[",$p->email,"] has empty MX & NS records\n";
      }
      $validator = new \app\components\validators\MXServersValidator();
      $validator->mxonly=true;
      if (!$validator->validate($p->email, $error)) {
        echo "Domain[",$p->email,"] MX Validator error\n";
      }
    }
  }

  /**
   * Check users with duplicate IP
   */
  public function actionCheckDupips($skip_uids=false)
  {
    $table="player_ips_".substr(md5(Yii::$app->security->generateRandomString(32)),0,12);
    echo "Using table $table\n";
    $populate_ips[]="create table $table (id integer unsigned not null, ip integer unsigned not null, primary key (id,ip)) engine=memory";
    $populate_ips[]="INSERT IGNORE INTO $table SELECT id,signup_ip FROM player_last WHERE signup_ip IS NOT null";
    $populate_ips[]="INSERT IGNORE INTO $table SELECT id,signin_ip FROM player_last WHERE signin_ip IS NOT null";
    $populate_ips[]="INSERT IGNORE INTO $table SELECT player_id as id,vpn_remote_address FROM player_vpn_history WHERE 1=1";
    foreach($populate_ips as $query)
    {
      if(substr($query,0,6)=="INSERT" && $skip_uids!==false)
        $query.=" AND id not in ($skip_uids)";
      Yii::$app->db->createCommand($query)->execute();
    }
    $offenders=Yii::$app->db->createCommand("select inet_ntoa(ip) as IP,group_concat(id) as players from $table group by ip having count(id)>1")->queryAll();
    Yii::$app->db->createCommand("DROP TABLE $table")->execute();
    foreach($offenders as $offense)
    {
        $players=ArrayHelper::getColumn(Player::find()->where("id in (".$offense['players'].")")->all(),'username');
        printf("%s => %s\n",$offense['IP'],implode(',',$players));
    }
  }

  public function actionFailValidation($delete=false)
  {
    $allRecords=Player::find()->where(['status'=>10])->all();
    foreach($allRecords as $p)
    {
        $p->scenario='validator';
        if(!$p->validate('email'))
        {
          echo Table::widget([
            'headers' =>['Error for ID: '.$p->id, 'Description'],
            'rows' => $this->getErrorRows($p),
          ]);
          if($delete!==false)
          {
            $p->delete();
            echo $p->id," Deleted\n";
          }
        }
    }
  }

  public function actionFailValidationProfiles($fix=false)
  {
    $allRecords=Profile::find()->all();
    $fields=['twitter','youtube','htb','discord','github'];
    foreach($allRecords as $p)
    {
        $p->scenario='validator';
        if(!$p->validate())
        {
          echo Table::widget([
            'headers' =>['Error for ID: '.$p->id, 'Description'],
            'rows' => $this->getErrorRows($p),
          ]);
          if($fix!==false)
          {
            foreach($p->getErrors() as $attribute => $errors)
            {
                if($attribute==='twitter' && $p->twitter[0]==='@')
                {
                    $p->twitter=str_replace('@','',$p->twitter);
                }
                elseif(array_search($attribute,$fields)!==false)
                {
                    $p->$attribute=null;
                }
                else
                    printf("Failing attribute not on the list %a",$attribute);
            }
            $p->save();
            echo $p->id," fixed\n";
          }
        }
    }
  }

  /**
   * Penalize a player that is using a writeup hoarder and disable access to the hoarder. The user is penalized with a 1/3 reduction of points and headshot timers zeroed out.
   * @param integer $hoarder_id the ID of the writeup hoarder
   * @param integer $beneficiary_id the ID of the beneficiary
   */
  public function actionPenalizeWithHoarder($hoarder_id,$beneficiary_id)
  {
    Yii::$app->db->createCommand("update ignore player_target_help set player_id=:beneficiary_id where player_id=:hoarder_id",[':beneficiary_id'=>$beneficiary_id,':hoarder_id'=>$hoarder_id])->execute();
    Yii::$app->db->createCommand("update player SET status=0, active=0 WHERE id=:player_id",[':player_id'=>$hoarder_id])->execute();
    Yii::$app->db->createCommand("update headshot set timer=0 WHERE player_id=:player_id",[':player_id'=>$beneficiary_id])->execute();
    Yii::$app->db->createCommand("SET @TRIGGER_CHECKS=FALSE")->execute();
    Yii::$app->db->createCommand("update stream set points=round(points/3) WHERE player_id=:player_id AND points>0",[':player_id'=>$beneficiary_id])->execute();
    Yii::$app->db->createCommand("update player_score set points=(SELECT sum(points) from stream where player_id=:player_id) WHERE player_id=:player_id",[':player_id'=>$beneficiary_id])->execute();
    Yii::$app->db->createCommand("SET @TRIGGER_CHECKS=TRUE")->execute();
  }

  public function p($message, array $params=[])
  {
      $this->stdout(Yii::t('app', $message, $params).PHP_EOL);
  }

  private function getErrorRows($model)
  {
    $errors=$model->getErrors();
    $errorows=null;
    foreach($errors as $field => $errstrs)
    {
      $errorows[]=[$model->{$field}, implode(" ",$errstrs)];
    }
    return $errorows;
  }

}
