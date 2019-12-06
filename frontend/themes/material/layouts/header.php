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
            <form class="navbar-form">
              <div class="input-group no-border">
                <input type="text" value="" class="form-control" placeholder="ETSCTF_FLAG">
                <button type="submit" class="btn btn-white btn-round btn-just-icon">
                  <i class="material-icons">flag</i>
                  <div class="ripple-container"></div>
                </button>
              </div>
            </form>
          <?php endif; /*END OF FLAG FORM*/?>
            <ul class="navbar-nav">
              <?php if(Yii::$app->user->isGuest):?>
                <li class="nav-item">
                  <?=Html::a('Login',['/site/login'],['class'=>'nav-link'])?>
                </li>
              <?php else: ?>
                  <li class="nav-item">
                    <a class="nav-link" href="#">
                      <i class="material-icons">notifications</i>
                      <?php if(count(Yii::$app->user->identity->pendingNotifications)>0):?><span class="notification"><?=count(Yii::$app->user->identity->pendingNotifications)?></span><?php endif;?>
                      <p class="d-lg-none d-md-block">Pending Notifications</p>
                    </a>
                  </li>
              <li class="nav-item dropdown">
                <a class="nav-link" href="#" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">person</i>
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
                      echo Html::beginForm(['/site/logout'], 'post');
                      echo Html::submitButton(
                          'Logout (' . Yii::$app->user->identity->username . ')',
                          ['class' => 'btn btn-link logout']
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
