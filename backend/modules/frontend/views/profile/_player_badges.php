<div class="panel">
  <div class="panel-heading">
    <span class="panel-icon">
      <i class="far fa-id-badge"></i>
    </span>
    <span class="panel-title"> Badges</span>
  </div>
  <div class="panel-body pb5">
    <?php foreach($model->owner->badges as $badge):?>
    <span class="label label-primary mr5 mb10 ib lh15"><?=$badge->name?></span>
    <?php endforeach;?>
  </div>
</div>
