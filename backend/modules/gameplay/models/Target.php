<?php

namespace app\modules\gameplay\models;

use Yii;
use Docker\Docker;
use Docker\DockerClientFactory;
use Docker\API\Model\RestartPolicy;
use Docker\API\Model\HostConfig;
use Docker\API\Model\ContainersCreatePostBodyNetworkingConfig;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\EndpointSettings;
use Docker\API\Model\EndpointIPAMConfig;
use Docker\API\Exception\ImageCreateNotFoundException;
use Docker\API\Exception\ContainerCreateNotFoundException;
use Docker\API\Exception\ContainerStartNotFoundException;
use Docker\API\Exception\ContainerStartInternalServerErrorException;
use app\modules\activity\models\SpinQueue;
use app\modules\activity\models\Headshot;


/**
 * This is the model class for table "target".
 *
 * @property int $id target ID
 * @property string $name A name for the target
 * @property string $fqdn The FQDN for the target
 * @property string $purpose The purpose of this target
 * @property string $description
 * @property int $ip The IP of the target
 * @property string $mac The mac associated with this IP
 * @property int $active
 * @property string $status Status for the target (online,offline,powerup,powerdown)
 * @property string $scheduled_at A date to associate with "status" if needed
 * @property string $net Network this pod is attached
 * @property string $server Docker Server connection string.
 * @property string $image
 * @property string $dns
 * @property string $parameters
 * @property int $suggested_xp
 * @property int $required_xp
 * @property int $rootable
 * @property int $difficulty
 * @property bool $timer
 *
 * @property Finding[] $findings
 * @property TargetVariable[] $targetVariables
 * @property TargetVolume[] $targetVolumes
 * @property Treasure[] $treasures
 * @property Headshot[] $headshots
 * @property int $memory
 */
class Target extends TargetAR
{
    private $container;

    public function spin()
    {
      $targetVariables=null;
      if($this->server == null) return false;
      $docker=$this->connectAPI();
      $this->destroy();

      $hostConfig=$this->hostConfig();

      $containerConfig=new ContainersCreatePostBody();
      $endpointSettings=new EndpointSettings();
      $endpointIPAMConfig=new EndpointIPAMConfig();
      $endpointIPAMConfig->setIPv4Address($this->ipoctet);// target->ipoctet
      $endpointSettings->setIPAMConfig($endpointIPAMConfig);

      $nwc=new ContainersCreatePostBodyNetworkingConfig();
      $nwc->setEndpointsConfig(new \ArrayObject([
        $this->net => $endpointSettings
      ]));
      $containerConfig->setNetworkingConfig($nwc);
      $containerConfig->setHostname($this->fqdn);// target->fqdn
      $containerConfig->setImage($this->image);// target->image
      foreach($this->targetVariables as $var)
        $targetVariables[]=sprintf("%s=%s", $var->key, $var->val);
      $containerConfig->setEnv($targetVariables);// target->targetVariables
      //$containerConfig->setMacAddress($this->mac); // target->mac
      $containerConfig->setHostConfig($hostConfig);

      $this->pull();
      $containerCreateResult=$docker->containerCreate($containerConfig, ['name'=>$this->name]);// target->name

      $docker->containerStart($containerCreateResult->getId());

      return true;
    }

    public function hostConfig()
    {
//      $targetVariables=null;
      $targetVolumes=null;
      $restartPolicy=new RestartPolicy();
      $restartPolicy->setName('always');
      $hostConfig=new HostConfig();
      if($this->memory !== null)
        $hostConfig->setMemory($this->memory);// Set memory limit to 512MB

      $hostConfig->setNetworkMode($this->net);// target->net
      $hostConfig->setDns([$this->dns]);// target->dns
      foreach($this->targetVolumes as $var)
        $targetVolumes[]=sprintf("%s:%s", $var->volume, $var->bind);
      $hostConfig->setBinds($targetVolumes);// target->targetVolumes

      $hostConfig->setRestartPolicy($restartPolicy);
      return $hostConfig;
    }

    public function getContainer()
    {
      try
      {
        $client=DockerClientFactory::create([
          'remote_socket' => $this->server,
          'ssl' => false,
        ]);
        $docker=Docker::create($client);

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

    public function pull()
    {
      if($this->server == null) return false;
      $docker=$this->connectAPI();
      $imageCreateResult=$docker->imageCreate($this->image, ['fromImage'=>$this->image]);
      $imageCreateResult->wait();
      return true;
    }

    public function destroy()
    {
//      $targetVariables=null;
//      $targetVolumes=null;
      if($this->server == null) return false;
      $docker=$this->connectAPI();
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

  public function getMemory()
  {
    if($this->parameters !== NULL)
    {
      $decoded=\yii\helpers\Json::decode($this->parameters, false);
      if($decoded !== null && property_exists($decoded, 'hostConfig') && property_exists($decoded->hostConfig, 'Memory'))
        return intval($decoded->hostConfig->Memory) * 1024 * 1024;
    }
    return null;
  }

  public function connectAPI()
  {
    if($this->server == null) return false;

    try
    {
      $client=DockerClientFactory::create([
        'remote_socket' => $this->server, // target->server
        'ssl' => false,
      ]);
      return Docker::create($client);
    }
    catch(\Exception $e)
    {
      return false;
    }

  }

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
}
