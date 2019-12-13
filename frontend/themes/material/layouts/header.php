<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;


/* @var $this \yii\web\View */
/* @var $content string */
?>
  <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <a class="navbar-brand" href="<?=Yii::$app->request->url?>"><?=$this->title?></a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end">
            <?php if(!Yii::$app->user->isGuest):?>
              <?php \yii\widgets\Pjax::begin(['id'=>'claim-flag','formSelector'=>'#claim','linkSelector'=>false,'enablePushState'=>false]);?>
              <?=$this->render('@app/modules/target/views/default/claim');?>
              <?php \yii\widgets\Pjax::end();?>
            <?php endif; /*END OF FLAG FORM*/?>
            <ul class="navbar-nav">
              <?php if(Yii::$app->user->isGuest):?>
                <li class="nav-item"><?=Html::a('Login',['/site/login'],['class'=>'nav-link'])?></li>
                <li class="nav-item"><?=Html::a('Signup',['/site/register'],['class'=>'nav-link'])?></li>
              <?php else: ?>
                <li class="nav-item dropdown">
                  <a class="nav-link" href="/profile/hints" id="navbarHintsDropDown" data-toggle="dropdown" aria-haspopup="true" data-pjax="" aria-expanded="false">
                    <?php if(count(Yii::$app->user->identity->pendingHints)>0):?><i class="fas fa-lightbulb text-primary" style="font-size: 2em;"></i><span class="notification"><?=count(Yii::$app->user->identity->pendingHints)?></span><?php else:?><i class="fas fa-lightbulb" style="font-size: 2em;"></i><?php endif;?>
                    <p class="d-lg-none d-md-block">
                      Some Actions
                    </p>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarHintsDropDown" id="hintsMenu">
                  </div>
                </li>

                <li class="nav-item dropdown">
                  <a class="nav-link" href="/profile/notifications" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" data-pjax="" aria-expanded="false">
                    <?php if(count(Yii::$app->user->identity->pendingNotifications)>0):?><i class="fas fa-bell text-primary" style="font-size: 2em;"></i><span class="notification"><?=count(Yii::$app->user->identity->pendingNotifications)?></span><?php else:?><i class="fas fa-bell" style="font-size: 2em;"></i><?php endif;?>
                    <p class="d-lg-none d-md-block">
                      Some Actions
                    </p>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink" id="notificationsMenu">
                  </div>
                </li>
              <li class="nav-item dropdown">
                <a class="nav-link" href="#" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fas fa-user" style="font-size: 2em;"></i>
                  <p class="d-lg-none d-md-block">
                    Account
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                  <a class="dropdown-item" href="/profile/me">Profile</a>
                  <a class="dropdown-item" href="/profile/settings">Settings</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#" style="padding: 0;">
                    <?php
                    if(!Yii::$app->user->isGuest)
                    {
                      echo Html::beginForm(['/site/logout'], 'post',['id'=>'logout','pjax-data'=>"0"]);
                      echo Html::submitButton(
                          'Logout (' . Yii::$app->user->identity->username . ')',
                          ['class' => 'btn btn-link logout','pjax-data'=>"0",'id'=>'logoutButton']
                      );
                      echo Html::endForm();
                    }
                    ?>
                  </a>
                </div>
              </li><!-- // end of account drop down menu -->
            <?php endif;?>
            </ul>
          </div><!-- collapse navbar-collapse justify-content-end -->
        </div>
      </nav>
  <!-- End Navbar -->
