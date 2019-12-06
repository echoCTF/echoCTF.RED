<div class="leader">
    <div class="leader-wrap">
      <div class="leader-name" style="margin-bottom: 0.3em"><?=$data->formatted(!$me);?>, <?=$data->ts_ago?> <?php $this->widget('ext.ets.TweetPortlet',['model'=>$data,'type'=>'Stream','priv'=>$me, 'url'=>Yii::app()->controller->createAbsoluteUrl('profile/index',['id'=>$this->curProfile->id])]);?></div>
    </div>
    <div class="border"></div>
</div>
