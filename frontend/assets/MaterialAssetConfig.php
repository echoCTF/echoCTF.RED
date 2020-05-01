<?php
namespace app\assets;

use Yii;

class MaterialAssetConfig
{
    public static function sidebarColor()
    {
        /** @var Asset $bundle */
        $bundle=Yii::$app->assetManager->getBundle('app\assets\MaterialAsset');

        return $bundle->sidebarColor;
    }


    public static function sidebarBackgroundColor()
    {
        /** @var Asset */
        $bundle=Yii::$app->assetManager->getBundle('app\assets\MaterialAsset');

        return $bundle->sidebarBackgroundColor;
    }

    public static function sidebarBackgroundImage()
    {
        /** @var Asset */
        $bundle=Yii::$app->assetManager->getBundle('app\assets\MaterialAsset');

        return $bundle->sidebarBackgroundImage;
    }

    public static function siteTitle()
    {
        /** @var Asset */
        $bundle=Yii::$app->assetManager->getBundle('app\assets\MaterialAsset');

        return $bundle->siteTitle;
    }

    public static function logoMini()
    {
        /** @var Asset */
        $bundle=Yii::$app->assetManager->getBundle('app\assets\MaterialAsset');

        return $bundle->logoMini;
    }
}
