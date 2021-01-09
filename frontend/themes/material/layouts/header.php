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
            <a class="navbar-brand" href="<?=Html::encode(Yii::$app->request->url)?>"><?=$this->title?></a>
          </div>
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
                <li class="nav-item"><?=Html::a('<i class="fas fa-user-plus"></i> Signup', ['/site/register'], ['class'=>'nav-link'])?></li>
                <li class="nav-item"><?=Html::a('<i class="fas fa-sign-in-alt"></i>  Login', ['/site/login'], ['class'=>'nav-link'])?></li>
              <?php else: ?>
                <li class="nav-item dropdown" id="Hints">
                  <a class="nav-link" href="/profile/hints" id="navbarHintsDropDown" data-toggle="dropdown" aria-haspopup="true" data-pjax="" aria-expanded="false" aria-label="Hints to help you progress further">
                    <?php if(count(Yii::$app->user->identity->pendingHints) > 0):?><i class="fas fa-lightbulb text-primary" style="font-size: 2em;"></i><span class="notification"><?=count(Yii::$app->user->identity->pendingHints)?></span><?php else:?><i class="fas fa-lightbulb" style="font-size: 2em;"></i><?php endif;?>
                    <p class="d-lg-none d-md-block">Hints</p>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarHintsDropDown" id="hintsMenu">
                    <?php if(count(Yii::$app->user->identity->pendingHints) > 0):?>
                    <?=\app\widgets\HintsWidget::widget();?>
                    <?php else: ?>
                      <a href="#" class="dropdown-item" title="nothing here...">nothing here...</a>
                    <?php endif;?>
                  </div>
                </li>

                <li class="nav-item dropdown" id="Notifications">
                  <a class="nav-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" data-pjax="" aria-expanded="false"  aria-label="Your notifications">
                    <?php if(count(Yii::$app->user->identity->pendingNotifications) > 0):?><i class="fas fa-bell text-primary" style="font-size: 2em;"></i><span class="notification"><?=count(Yii::$app->user->identity->pendingNotifications)?></span><?php else:?><i class="fas fa-bell" style="font-size: 2em;"></i><?php endif;?>
                    <p class="d-lg-none d-md-block">Notifications</p>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink" id="notificationsMenu">
                    <?php if(count(Yii::$app->user->identity->pendingNotifications) > 0):?>
                    <?=\app\widgets\NotificationsWidget::widget();?>
                    <?php else: ?>
                    <a href="#" class="dropdown-item" title="nothing here...">nothing here...</a>
                    <?php endif;?>
                  </div>
                </li>
              <li class="nav-item">
                <a class="nav-link" href="/profile/me" aria-haspopup="false" aria-expanded="false">
                  <i class="fas fa-user" style="font-size: 2em;"></i>
                  <p class="d-lg-none d-md-block">
                    Profile
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <?= Html::a('<i class="fas fa-sign-out-alt" style="font-size: 2.2em;"></i><p class="d-lg-none d-md-block">Logout</p>', Url::to(['/site/logout']), ['data-method' => 'POST',"data-pjax"=>"0",'data-confirm'=>"Are you sure you want to logout?", 'class'=>'nav-link']) ?>
              </li>
            <?php endif;?>
            </ul>
          </div><!-- collapse navbar-collapse justify-content-end -->
        </div>
      </nav>
  <!-- End Navbar -->
