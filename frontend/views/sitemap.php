<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <url>
      <loc><?=$BASEURL?></loc>
      <lastmod>2005-01-01</lastmod>
      <changefreq>monthly</changefreq>
      <priority>0.8</priority>
   </url>
<!-- TARGETS -->
<?php foreach($targets as $target):?>
   <url>
      <loc><?=$BASEURL?>target/<?=$target->id?></loc>
      <lastmod><?=$target->ts?></lastmod>
      <changefreq>weekly</changefreq>
   </url>
<?php endforeach;?>
<!-- /TARGETS -->

<!-- PROFILES -->
<?php foreach($players as $player):?>
<?php if($player->profile->visibility==="public"):?>
<url>
   <loc><?=$BASEURL?>profile/<?=$player->profile->id?></loc>
   <lastmod><?=$player->profile->updated_at?></lastmod>
   <changefreq>weekly</changefreq>
</url>
<?php endif;?>
<?php endforeach;?>
<!-- /PROFILES -->

<!-- targetVSplayer -->
<?php foreach($TvsP as $tvsp):?>
<url>
   <loc><?=$BASEURL?>target/<?=$tvsp->target_id?>/vs/<?=$tvsp->player_id?></loc>
   <changefreq>weekly</changefreq>
</url>
<?php endforeach;?>
<!-- /targetVSplayer -->

</urlset>
