<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\gameplay\models\TargetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=ucfirst(Yii::$app->controller->module->id).' / '.ucfirst(Yii::$app->controller->id);
$this->params['breadcrumbs'][]=ucfirst(Yii::$app->controller->module->id);
$this->params['breadcrumbs'][]=['label' => 'Targets', 'url' => ['index']];

?>
<pre>
version: '2'
services:
<?= ListView::widget([
    'itemOptions' => [
      'tag' => false,
     ],
     'options' => ['tag'=>false],
     'layout'=>"{items}",
     'summary'=>false,
     'pager'=>false,
    'dataProvider' => $dataProvider,
    'itemView' => '_docker-compose-item'
]);?>


networks:
  AAnet:
    driver: bridge
    driver_opts:
      com.docker.network.enable_ipv6: "false"
    ipam:
      driver: default
      config:
      - subnet: 10.0.0.0/16
        gateway: 10.0.255.254

</pre>
