<?php
    $menu = $img = "";
    $config = new rce\material\Config();
    $menu = app\models\Menu::getMenu();
    $img = $config::sidebarBackgroundImage();
?>
<div class="sidebar" data-color="<?= $config::sidebarColor()  ?>" data-background-color="<?= $config::sidebarBackgroundColor()  ?>">
    <div class="logo">
        <a href="#" class="simple-text logo-mini">
            <img src="<?=$config::logoMini();?>" style="img-fluid">
        </a>
        <a href="#" class="simple-text logo-normal">
            <?= $config::siteTitle() ?>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <?= $menu ?>
    </div>
</div>
