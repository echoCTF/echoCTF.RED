<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
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

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'brandImage'=>'/images/logo-white-small.png',
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels'=>true,
        'items' => [
            ['label' => 'Dashboard', 'url' => ['/dashboard/index'],'visible'=>!Yii::$app->user->isGuest],
            ['label' => 'Challenges', 'url' => ['/challenge/default/index'],'visible'=>!Yii::$app->user->isGuest],
            ['label' => 'Help', 'url' => ['/help/index'],
              'items'=> [
                ['label' => 'FAQ', 'url' => ['/help/faq/index']],
                ['label' => 'Rules', 'url' => ['/help/rule/index']],
                ['label' => 'Instructions', 'url' => ['/help/instruction/index']],
              ],
            ],
            ['label' => 'Login', 'url' => ['/site/login'],'visible'=>Yii::$app->user->isGuest],
            Yii::$app->user->isGuest?'<li><b style="color: #9d9d9d; padding-top: 15px;￼    position: relative;    display: block;￼    padding: 10px 15px;">or</b></li>': '',
            ['label' => 'Register', 'url' => ['/site/register'], 'visible'=>Yii::$app->user->isGuest],
            ['label' => Yii::$app->user->isGuest ? 'Guest' : Yii::$app->user->identity->username.' ('.number_format(Yii::$app->user->identity->playerScore->points).')', 'url' => ['/profile/me'], 'visible'=>!Yii::$app->user->isGuest],
            !Yii::$app->user->isGuest?'<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Logout',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>' : '',
        ],
    ]);
      NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p>
          <span class="pull-left">&copy; <?= Html::a('Echothrust Solutions', 'https://www.echothrust.com/' ) ?> 2012-<?= date('y') ?></span>
        </p>
    </div>
</footer>

<?php $this->endBody() ?>
<script src="/js/cookieconsent.min.js" data-cfasync="false"></script>
<script>
window.cookieconsent.initialise({
  "palette": {
    "popup": {
      "background": "#000",
      "text": "#94c11f"
    },
    "button": {
      "background": "#94c11f"
    }
  },
  "theme": "classic",
	"content": {
    "message": "echoCTF RED needs cookies to operate."
  }
});
</script>
</body>
</html>
<?php $this->endPage() ?>
