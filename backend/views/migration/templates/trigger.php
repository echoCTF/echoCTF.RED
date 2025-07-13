<?php
/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */

/* @var $className string the new migration class name */
$triggerName=sprintf("t%s%s_%s",strtolower($timing[0] ?? ''),strtolower($event[0] ?? ''),$table);
echo "<?php\n";
?>

use yii\db\Migration;

class <?= $className ?> extends Migration
{
  public $DROP_SQL="DROP TRIGGER IF EXISTS {{%<?=$triggerName?>}}";
  public $CREATE_SQL="CREATE TRIGGER {{%<?=$triggerName?>}} AFTER <?=$event?> ON {{%<?=$table?>}} FOR EACH ROW
  thisBegin:BEGIN
  IF (@TRIGGER_CHECKS = FALSE) THEN
      LEAVE thisBegin;
  END IF;
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