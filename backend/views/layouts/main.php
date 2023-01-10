<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
Yii::$app->timeZone=Yii::$app->sys->time_zone ?: 'UTC';
date_default_timezone_set(Yii::$app->sys->time_zone ?: 'UTC');
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
        //'brandImage' => "/images/echoCTF logo white.png",
        'brandLabel' => '<img src="/images/echoCTF logo white.png" class="pull-left" style="padding-right: 3px;" width="120" alt="'.Yii::$app->name.'"/>',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels'=>false,
        'items' => [
            //['label' => '<span class="glyphicon glyphicon-home"></span> Home', 'url' => ['/site/index'], 'icon' => 'fa fa-home', ],
            ['label' => '<span class="glyphicon glyphicon-globe"></span> Content', 'url' => ['/content/default/index'], 'icon' => 'fas fa-money-check-alt','active'=>Yii::$app->controller->module->id=='content', 'visible'=>!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,
              'items'=>[
                ['label' => 'News', 'url' => ['/content/news/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin  ],
                ['label' => 'FAQ', 'url' => ['/content/faq/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Rules', 'url' => ['/content/rule/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Objectives', 'url' => ['/content/objective/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Instructions', 'url' => ['/content/instruction/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Writeup rules', 'url' => ['/content/default/writeup-rules'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Email Templates', 'url' => ['/content/email-template/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Frontpage Scenario', 'url' => ['/content/default/frontpage-scenario'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Offense Scenario', 'url' => ['/content/default/offense-scenario'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Defense Scenario', 'url' => ['/content/default/defense-scenario'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Footer logos', 'url' => ['/content/default/footer-logos'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'CSS Override', 'url' => ['/content/default/css-override'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'JS Override', 'url' => ['/content/default/js-override'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Layout Override', 'url' => ['/content/layout-override/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'VPN Templates', 'url' => ['/content/vpn-template/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Pages', 'url' => ['/content/pages/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin  ],
              ]
            ],

            ['label' => '<span class="glyphicon glyphicon-credit-card"></span> Sales', 'url' => ['/sales/default/index'], 'icon' => 'fas fa-money-check-alt','active'=>Yii::$app->controller->module->id=='sales', 'visible'=>array_key_exists('sales',\Yii::$app->modules)!==false && !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,
              'items'=>[
                ['label' => 'Sales Dashboard', 'url' => ['/sales/default/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin && array_key_exists('sales',\Yii::$app->modules)!==false, ],
                ['label' => 'Customers', 'url' => ['/sales/player-customer/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin && array_key_exists('sales',\Yii::$app->modules)!==false, ],
                ['label' => 'Subscriptions', 'url' => ['/sales/player-subscription/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin && array_key_exists('sales',\Yii::$app->modules)!==false, ],
                ['label' => 'Products', 'url' => ['/sales/product/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin && array_key_exists('sales',\Yii::$app->modules)!==false, ],
                ['label' => 'Product Networks', 'url' => ['/sales/product-network/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin && array_key_exists('sales',\Yii::$app->modules)!==false, ],
                ['label' => 'Webhook', 'url' => ['/sales/stripe-webhook/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin && array_key_exists('sales',\Yii::$app->modules)!==false, ],
              ]
            ],
            ['label' => '<span class="glyphicon glyphicon-stats"></span> Activity', 'url' => ['/activity/default/index'], 'visible' => !Yii::$app->user->isGuest,'active'=>Yii::$app->controller->module->id=='activity',
              'items'=> [
                ['label' => 'Stream', 'url' => ['/activity/stream/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Team Scores', 'url' => ['/activity/team-score/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Player Scores', 'url' => ['/activity/player-score/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Player Monthly Scores', 'url' => ['/activity/player-score-monthly/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Player Counters NF', 'url' => ['/activity/player-counter-nf/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Player vs Target Progress', 'url' => ['/activity/player-vs-target/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Target Player Progress', 'url' => ['/activity/target-player-state/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Player vs Challenge Progress', 'url' => ['/activity/player-vs-challenge/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Headshots', 'url' => ['/activity/headshot/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Challenge Solvers', 'url' => ['/activity/challenge-solver/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Player Badges', 'url' => ['/activity/player-badge/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Player Treasures', 'url' => ['/activity/player-treasure/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Player Findings', 'url' => ['/activity/player-finding/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Writeups', 'url' => ['/activity/writeup/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Writeup Ratings', 'url' => ['/activity/writeup-rating/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Player Activated Writeups', 'url' => ['/activity/player-target-help/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Reports', 'url' => ['/activity/report/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Player VPN History', 'url' => ['/activity/player-vpn-history/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Player Question Answers', 'url' => ['/activity/player-question/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Player Hints', 'url' => ['/activity/player-hint/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
//                ['label' => 'Player Tutorial Task', 'url' => ['/activity/player-tutorial-task'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin ,],
                ['label' => 'Spin History', 'url' => ['/activity/spin-history/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Spin Queue', 'url' => ['/activity/spin-queue/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Inquiries', 'url' => ['/activity/inquiry/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Notifications', 'url' => ['/activity/notification/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Sessions', 'url' => ['/activity/session/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
              ],
            ],

            ['label' => '<span class="glyphicon glyphicon-tower"></span> SmartCity', 'url' => ['/smartcity/default/index'], 'visible' => !Yii::$app->user->isGuest,'active'=>Yii::$app->controller->module->id=='smartcity',
              'items'=> [
                ['label' => 'Infrastructure', 'url' => ['/smartcity/infrastructure/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Infrastructure Targets', 'url' => ['/smartcity/infrastructure-target/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Treasure Actions', 'url' => ['/smartcity/treasure-action/index'], 'visible' => !Yii::$app->user->isGuest, ],
              ],
            ],

            ['label' => '<span class="glyphicon glyphicon-user"></span> Frontend', 'url' => ['/frontend/default/index'], 'visible' => !Yii::$app->user->isGuest,'active'=>Yii::$app->controller->module->id=='frontend',
              'items'=> [
                ['label' => 'Players', 'url' => ['/frontend/player/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Profiles', 'url' => ['/frontend/profile/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Player Last', 'url' => ['/frontend/player-last/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Player SSL', 'url' => ['/frontend/player-ssl/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Player Spins', 'url' => ['/frontend/player-spin/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Player Relations', 'url' => ['/frontend/player-relation/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Teams', 'url' => ['/frontend/team/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Team Players', 'url' => ['/frontend/teamplayer/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Banned Players', 'url' => ['/frontend/banned-player/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Certificate Revocation List', 'url' => ['/frontend/crl/index'], 'visible' => !Yii::$app->user->isGuest, ],
              ],
            ],
            ['label' => '<span class="glyphicon glyphicon-tasks"></span> Infrastructure', 'url' => ['/infrastructure/default/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin,'active'=>Yii::$app->controller->module->id=='infrastructure',
              'items'=> [
                ['label' => 'Networks', 'url' => ['/infrastructure/network/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Targets', 'url' => ['/infrastructure/target/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Ondemand', 'url' => ['/infrastructure/target-ondemand/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Target metadata', 'url' => ['/infrastructure/target-metadata/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Target State', 'url' => ['/infrastructure/target-state/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Target Instances', 'url' => ['/infrastructure/target-instance/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Network Targets', 'url' => ['/infrastructure/network-target/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Network Target Schedule', 'url' => ['/infrastructure/network-target-schedule/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Network Players', 'url' => ['/infrastructure/network-player/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Variables', 'url' => ['/infrastructure/target-variable/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Volumes', 'url' => ['/infrastructure/target-volume/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Servers', 'url' => ['/infrastructure/server/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Target Instance Audit', 'url' => ['/infrastructure/target-instance-audit/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Credential', 'url' => ['/gameplay/credential/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
              ],
            ],
            ['label' => '<span class="glyphicon glyphicon-flag"></span> Gameplay', 'url' => ['/gameplay'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, 'active'=>Yii::$app->controller->module->id=='gameplay',
              'items'=> [
                ['label' => 'Findings', 'url' => ['/gameplay/finding/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Treasure', 'url' => ['/gameplay/treasure/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Challenges', 'url' => ['/gameplay/challenge/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Questions', 'url' => ['/gameplay/question/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Hints', 'url' => ['/gameplay/hint/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Achievements', 'url' => ['/gameplay/achievement/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Badges', 'url' => ['/gameplay/badge/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Tutorials', 'url' => ['/gameplay/tutorial/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Tutorial Target', 'url' => ['/gameplay/tutorial-target/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Tutorial Tasks', 'url' => ['/gameplay/tutorial-task/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Tutorial Task Dependencies', 'url' => ['/gameplay/tutorial-task-dependency/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
              ],
            ],
            ['label' => '<span class="glyphicon glyphicon-cog"></span> Settings', 'url' => ['/settings'], 'visible' => !Yii::$app->user->isGuest,'active'=>Yii::$app->controller->module->id=='settings',
              'items'=> [
                ['label' => 'Avatar', 'url' => ['/settings/avatar/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Experience', 'url' => ['/settings/experience/index'], 'visible' => !Yii::$app->user->isGuest, ],
                ['label' => 'Countries', 'url' => ['/settings/country/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Users', 'url' => ['/settings/user/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'URL Routes', 'url' => ['/settings/url-route/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Disabled Routes', 'url' => ['/settings/disabled-route/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Player Disabled Routes', 'url' => ['/settings/player-disabledroute/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Banned MX Servers', 'url' => ['/settings/banned-mx-server/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'OpenVPN', 'url' => ['/settings/openvpn/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Sysconfigs', 'url' => ['/settings/sysconfig/index'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
                ['label' => 'Configure', 'url' => ['/settings/sysconfig/configure'], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin, ],
              ],
            ],
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout',
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
        <p class="pull-left">&copy; <?= Html::a('Echothrust Solutions', 'https://www.echothrust.com/') ?> <?= date('Y') ?></p>
        <p class="pull-right"><small><?=date('Y/m/d H:i');?></small></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
