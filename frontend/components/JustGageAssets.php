<?php

/**
 * JustGageAssets class file.
 *
 * @author Simon Smith <simon@simonsmith.ca>
 * @link https://github.com/simonmesmith/yii2-justgage/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version
 */

namespace app\components;

use yii\web\AssetBundle;

/**
 * Asset bundle for JustGage.
 */
class JustGageAssets extends AssetBundle
{

  //public $sourcePath = '@vendor/simonmesmith/yii2-justgage/assets';
  public $depends=['yii\web\JqueryAsset'];
  public $js=['js/raphael-2.1.4.min.js', 'js/justgage.min.js'];

}
