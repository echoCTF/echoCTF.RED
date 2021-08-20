<?php
use yii\helpers\Html;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <script type="application/ld+json">
[{
  "@context": "http://schema.org/",
  "@type": "Organization",
  "logo": "https://<?=Yii::$app->sys->offense_domain?>/images/logo.png"
},{
  "@context": "http://schema.org/",
  "@type": "EmailMessage",
  "subjectLine": "<?=$this->title?>"
},{
  "@context": "http://schema.org/",
  "@type": "DiscountOffer",
  "description": "",
  "availabilityStarts": "2020-01-01T00:00:01+00:00",
  "availabilityEnds": ""
},{
  "@context": "http://schema.org/",
  "@type": "PromotionCard",
  "image": "https://<?=Yii::$app->sys->offense_domain?>/images/logo.png"
}]
    </script>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

  </head>
  <body>
    <?php $this->beginBody() ?>
    <?= $content ?>
    <?php $this->endBody() ?>
  </body>
</html>
<?php $this->endPage() ?>
