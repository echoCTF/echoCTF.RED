<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

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
        'brandLabel' => '<img src="/images/echoCTF logo white.png" class="pull-left" style="padding-right: 3px;" width="120" alt="'. Yii::$app->name.'"/>',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels'=>false,
        'items' => [
            ['label' => '<span class="glyphicon glyphicon-home"></span> Home', 'url' => ['/site/index'],'icon' => 'fa fa-home',],
            ['label' => '<span class="glyphicon glyphicon-stats"></span> Game Activity', 'url' => ['/activity'], 'visible' => !Yii::$app->user->isGuest ,
              'items'=> [
                ['label' => 'Sessions', 'url' => ['/activity/session'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Notifications', 'url' => ['/activity/notification'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Player Scores', 'url' => ['/activity/player-score'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Team Scores', 'url' => ['/activity/team-score'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Reports', 'url' => ['/activity/report'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Stream', 'url' => ['/activity/stream'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Player VPN History', 'url' => ['/activity/player-vpn-history'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Player Badges', 'url' => ['/activity/player-badge'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Player Treasures', 'url' => ['/activity/player-treasure'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Player Findings', 'url' => ['/activity/player-finding'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Player Question Answers', 'url' => ['/activity/player-question'],'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Player Hints', 'url' => ['/activity/player-hint'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
//                ['label' => 'Player Tutorial Task', 'url' => ['/activity/player-tutorial-task'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Headshots', 'url' => ['/activity/headshot'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Spin History', 'url' => ['/activity/spin-history'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Spin Queue', 'url' => ['/activity/spin-queue'], 'visible' => !Yii::$app->user->isGuest ,],
              ],
            ],

            ['label' => '<span class="glyphicon glyphicon-tower"></span> SmartCity', 'url' => ['/smartcity'], 'visible' => !Yii::$app->user->isGuest,
              'items'=> [
                ['label' => 'Infrastructure', 'url' => ['/smartcity/infrastructure'], 'visible' => !Yii::$app->user->isGuest,],
                ['label' => 'Infrastructure Targets', 'url' => ['/smartcity/infrastructure-target'], 'visible' => !Yii::$app->user->isGuest,],
                ['label' => 'Treasure Actions', 'url' => ['/smartcity/treasure-action'], 'visible' => !Yii::$app->user->isGuest,],
              ],
            ],

            ['label' => '<span class="glyphicon glyphicon-user"></span> Frontend', 'url' => ['/frontend'], 'visible' => !Yii::$app->user->isGuest,
              'items'=> [
                ['label' => 'Players', 'url' => ['/frontend/player'], 'visible' => !Yii::$app->user->isGuest,],
                ['label' => 'Profiles', 'url' => ['/frontend/profile'], 'visible' => !Yii::$app->user->isGuest,],
                ['label' => 'Player Last', 'url' => ['/activity/player-last'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Player SSL', 'url' => ['/frontend/player-ssl'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Player Spins', 'url' => ['/frontend/player-spin'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Teams', 'url' => ['/frontend/team'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Team Players', 'url' => ['/frontend/teamplayer'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Banned Players', 'url' => ['/frontend/banned-player'], 'visible' => !Yii::$app->user->isGuest,],
                ['label' => 'Certificate Revocation List', 'url' => ['/frontend/crl'], 'visible' => !Yii::$app->user->isGuest,],
              ],
            ],
            ['label' => '<span class="glyphicon glyphicon-tasks"></span> Network', 'url' => ['/network'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,
              'items'=> [
                ['label' => 'Networks', 'url' => ['/gameplay/network'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Targets', 'url' => ['/gameplay/target'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Network Targets', 'url' => ['/gameplay/network-target'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Network Players', 'url' => ['/gameplay/network-player'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Variables', 'url' => ['/gameplay/target-variable'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Volumes', 'url' => ['/gameplay/target-volume'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Credential', 'url' => ['/gameplay/credential'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
              ],
            ],
            ['label' => '<span class="glyphicon glyphicon-flag"></span> Gameplay', 'url' => ['/gameplay'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,
              'items'=> [
                ['label' => 'Findings', 'url' => ['/gameplay/finding'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Treasure', 'url' => ['/gameplay/treasure'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Challenges', 'url' => ['/gameplay/challenge'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Questions', 'url' => ['/gameplay/question'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Hints', 'url' => ['/gameplay/hint'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Achievements', 'url' => ['/gameplay/achievement'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Badges', 'url' => ['/gameplay/badge'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Tutorials', 'url' => ['/gameplay/tutorial'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
              ],
            ],
            ['label' => '<span class="glyphicon glyphicon-cog"></span> Settings', 'url' => ['/settings'], 'visible' => !Yii::$app->user->isGuest ,
              'items'=> [
                ['label' => 'Avatar', 'url' => ['/settings/avatar'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Experience', 'url' => ['/settings/experience'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Countries', 'url' => ['/settings/country'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'FAQ', 'url' => ['/settings/faq'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Rules', 'url' => ['/settings/rule'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Objectives', 'url' => ['/settings/objective'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Instructions', 'url' => ['/settings/instruction'], 'visible' => !Yii::$app->user->isGuest ,],
                ['label' => 'Sysconfigs', 'url' => ['/settings/sysconfig'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Disabled Routes', 'url' => ['/settings/disabled-route'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Users', 'url' => ['/settings/user'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Configure', 'url' => ['/settings/sysconfig/configure'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
              ],
            ],
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
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
        <p class="pull-left">&copy; <?= Html::a('Echothrust Solutions', 'https://www.echothrust.com/' ) ?> <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
