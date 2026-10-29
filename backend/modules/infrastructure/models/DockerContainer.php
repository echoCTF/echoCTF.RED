<?php

namespace app\modules\infrastructure\models;

use yii\base\Model;
use Docker\Docker;
use Docker\DockerClientFactory;

use Docker\API\Model\AuthConfig;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\EndpointIPAMConfig;
use Docker\API\Model\EndpointSettings;
use Docker\API\Model\HostConfig;
use Docker\API\Model\NetworkingConfig;
use Docker\API\Model\RestartPolicy;

use Docker\API\Exception\ImageCreateNotFoundException;
use Docker\API\Exception\ContainerCreateNotFoundException;
use Docker\API\Exception\ContainerStartNotFoundException;
use Docker\API\Exception\ContainerStartInternalServerErrorException;

class DockerContainer extends Model
{
  public $name;
  public $hostname;
  public $ipoctet;
  public $fqdn;
  public $image;
  public $net;
  public $dns;
  public $targetVariables = [];
  public $targetVolumes = [];
  public $mac;
  public $memory;
  public $server;
  public $ssl;
  public $parameters;
  public $imageparams;
  public $timeout = 2000;
  public $labels = [];
  private $container;
  private $client;
  private $docker;

  public function init()
  {
    parent::init();
  }

  public function __set($name, $value)
  {
    if (!property_exists($this, $name)) {
      return;
    }
    parent::__set($name, $value);
  }

  public function setTargetVariables($value)
  {
    if (is_array($value)) {
      $this->targetVariables = $value;
    }
    $this->targetVariables = [];
  }

  public function setTargetVolumes($value)
  {
    if (is_array($value)) {
      $this->targetVolumes = $value;
    }
    $this->targetVolumes = [];
  }

  public function connectAPI()
  {
    if ($this->docker !== null) {
      return;
    }

    $params['remote_socket'] = $this->server;
    $params['ssl'] = $this->ssl;
    $params['timeout'] = $this->timeout;

    $this->client = DockerClientFactory::create($params);
    $this->docker = Docker::create($this->client);
  }


  // Spin a container
  public function spin()
  {
    $targetVariables = null;
    $this->connectAPI();

    $hostConfig = $this->hostConfig();

    $containerConfig = new ContainersCreatePostBody();
    /*
    $endpointSettings=new EndpointSettings();
    $endpointIPAMConfig=new EndpointIPAMConfig();
    $endpointIPAMConfig->setIPv4Address($this->ipoctet);// target->ipoctet
    $endpointSettings->setIPAMConfig($endpointIPAMConfig);

    $nwc=new NetworkingConfig();
    $nwc->setEndpointsConfig(new \ArrayObject([
      $this->net => $endpointSettings
    ]));
    $containerConfig->setNetworkingConfig($nwc);
*/
    $containerConfig->setHostname($this->fqdn); // target->fqdn
    $containerConfig->setImage($this->image); // target->image
    $containerConfig->setOpenStdin(true);
    $containerConfig->setTty(true);
    $containerConfig->setAttachStdin(true);
    $containerConfig->setAttachStdout(true);
    $containerConfig->setAttachStderr(true);

    foreach ($this->targetVariables as $var) {
      $targetVariables[] = sprintf('%s=%s', $var->key, $var->val);
    }

    $containerConfig->setEnv($targetVariables);
    $containerConfig->setHostConfig($hostConfig);

    if (!empty($this->labels) && is_array($this->labels)) {
      $containerConfig->setLabels($this->labels);
    }

    $this->container = $this->docker->containerCreate($containerConfig, ['name' => $this->name]);
    $this->docker->containerStart($this->container->getId());
  }

  public function hostConfig()
  {
    $targetVolumes = null;
    $restartPolicy = new RestartPolicy();
    $restartPolicy->setName('always');
    $hostConfig = new HostConfig();
    $memory = $this->memory ?? $this->getMemory();
    if ($memory !== null) {
      $hostConfig->setMemory($memory);
    }

    $hostConfig->setNetworkMode($this->net);
    $hostConfig->setDns([$this->dns]);
    foreach ($this->targetVolumes as $var) {
      $targetVolumes[] = sprintf('%s:%s', $var->volume, $var->bind);
    }
    $hostConfig->setBinds($targetVolumes);

    $hostConfig->setRestartPolicy($restartPolicy);
    return $hostConfig;
  }

  public function getContainer()
  {
    $this->connectAPI();
    /*
    if(!$this->container)
    {
     $this->container=$this->docker->getContainerManager()->find($this->name);
    }
    if($this->container instanceof \Docker\API\Model\ContainersCreatePostResponse201 )
    {
      $this->container=$this->docker->containerInspect($this->container->getId());
    }
*/

    $this->container = $this->docker->containerInspect($this->container->getId());
    return $this->container;
  }

  public function getDocker()
  {
    return $this->docker;
  }

  public function pull()
  {
    if ($this->server == null) {
      return false;
    }
    $this->connectAPI();
    $authHeaders = [];
    if (!empty($this->imageparams)) {
      // X-Registry-Auth must be a base64url encoded JSON string, imageCreate()
      // does not serialize an AuthConfig object (only imagePush() does)
      $decoded = json_decode($this->imageparams, true);
      $auth = is_array($decoded) ? array_intersect_key($decoded, array_flip(['username', 'password', 'email', 'serveraddress'])) : [];
      if ($auth !== []) {
        $authHeaders['X-Registry-Auth'] = rtrim(strtr(base64_encode(json_encode($auth)), '+/', '-_'), '=');
      }
    }
    $imageCreateResult = $this->docker->imageCreate(null, ['fromImage' => $this->image], $authHeaders);
    $imageCreateResult->wait();
    return true;
  }

  public function destroy()
  {
    $this->connectAPI();
    $this->docker->containerDelete($this->name, ['force' => true]);
  }

  public function getMemory()
  {
    if ($this->parameters !== null) {
      $decoded = \yii\helpers\Json::decode($this->parameters, false);
      if ($decoded !== null && property_exists($decoded, 'hostConfig') && property_exists($decoded->hostConfig, 'Memory')) {
        $memory = intval($decoded->hostConfig->Memory);
        return $memory < (16 * 1024 * 1024) ? $memory * 1024 * 1024 : $memory;
      }
    }
    return null;
  }

  public function disconnectApi()
  {
    $this->docker = null;
    $this->client = null;
  }

  /**
   * Fetch the logs (stdout and stderr) of a container as text.
   *
   * @param Docker $docker connected client
   * @param string $name container name
   * @return string
   * @throws \yii\base\UserException when docker doesn't return the logs
   */
  public static function fetchLogs(Docker $docker, string $name): string
  {
    $response = $docker->executeRawEndpoint(new \Docker\API\Endpoint\ContainerLogs($name, ['stdout' => true, 'stderr' => true]));
    $body = (string) $response->getBody();
    if ($response->getStatusCode() !== 200) {
      throw new \yii\base\UserException(sprintf('Docker answered %d: %s', $response->getStatusCode(), trim($body)));
    }
    return self::demuxLogs($body);
  }

  /**
   * Containers with a TTY send their logs as plain text. Containers without
   * one put an 8 byte header ([stream, 0, 0, 0, size]) in front of each chunk,
   * strip those.
   *
   * @param string $body
   * @return string
   */
  public static function demuxLogs(string $body): string
  {
    $out = '';
    $pos = 0;
    $len = strlen($body);
    while ($pos + 8 <= $len) {
      if (ord($body[$pos]) > 2 || substr($body, $pos + 1, 3) !== "\0\0\0") {
        break;
      }
      $size = unpack('N', substr($body, $pos + 4, 4))[1];
      $out .= substr($body, $pos + 8, $size);
      $pos += 8 + $size;
    }
    return $out . substr($body, $pos);
  }
}
