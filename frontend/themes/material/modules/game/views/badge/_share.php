<?php
use yii\helpers\Url;
use app\widgets\Twitter;
?>
<div class="dropdown">
<?=$PRELINK?>
  <button class="btn btn-info dropdown-toggle btn-block" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fas fa-share-alt"></i> <?=\Yii::t('app','SHARE')?>
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <?php echo Twitter::widget([
          'icon'=>'<i class="fab fa-twitter-square" style="font-size: 1.4em;"></i>&nbsp; Twitter',
          'message'=>$twMessage,
          'url'=>$callbackURL,
          'linkOptions'=>['class'=>'dropdown-item', 'target'=>'_blank', 'rel'=>'noopener noreferrer nofollow'],
      ]);?>
    <a class="dropdown-item" href="https://www.linkedin.com/sharing/share-offsite/?url=<?=urlencode($callbackURL)?>" target="_blank"><i class="fab fa-linkedin" style="font-size: 1.4em;"></i>&nbsp; Linkedin</a>
    <a class="dropdown-item copy-to-clipboard" swal-data="Copied to clipboard!" href="<?=$callbackURL?>"><i class="fas fa-copy" style="font-size: 1.4em;"></i>&nbsp; Copy URL</a>
  </div>
</div>
