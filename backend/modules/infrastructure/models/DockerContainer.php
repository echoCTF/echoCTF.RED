<?php
namespace app\modules\infrastructure\models;

use yii\base\Model;
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
use Docker\API\Model\AuthConfig;

class DockerContainer extends Model
{
  public $name;
  public $hostname;
  public $ipoctet;
  public $fqdn;
  public $image;
  public $net;
  public $dns;
  public $targetVariables=[];
  public $targetVolumes=[];
  public $mac;
  public $memory;
  public $server;
  public $ssl;
  public $parameters;
  public $imageparams;
  public $timeout=2000;

  private $container;
  private $client;
  private $docker;

  public function init()
  {
    parent::init();
  }
  public function __set($name, $value)
  {
    if(!property_exists($this,$name))
      return;
    parent::__set($name,$value);
  }

  public function setTargetVariables($value)
  {
    if(is_array($value)) $this->targetVariables=$value;
    $this->targetVariables=[];
  }
  public function setTargetVolumes($value)
  {
    if(is_array($value)) $this->targetVolumes=$value;
    $this->targetVolumes=[];
  }

  public function connectAPI()
  {
    if($this->docker!==null) return;

    $params['remote_socket']=$this->server;
    $params['ssl']=$this->ssl;
    $params['timeout']=$this->timeout;

    $this->client=DockerClientFactory::create($params);
    $this->docker=Docker::create($this->client);

  }


  // Spin a container
  public function spin()
  {
    $targetVariables=null;
    $this->connectAPI();
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
    $containerConfig->setOpenStdin(true);
    $containerConfig->setTty(true);
    $containerConfig->setAttachStdin(true);
    $containerConfig->setAttachStdout(true);
    $containerConfig->setAttachStderr(true);

    foreach($this->targetVariables as $var)
      $targetVariables[]=sprintf("%s=%s", $var->key, $var->val);

    $containerConfig->setEnv($targetVariables);
    //$containerConfig->setMacAddress($this->mac); // target->mac
    $containerConfig->setHostConfig($hostConfig);
    $this->container=$this->docker->containerCreate($containerConfig, ['name'=>$this->name]);
    $this->docker->containerStart($this->container->getId());
  }

  public function hostConfig()
  {
    $targetVolumes=null;
    $restartPolicy=new RestartPolicy();
    $restartPolicy->setName('always');
    $hostConfig=new HostConfig();
    if($this->memory !== null)
      $hostConfig->setMemory($this->memory);

    $hostConfig->setNetworkMode($this->net);
    $hostConfig->setDns([$this->dns]);
    foreach($this->targetVolumes as $var)
      $targetVolumes[]=sprintf("%s:%s", $var->volume, $var->bind);
    $hostConfig->setBinds($targetVolumes);

    $hostConfig->setRestartPolicy($restartPolicy);
    return $hostConfig;
  }

  public function getContainer()
  {
    $this->connectAPI();
    if(!$this->container)
    {
      $this->container=$this->docker->getContainerManager()->find($this->name);
    }
    if($this->container instanceof \Docker\API\Model\ContainersCreatePostResponse201 )
    {
      $this->container=$this->docker->containerInspect($this->container->getId());
    }

    return $this->container;
  }

  public function getDocker()
  {
    return $this->docker;
  }

  public function pull()
  {
    if($this->server == null) return false;
    $this->connectAPI();
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
    $imageCreateResult=$this->docker->imageCreate($this->image, ['fromImage'=>$this->image],$authHeaders);
    $imageCreateResult->wait();
    return true;
  }

  public function destroy()
  {
    $this->connectAPI();
    $this->docker->containerDelete($this->name, ['force'=>true]);
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
}
