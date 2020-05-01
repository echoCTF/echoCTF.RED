<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\target\models\Target;

class GeneratorController extends Controller {

  /**
   * Generate sitemap.xml
   */
    public function actionSitemap($baseurl='https://echoctf.red/')
    {
      $targets=Target::find()->active()->all();
      $contents=$this->renderFile(\Yii::getAlias('@app/views/sitemap.php'), ['targets'=>$targets, 'BASEURL'=>$baseurl, 'profiles'=>[], 'TvsP'=>[]]);
      file_put_contents(\Yii::getAlias('@app/web/sitemap.xml'),$contents);
    }
}
