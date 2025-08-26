<?php
/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */

/* @var $className string the new migration class name */
echo "<?php\n";
?>

use yii\db\Migration;

class <?= $className ?> extends Migration
{
  public $DROP_SQL="DROP PROCEDURE IF EXISTS {{%<?=$procedureName?>}}";
  public $CREATE_SQL="CREATE PROCEDURE {{%<?=$procedureName?>}}()
  BEGIN
    YOUR CODE HERE
  END";


  public function up()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->CREATE_SQL)->execute();
  }

  public function down()
  {
    $this->db->createCommand($this->DROP_SQL)->execute();
  }
}