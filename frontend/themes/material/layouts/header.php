<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;


/* @var $this \yii\web\View */
/* @var $content string */
?>
  <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <a class="navbar-brand" href="<?=Html::encode(Yii::$app->request->url)?>"><?=Html::encode($this->title)?></a>
          </div>
          <?php if(!Yii::$app->user->isGuest):?>
          <div class="justify-content-end">
              <?=$this->render('@app/modules/target/views/default/search');?>
          </div>
            <?php endif;?>

          <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end">
            <?php if(!Yii::$app->user->isGuest):?>
              <?php \yii\widgets\Pjax::begin(['id'=>'claim-flag', 'formSelector'=>'#claim', 'linkSelector'=>false, 'enablePushState'=>false]);?>
              <?=$this->render('@app/modules/target/views/default/claim');?>
              <?php \yii\widgets\Pjax::end();?>
            <?php endif;/*END OF FLAG FORM*/?>
            <ul class="navbar-nav">
              <?php if(Yii::$app->user->isGuest):?>
                <?php if(Yii::$app->sys->disable_registration!==true):?>
                <li class="nav-item"><?=Html::a('<i class="fas fa-user-plus"></i> Signup', ['/register'], ['class'=>'nav-link','rel'=>'tooltip', 'title'=>"Sign up for an ".\Yii::$app->sys->{"event_name"}." account"])?></li>
                <?php endif;?>
                <li class="nav-item"><?=Html::a('<i class="fas fa-sign-in-alt"></i>  Login', ['/site/login'], ['class'=>'nav-link','rel'=>"tooltip", 'title'=>"Login to your ".\Yii::$app->sys->{"event_name"}." account"])?></li>
              <?php else: ?>
                <li class="nav-item dropdown" id="Hints">
                  <a class="nav-link" href="/profile/hints" id="navbarHintsDropDown" data-toggle="dropdown" aria-haspopup="true" data-pjax="" aria-expanded="false" rel="tooltip" aria-label="<?=\Yii::t('app','Hints to help you progress further')?>" title="<?=\Yii::t('app','Hints to help you progress further')?>">
                    <?php if(count(Yii::$app->user->identity->pendingHints) > 0):?><i class="fas fa-lightbulb text-primary" style="font-size: 2em;"></i><span class="notification"><?=count(Yii::$app->user->identity->pendingHints)?></span><?php else:?><i class="fas fa-lightbulb" style="font-size: 2em;"></i><?php endif;?>
                    <p class="d-lg-none d-md-block">Hints</p>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarHintsDropDown" id="hintsMenu">
                    <?=\app\widgets\HintsWidget::widget();?>
                  </div>
                </li>

                <li class="nav-item dropdown" id="Notifications">
                  <a class="nav-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" data-pjax="" aria-expanded="false" rel="tooltip"  aria-label="<?=\Yii::t('app','Your notifications')?>" title="<?=\Yii::t('app','Your notifications')?>">
                    <?php if(count(Yii::$app->user->identity->pendingNotifications) > 0):?><i class="fas fa-bell text-primary" style="font-size: 2em;"></i><span class="notification"><?=count(Yii::$app->user->identity->pendingNotifications)?></span><?php else:?><i class="fas fa-bell" style="font-size: 2em;"></i><?php endif;?>
                    <p class="d-lg-none d-md-block"><?=\Yii::t('app','Notifications')?></p>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink" id="notificationsMenu">
                    <?=\app\widgets\NotificationsWidget::widget();?>
                  </div>
                </li>
              <li class="nav-item">
                <a class="nav-link" href="/profile/me" aria-haspopup="false" aria-expanded="false" data-toggle="tooltip" aria-label="<?=\Yii::t('app','Go to your profile')?>" title="<?=\Yii::t('app','Go to your profile')?>">
                  <i class="fas fa-user" style="font-size: 2em;"></i>
                  <p class="d-lg-none d-md-block">
                    <?=\Yii::t('app','Profile')?>
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <?= Html::a('<i class="fas fa-sign-out-alt" style="font-size: 2.2em;"></i><p class="d-lg-none d-md-block">'.\Yii::t('app','Logout').'</p>', Url::to(['/site/logout']), ['data-method' => 'POST',"data-pjax"=>"0",'data-toggle'=>'tooltip','title'=>\Yii::t('app','Logout'), 'aria-label'=>'Logout','data-confirm'=>\Yii::t('app',"Are you sure you want to logout?"), 'class'=>'nav-link']) ?>
              </li>
            <?php endif;?>
            </ul>
          </div><!-- collapse navbar-collapse justify-content-end -->
        </div>
      </nav>
  <!-- End Navbar -->
