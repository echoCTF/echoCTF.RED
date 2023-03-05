<?php
namespace app\modules\frontend\actions\player;

use Yii;

use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\frontend\models\TeamPlayer;
use app\modules\frontend\models\PlayerSsl;
use app\modules\frontend\models\PlayerSearch;
use app\modules\settings\models\Sysconfig;
use app\modules\frontend\models\ImportPlayerForm;
use yii\web\UploadedFile;
use yii\helpers\Html;

class ImportAction extends \yii\base\Action
{

    /**
     * Imports Players from uploaded CSV file.
     * If import is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function run()
    {
      $model=new ImportPlayerForm();

      if(Yii::$app->request->isPost)
      {
          $model->attributes=Yii::$app->request->post()["ImportPlayerForm"];
          $model->csvFile=UploadedFile::getInstance($model, 'csvFile');
          if($model->upload() && $model->parseCSV())
          {
              $trans=Yii::$app->db->beginTransaction();
              try
              {
                $model->processCsvRecords();
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', Yii::t('app','successful import of csv records'));
              }
              catch(\Exception $e)
              {
                $trans->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app','Failed to import file, {exception}',['exception'=>Html::encode($e->getMessage())]));
              }
          }
      }

      return $this->controller->render('import', ['model' => $model]);
    }
}
