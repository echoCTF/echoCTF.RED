<?php
use yii\widgets\DetailView;
?>
<h5>Target Properties</h5>
<?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fqdn',
            'ipoctet',
            'mac',
            'dns',
            'net',
            'server',
            'image',

            [
              'label'=>'Network',
              'attribute'=>'network.name'
            ],
            'parameters',
            [
              'label'=>'Examples',
              'format'=>'raw',
              'value'=>function($model){ return '<pre>'.sprintf("docker run -itd \\\n--name %s \\\n--dns %s \\\n--hostname %s \\\n--ip %s \\\n--mac-address %s \\\n--network %s \\\n%s", $model->name,$model->dns,$model->fqdn,$model->ipoctet,$model->mac,$model->net,$model->image).'</pre>'; }
            ],
        ],
    ]) ?>
