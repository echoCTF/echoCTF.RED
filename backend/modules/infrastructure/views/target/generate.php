<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\gameplay\models\Target */

$this->title=$model->name;
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=['label' => 'Targets', 'url' => ['index']];
$this->params['breadcrumbs'][]=$this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="target-view">
    <h1><?= Html::encode($this->title) ?></h1>

<div class="row">
  <h3>variables.yml</h3>
<pre>
  ---
  ansible_host: <?=$model->ipoctet."\n"?>
  DOCKER: <?=$model->server."\n"?>
  mac: "<?=$model->mac?>"
  hostname: <?=$model->name."\n"?>
  fqdn: <?=$model->fqdn."\n"?>
  rootable: <?=$model->rootable."\n"?>
  difficulty: <?=$model->difficulty."\n"?>
  container:
    name: "{{hostname}}"
    hostname: "{{fqdn}}"
    build: "example" # The current folder name
    image: "example" # The current folder name
    state: "started"
    mac_address: "{{mac}}"
    purge_networks: "yes"
    env:
<?php foreach($model->targetVariables as $variable):?>
      <?=$variable->key?>: "<?=$variable->val?>"
<?php endforeach;?>
    dns_servers:
      - "<?=$model->dns?>"
    networks:
      - { name: <?=$model->net?>, ipv4_address: "{{ansible_host}}" }
    volumes: []

  ETSCTF_ROOT_FLAG: "ETSCTF_ROOT_FLAG"
  ETSCTF_ENV_FLAG: "ETSCTF_ENV_FLAG"
  ETSCTF_SHADOW_FLAG: "ETSCTF_SHADOW_FLAG"
  ETSCTF_PASSWD_FLAG: "ETSCTF_PASSWD_FLAG"
  envstr:  "ETSCTF_FLAG=ETSCTF_{{ETSCTF_ENV_FLAG}}\n"
  envhash:  "{{envstr|hash('sha256')}}"


  ETSCTF_FINDINGS:
<?php foreach($model->findings as $finding):?>
    - {
        name: "<?=$finding->name?>",
        pubname: "<?=$finding->pubname?>",
        points: <?=$finding->points?>,
        stock: <?=$finding->stock?>,
        protocol: "<?=$finding->protocol?>",
        port: '<?=$finding->port?>',
      }
<?php endforeach;?>

  ETSCTF_TREASURES:
<?php foreach($model->treasures as $index=>$treasure):?>
  - { #<?=$index."\n"?>
      name: "<?=$treasure->name?>",
      pubname: "<?=$treasure->pubname?>",
      points: <?=$treasure->points?>,
      player_type: <?=$treasure->player_type?>,
      stock: <?=$treasure->appears?>,
      code: "<?=$treasure->code?>",
      location: "<?=$treasure->location?>",
      category: "<?=$treasure->category?>",
      suggestion: "<?=$treasure->suggestion?>",
      solution: "<?=$treasure->solution?>"
    }
<?php endforeach;?>
  # These commands are executed at build time by ansible
  BUILD_COMMANDS:
  #  exec:
  #  - { cmd: "mysql < /tmp/ETSCTF.sql" }
    replace:
    - { #1
        pattern: "ENVFLAG_HASH",
        file: "/usr/local/sbin/healthcheck.sh",
        value: "{{envhash}}",
      }

  DETAILS:
    - { username: "ETSCTF", password: "ETSCTF_{{ETSCTF_SHADOW_FLAG}}", gecos: "ETSCTF_{{ETSCTF_PASSWD_FLAG}}", group: "nogroup", module: 'user' }


  purpose: >
    <?=Html::encode($model->purpose)."\n"?>

  description: |
    <?=Html::encode($model->description)."\n"?>
</pre>
</div>
</div>
