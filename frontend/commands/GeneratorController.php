<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\target\models\Target;

class GeneratorController extends Controller {

  /**
   * Generate sitemap.xml
   */
   public function actionSitemap()
   {
     $targets=Target::find()->active()->all();
     $contents=$this->renderFile(\Yii::getAlias('@app/views/sitemap.php'), ['targets'=>$targets, 'BASEURL'=>'https://echoctf.red/', 'profiles'=>[], 'TvsP'=>[]], true);
     file_put_contents(Yii::getAlias('@app/web/sitemap.xml'),$content);
   }
}
