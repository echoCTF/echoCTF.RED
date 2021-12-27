<h3>Badges</h3>
<div class="row game-badges">
  <?php foreach($game->badges->received_by($profile->player_id)->all() as $badge):?>
  <div class="col col-xl-4 col-sm-12 col-md-6 col-lg-6">
    <div class="iconic-card">
      <center><?=$badge->pubname?></center>
      <h3><?=$badge->name?></h3>
      <?php if(!Yii::$app->user->isGuest && $profile->player_id===Yii::$app->user->id):?>
        <p><?=$badge->description?></p>
      <?php else:?>
        <p><?=$badge->pubdescription?></p>
      <?php endif;?>
    </div>
  </div>
  <?php endforeach;?>
</div>
