  <?=$model->name?>:
    hostname: <?=$model->name."\n"?>
    container_name: <?=strtolower(str_replace(' ','',trim(Yii::$app->sys->event_name)))?>_<?=$model->name."\n"?>
    restart: "always"
    image: <?=$model->image."\n"?>
<?php if($model->targetVariables):?>
    environment:
<?php foreach($model->targetVariables as $item):?>
    - <?=$item->key?>=<?=$item->val."\n"?>
<?php endforeach;?>
<?php endif;?>
    networks:
      <?=$model->net?>:
        ipv4_address: "<?=$model->ipoctet?>"
