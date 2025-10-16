<?php

namespace app\modules\gameplay\models;

use Yii;
use Docker\Docker;
use Docker\DockerClientFactory;
use Docker\API\Model\RestartPolicy;
use Docker\API\Model\HostConfig;
use Docker\API\Model\NetworkingConfig;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\EndpointSettings;
use Docker\API\Model\EndpointIPAMConfig;
use Docker\API\Exception\ImageCreateNotFoundException;
use Docker\API\Exception\ContainerCreateNotFoundException;
use Docker\API\Exception\ContainerStartNotFoundException;
use Docker\API\Exception\ContainerStartInternalServerErrorException;
use app\modules\activity\models\SpinQueue;
use app\modules\activity\models\Headshot;
use Docker\API\Model\AuthConfig;
use app\modules\infrastructure\models\DockerInstance;
use \yii\helpers\Html as H;
use app\components\WebhookTrigger as Webhook;
use yii\base\UserException;
use yii\helpers\Url;
/**
 * This is the model class extends the default one for "target"
 * with extra methods.
 *
 */
class Target extends TargetAR
{
    private $difficulties=[
      0=>"beginner",
      1=>"basic",
      2=>"intermediate",
      3=>"advanced",
      4=>"expert",
      5=>"guru",
      6=>"insane"
    ];
    private $container;

    const EVENT_NEW_TARGET_ANNOUNCEMENT="event_new_target_announcement";

    public function init()
    {
        $this->on(self::EVENT_NEW_TARGET_ANNOUNCEMENT, [$this, 'addNews']);
        parent::init();
    }


    public function dockerSpin()
    {
      $targetVariables=null;
      $docker=$this->connectAPI();
      if($docker===false)
        throw new UserException("Failed to connect to docker server: ".$this->server);

      $this->destroy();

      $hostConfig=$this->hostConfig();
      // We set the memory here again for backwards compatibility
      $hostConfig->setMemory($this->memory);
      $containerConfig=new ContainersCreatePostBody();
      $endpointSettings=new EndpointSettings();
      $endpointIPAMConfig=new EndpointIPAMConfig();
      $endpointIPAMConfig->setIPv4Address($this->ipoctet);
      $endpointSettings->setIPAMConfig($endpointIPAMConfig);

      $nwc=new NetworkingConfig();
      $nwc->setEndpointsConfig(new \ArrayObject([
        $this->net => $endpointSettings
      ]));
      $containerConfig->setNetworkingConfig($nwc);
      $containerConfig->setHostname($this->fqdn);
      $containerConfig->setImage($this->image);
      $containerConfig->setOpenStdin(true);
      $containerConfig->setTty(true);
      $containerConfig->setAttachStdin(true);
      $containerConfig->setAttachStdout(true);
      $containerConfig->setAttachStderr(true);

      foreach($this->targetVariables as $var)
        $targetVariables[]=sprintf("%s=%s", $var->key, $var->val);
      $containerConfig->setEnv($targetVariables);
      $containerConfig->setHostConfig($hostConfig);

      $this->pull();
      $containerCreateResult=$docker->containerCreate($containerConfig, ['name'=>$this->name]);
      $docker->containerStart($containerCreateResult->getId());

      return true;
    }

    public function pveSpin($server)
    {

      $HEADERS=['Accept: application/json', 'Content-Type: application/json'];
      if($this->imageparams!=='')
      {
        $decoded=json_decode($this->imageparams);
        foreach($decoded as $key => $val)
        {
          $HEADERS[]=$key.': '.$val;
        }
      }

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_HTTPHEADER, $HEADERS);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
      foreach([ 'status/stop' ,'snapshot/echoCTF/rollback','status/start'] as $action)
      {
        curl_setopt($ch, CURLOPT_URL,$server.$action);
        if(curl_exec($ch)===false)
          var_dump(curl_error($ch));
        curl_close($ch);
        sleep(1);
      }
      return true;
    }
    /**
     * Spin the target up
     */
    public function spin()
    {
      // if tcp do docker staff
      if(substr($this->server,0,3)=='tcp')
      {
        return $this->dockerSpin();
      }
      else if(substr($this->server,0,3)=='pve')
      {
        $this->pveSpin(substr($this->server,4));
      }
    }

    /**
     * Prepare hostConfig docker property
     */
    public function hostConfig()
    {
      $targetVolumes=null;
      $restartPolicy=new RestartPolicy();
      $restartPolicy->setName('always');
      $hostConfig=new HostConfig();
      $decoded=\yii\helpers\Json::decode($this->parameters, true);
      if($decoded !== null && array_key_exists('hostConfig',$decoded))
      {
        foreach($decoded['hostConfig'] as $key=>$val)
          $hostConfig->{"set".$key}($val);
      }

      $hostConfig->setNetworkMode($this->net);
      $hostConfig->setDns([$this->dns]);
      foreach($this->targetVolumes as $var)
        $targetVolumes[]=sprintf("%s:%s", $var->volume, $var->bind);
      $hostConfig->setBinds($targetVolumes);

      $hostConfig->setRestartPolicy($restartPolicy);
      return $hostConfig;
    }

    /**
     * @container method
     * Connect to the given docker and return a docker ContainerManager
     * @returns Docker\Docker\ContainerManager|int
     */
    public function getContainer()
    {
      try
      {
        $docker=$this->connectAPI();
        if($docker->systemPing())
          $this->container=$docker->getContainerManager()->find($this->name);
      }
      catch(\Exception $e)
      {
        $this->container=1;
      }
      return $this->container;
    }

    public function getFindingPoints()
    {
      return (int) (new \yii\db\Query())->from('finding')->where(['target_id'=>$this->id])->sum('points');
    }

    public function getTreasurePoints()
    {
      return (int) (new \yii\db\Query())->from('treasure')->where(['target_id'=>$this->id])->sum('points');
    }

    /**
     * Instruct the docker server to pull the image
     */
    public function pull()
    {
      if($this->server == null) return false;
      if(($docker=$this->connectAPI())===false)
        return false;
      $authHeaders=[];
      if(!empty($this->imageparams))
      {
        $registryConfig = new AuthConfig();
        $decoded=\yii\helpers\Json::decode($this->imageparams, false);

        if($decoded !== null && property_exists($decoded, 'username'))
        {
          $registryConfig->setUsername($decoded->username);
        }

        if($decoded !== null && property_exists($decoded, 'password'))
        {
          $registryConfig->setPassword($decoded->password);
        }

        if($decoded !== null && property_exists($decoded, 'email'))
        {
          $registryConfig->setEmail($decoded->email);
        }
        $authHeaders['X-Registry-Auth']=$registryConfig;
      }
      $imageCreateResult=$docker->imageCreate($this->image, ['fromImage'=>$this->image],$authHeaders);
      $imageCreateResult->wait();
      return true;
    }

    public function dockerDestroy()
    {
      $docker=$this->connectAPI();
      if($docker===false)
        return false;

      try
      {
        $docker->containerDelete($this->name, ['force'=>true]);
      }
      catch(\Exception $e)
      {
        return false;
      }
      return true;
    }
    /**
     * Destroy the remote container
     */
    public function destroy()
    {
      if(substr($this->server,0,3)==='tcp')
      {
        return $this->dockerDestroy();
      }
      else if(substr($this->server,0,3)==='pve')
      {
        $HEADERS=['Accept: application/json', 'Content-Type: application/json'];
        if($this->imageparams!=='')
        {
          $decoded=json_decode($this->imageparams);
          foreach($decoded as $key => $val)
          {
            $HEADERS[]=$key.': '.$val;
          }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $HEADERS);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
        curl_setopt($ch, CURLOPT_URL,substr($this->server,4).'status/stop' );
        if(curl_exec($ch)===false)
          var_dump(curl_error($ch));
        curl_close($ch);
      }
    }

  /**
   * Get memory from parameters
   */
  public function getMemory()
  {
    if($this->parameters !== NULL)
    {
      $decoded=\yii\helpers\Json::decode($this->parameters, false);
      if($decoded !== null && property_exists($decoded, 'hostConfig') && property_exists($decoded->hostConfig, 'Memory'))
        return intval($decoded->hostConfig->Memory)<(16*1024*1024)? intval($decoded->hostConfig->Memory) * 1024 * 1024 : intval($decoded->hostConfig->Memory);
    }
    return null;
  }

  public function connectAPI($params=null)
  {

    if($params===null)
    {
      if($this->server!==null)
      {
        $params['remote_socket']=$this->server;
        $params['ssl']=false;
        $params['timeout']=5000;
      }
    }

    try
    {
      $client=DockerClientFactory::create($params);
      return Docker::create($client);
    }
    catch(\Exception $e)
    {
      return false;
    }

  }

  /**
  * Perform powerdown of a target
  */
  public function powerdown()
  {
    if($this->destroy())
    {
      $this->status='offline';
      $this->scheduled_at=null;
      $this->active=0;
      return $this->save();
    }
    return false;
  }

  public function powerup()
  {

  }

  public function addNews()
  {
    if($this->network!==null && ($this->network->announce===false || $this->network->active===false)) return ;
    $news=new \app\modules\content\models\News;
    $news->title=sprintf(\Yii::t('app',"New target %s added"),$this->name);
    $news->category=H::img("/images/news/category/new-target.svg",['width'=>'25px']);
    $body=sprintf(\Yii::t('app',"Just a heads up, new target [%s]"),H::a($this->name,'/target/'.$this->id));
    $bodyPlain=sprintf(\Yii::t('app',"Hey @everyone, just a heads up, new target [**%s**] => https://%s/target/%d"),$this->name,Yii::$app->sys->offense_domain,$this->id);
    if($this->network!==null)
    {
      $bodyPlain=sprintf(\Yii::t('app',"%s got added to the [**%s**]"),$bodyPlain,$this->network->name);
      $news->body=sprintf(\Yii::t('app',"%s, got added in the [%s]"),$body,H::a($this->network->name,'/network/'.$this->network->id));
    }
    else
    {
      $bodyPlain=sprintf(\Yii::t('app',"%s, got added"),$bodyPlain);
      $news->body=sprintf(\Yii::t('app',"%s, got added"),$body);
    }

    if($this->scheduled_at && $this->status=='powerup')
    {
      $bodyPlain.=sprintf(\Yii::t('app'," and will become available at %s."),$this->scheduled_at);
      $news->body.=sprintf(\Yii::t('app'," and will become available at %s."),$this->scheduled_at);
    }
    elseif($this->status=='online')
    {
      $bodyPlain.=sprintf(\Yii::t('app'," and is now available."));
      $news->body.=sprintf(\Yii::t('app'," and is now available."));
    }

    if(Yii::$app->sys->discord_news_webhook!==false)
    {
      $bodyPlain.="\n\nWe hope you enjoy and as always Happy Hacking :heart:";
      $data['content']=$bodyPlain;
      $client = new Webhook(['url' => Yii::$app->sys->discord_news_webhook,'data'=>json_encode($data)]);
      $client->run();
    }

    if($news->save()===false)
      throw new \Exception('Failed to create news entry.');
  }

  public function getDifficultyString()
  {
    return $this->difficulties[$this->difficulty];
  }
}
