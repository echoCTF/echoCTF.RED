<?php

use \yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = Yii::$app->sys->event_name . " " . \Yii::t('app', "Payments History");
$this->_url = \yii\helpers\Url::to([null], 'https');

?>
<div class="payments-index">
  <div class="body-content">
    <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
      <h1 class="display-4 text-primary"><?= \Yii::t('app', 'Payment history') ?></h1>
    </div>
    <div>
      <?php
      echo GridView::widget([
        'dataProvider' => $paymentsProvider,
        'tableOptions' => ['class' => 'table orbitron'],
        'layout' => '{items}',
        'summary' => '',
        'pager' => [
          'class' => 'yii\bootstrap4\LinkPager',
          'linkOptions' => ['class' => ['page-link'], 'aria-label' => 'Pager link', 'rel' => 'nofollow'],
          'options' => ['id' => 'target-pager', 'class' => 'align-middle'],
          'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
          'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
          'maxButtonCount' => 3,
          'disableCurrentPageButton' => true,
          'prevPageLabel' => '<i class="fas fa-chevron-left"></i>',
          'nextPageLabel' => '<i class="fas fa-chevron-right"></i>',
        ],
        'columns' => [
          [
            'label' => \Yii::t('app', 'Date'),
            'headerOptions' => ['class' => 'd-none d-xl-table-cell',],
            'contentOptions' => ['class' => 'd-none d-xl-table-cell'],
            'attribute' => 'created_at',
            'format' => 'datetime',
          ],
          //[
          //  'label' => \Yii::t('app', 'Payment ID'),
          //  'headerOptions' => ['class' => 'd-none d-xl-table-cell',],
          //  'contentOptions' => ['class' => 'd-none d-xl-table-cell'],
          //  'attribute' => 'payment_id',
          //  'format' => 'raw',
          //],
          [
            'label' => \Yii::t('app', 'Amount'),
            'headerOptions' => ['class' => 'd-none d-xl-table-cell',],
            'contentOptions' => ['class' => 'd-none d-xl-table-cell'],
            'attribute' => 'amount',
            'format' => 'currency',
            'value' => function ($model) {
              return $model->amount / 100;
            }
          ],
          [
            'class' => 'app\actions\ActionColumn',
            'headerOptions' => ["style" => 'width: 4rem'],
            'template' => '{view}',
            'buttons' => [
              'view' => function ($url, $model) {
                return Html::a(
                  '<i class="fas fa-eye"></i>',
                  Url::to(['/subscription/default/payment', 'id' => $model->payment_id]),
                  [
                    'target' => '_blank',
                    'style' => "font-size: 1.5em;",
                    'rel' => "tooltip",
                    'title' => \Yii::t('app', 'View Payment details (fetch from Stripe)'),
                    'aria-label' => \Yii::t('app', 'View Payment details (fetch from Stripe)'),
                    'data-pjax' => '0',
                    'data' => [
                      'confirm' => Yii::t('app', 'You will be redirected to stripe for the payment details!'),
                      'method' => 'post',
                    ],
                  ]
                );
              }
            ]

          ]
        ]
      ]);
      ?>
    </div>
  </div>
  <center><?= Html::a('<b><i class="fas fa-backward"></i> ' . \Yii::t('app', 'Go back') . '</b>', ['/subscriptions'], ['class' => 'btn btn-lg btn-primary text-dark']) ?></center>
</div>