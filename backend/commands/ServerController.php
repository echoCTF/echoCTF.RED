<?php

namespace app\commands;

use Yii;
use app\modules\infrastructure\models\DockerContainer;
use app\components\ConsoleLockController as Controller;

class ServerController extends Controller
{
  public function actionPruneImages(int $hoursAgo = 360)
  {
    $q1 = (new \yii\db\Query())
      ->select(['value' => 'server'])
      ->from('target');

    $q2 = (new \yii\db\Query())
      ->select(['value' => 'connstr'])
      ->from('server');

    $q1->union($q2);
    $servers = $q1->column();
    $filters = [
      'until' => [$hoursAgo . 'h']
    ];
    foreach ($servers as $srv) {
      echo "Cleaning images from: ".$srv."\n";
      try {
        $dc = new DockerContainer(['server' => $srv, 'ssl' => false, 'timeout' => 9000]);
        $dc->connectAPI();

        $result = $dc->docker->imageList([
          'filters' => json_encode($filters)
        ]);

        echo "Found " . count($result) . " images to be deleted\n";
        foreach ($result as $r) {
          $imageIds[] = $r->getId();
        }

        foreach ($imageIds as $id) {
          try {
            $dc->docker->imageDelete($id);
          } catch (\Throwable $e) {
            echo "Failed to delete: " . $id . "\n";
          }
        }

        $dc->disconnectApi();
      } catch (\Throwable $e) {
        echo "Failure to clear images from: ".$srv." (".$e->getMessage().")\n";
      }
    }
  }
}
