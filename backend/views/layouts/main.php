<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\bootstrap5\Breadcrumbs;
use app\assets\AppAsset;

$this->title = Yii::$app->sys->event_name . ' mUI: ' . $this->title;
AppAsset::register($this);
$this->registerJsFile('@web/js/hljs/highlight.min.js',[
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
$this->registerCssFile('@web/js/hljs/styles/a11y-dark.min.css',['depends' => [\yii\web\JqueryAsset::class]]);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
  <meta charset="<?= Yii::$app->charset ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php $this->registerCsrfMetaTags() ?>
  <title><?= Html::encode($this->title) ?></title>
  <?php $this->head() ?>
</head>

<body>
  <?php $this->beginBody() ?>

  <div class="wrap mt-auto">

    <?php
    NavBar::begin([
      //'brandImage' => "/images/echoCTF logo white.png",
      'brandLabel' => '<img src="/images/echoCTF logo white.png" class="pull-left" style="padding-right: 3px;" width="120" alt="' . Yii::$app->name . '"/>',
      'brandUrl' => Yii::$app->homeUrl,
      'renderInnerContainer' => true,
      'innerContainerOptions' => [
        'class' => ['container']
      ],
      'options' => [
        'class' => ['navbar-dark', 'bg-dark', 'navbar-expand-xl'],
      ],
    ]);
    echo \app\widgets\DbMenuWidget::widget();
    NavBar::end();
    ?>

    <div class="container">
      <div class="row">
        <?php if (!empty($this->params['jumpto'])): ?>
          <div class="col-md-2">
            <?= $this->params['jumpto'] ?>
          </div>
        <?php endif; ?>
        <div class="col">
          <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
          ]) ?>

        </div>
      </div>
      <?= Alert::widget() ?>
      <?= $content ?>
    </div>
    <hr />
    <footer class="footer">
      <div class="container">
        <div class="row">
          <div class="col">
            <p class="pull-left">&copy; <?= Html::a('Echothrust Solutions', 'https://www.echothrust.com/') ?> <?= date('Y') ?></p>
          </div>
          <div class="col-xl-2">
            <p class="pull-right"><small><?= date('Y/m/d H:i'); ?></small></p>
          </div>
        </div>
      </div>
    </footer>
  </div>


  <?php $this->endBody() ?>
</body>
<?php
$this->registerJs(
  'hljs.highlightAll();',
  $this::POS_READY,
  'markdown-highlighter'
);
?>

</html>
<?php $this->endPage() ?>