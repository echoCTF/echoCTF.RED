<?php

namespace app\commands;

use Yii;
use app\modules\infrastructure\models\DockerContainer;
use app\modules\infrastructure\models\Server;
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
    // targets without a server (NULL) and PVE targets have no docker to clean
    $servers = array_filter($q1->column(), fn($srv) => !empty($srv) && strncmp($srv, 'pve:', 4) !== 0);
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
        $imageIds = [];
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

  /**
   * Delete from each docker server the images the backend did not use there in the last $hours hours.
   * Meant to run every hour. Images in use by a container are refused by docker and stay.
   */
  public function actionPruneUnusedImages(int $hours = 24)
  {
    $rows = Yii::$app->db->createCommand("SELECT server AS srv, image FROM target WHERE active = 1
      UNION SELECT t.server, t.image FROM spin_history h JOIN target t ON t.id = h.target_id WHERE h.created_at >= NOW() - INTERVAL :h1 HOUR
      UNION SELECT s.connstr, t.image FROM target_instance i JOIN target t ON t.id = i.target_id JOIN server s ON s.id = i.server_id
      UNION SELECT s.connstr, t.image FROM target_instance_audit a JOIN target t ON t.id = a.target_id JOIN server s ON s.id = a.server_id WHERE a.ts >= NOW() - INTERVAL :h2 HOUR",
      [':h1' => $hours, ':h2' => $hours])->queryAll();
    $used = array_fill_keys(Server::find()->select('connstr')->column(), []);
    foreach ($rows as $r)
      $used[$r['srv']][] = strpos(basename($r['image']), ':') === false ? $r['image'] . ':latest' : $r['image'];

    foreach ($used as $srv => $images) {
      if (empty($srv) || strncmp($srv, 'pve:', 4) === 0) continue;
      try {
        $dc = new DockerContainer(['server' => $srv, 'ssl' => false, 'timeout' => 9000]);
        $dc->connectAPI();
        foreach ($dc->docker->imageList() as $img) {
          $refs = array_diff((array) $img->getRepoTags(), ['<none>:<none>']) ?: [$img->getId()];
          foreach (array_diff($refs, $images) as $ref) {
            try { $dc->docker->imageDelete($ref); echo "Deleted $ref from $srv\n"; } catch (\Throwable $e) {}
          }
        }
      } catch (\Throwable $e) {
        echo "Failed on $srv: ", $e->getMessage(), "\n";
      }
    }
  }
}
