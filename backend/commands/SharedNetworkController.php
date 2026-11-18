<?php

/**
 * @author Pantelis Roditis <proditis@echothrust.com>
 * @copyright 2026
 * @since 1.0
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;
use app\modules\moderation\models\SharedNetwork;

/**
 * Manages shared networks (universities, NAT pools etc.), excluded
 * from duplicate-IP checks.
 *
 * @author proditis
 */
class SharedNetworkController extends Controller
{

  public $description;
  public $absorb;

  /**
   * Shared network operations.
   */
  public function actionIndex()
  {
    $this->stdout("*** Shared Network COMMAND ***\n");

    echo Table::widget([
      'headers' => ['Action', 'Usage', 'Description'],
      'rows' => [
        ['Action' => 'shared-network/add',  'Usage' => 'shared-network/add <cidr> --description="Universities" [--absorb]', 'Description' => 'Add a shared network range (eg. 10.0.0.0/8). --absorb removes existing entries swallowed by it'],
        ['Action' => 'shared-network/remove',  'Usage' => 'shared-network/remove <id|cidr>', 'Description' => 'Remove a shared network by id or CIDR'],
        ['Action' => 'shared-network/list',  'Usage' => 'shared-network/list', 'Description' => 'List shared networks'],
      ],
    ]);
  }

  /**
   * Add a shared network range.
   * @param string $cidr IPv4 network (eg. 10.0.0.0/8 or 172.16.0.0/255.240.0.0)
   */
  public function actionAdd($cidr)
  {
    $model = new SharedNetwork();
    $model->cidr = $cidr;
    $model->description = $this->description;
    $model->absorb = (bool) $this->absorb;

    if (!$model->save()) {
      foreach ($model->getErrorSummary(true) as $error)
        $this->stderr("* {$error}\n", Console::FG_RED);
      return ExitCode::UNSPECIFIED;
    }

    $this->stdout("Added shared network {$model->cidr}\n", Console::FG_GREEN);
    if ($model->absorb)
      $this->stdout("Covered smaller entries were removed\n");
    return ExitCode::OK;
  }

  /**
   * Remove a shared network by id or CIDR.
   * @param string $idOrCidr numeric id or CIDR (eg. 10.0.0.0/8)
   */
  public function actionRemove($idOrCidr)
  {
    $model = SharedNetwork::findByCidr($idOrCidr);
    if ($model === null) {
      $this->stderr("No shared network found for '{$idOrCidr}'\n", Console::FG_RED);
      return ExitCode::UNSPECIFIED;
    }

    $cidr = $model->cidr;
    $model->delete();
    $this->stdout("Removed shared network {$cidr}\n", Console::FG_GREEN);
    return ExitCode::OK;
  }

  /**
   * List shared networks.
   */
  public function actionList()
  {
    echo Table::widget([
      'headers' => ['ID', 'CIDR', 'Description', 'Created'],
      'rows' => array_map(function ($sn) {
        return [$sn->id, $sn->cidr, $sn->description, $sn->created_at];
      }, SharedNetwork::find()->orderBy(['network' => SORT_ASC])->all()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function options($actionID)
  {
    if ($actionID === 'add')
      return ['description', 'absorb'];
    return [];
  }
}
