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
        ],
    ]) ?>
