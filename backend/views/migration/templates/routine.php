<?php
/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */

/* @var $className string the new migration class name */
/* @var $routineName string the name of the routine */
/* @var $returns string the type of data returned */
/* @var $deterministic boolean if the routine is DETERMINISTIC or READS SQL DATA */

echo "<?php\n";
?>

use yii\db\Migration;

class <?= $className ?> extends Migration
{
  public $DROP_SQL="DROP FUNCTION IF EXISTS {{%<?=$routineName?>}}";
<?php if($action=='delete' || $action=='drop'):?>
  public $CREATE_SQL="SELECT true";
<?php else:?>
  public $CREATE_SQL="CREATE FUNCTION {{%<?=$routineName?>}}() RETURNS <?=$returns?>
<?php if($deterministic):?>
  DETERMINISTIC
<?php else: ?>
  READS SQL DATA
<?php endif;?>
  BEGIN
    YOUR CODE HERE
    RETURN something;
  END";
<?php endif;?>


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
