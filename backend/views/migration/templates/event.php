<?php
/**
 * @var string $className
 * @var string $action
 * @var string $eventName
 * @var int    $intervalValue
 * @var string $intervalUnit
 * @var string $starts
 */
?>

<?php echo "<?php\n"; ?>

use yii\db\Migration;

class <?= $className ?> extends Migration
{
  public $DROP_SQL = "DROP EVENT IF EXISTS {{%<?= $eventName ?>}}";
<?php if ($action !== 'drop' && $action !== 'delete'): ?>
  public $CREATE_SQL = "CREATE EVENT {{%<?= $eventName ?>}} ON SCHEDULE EVERY <?= $intervalValue ?> <?= $intervalUnit ?> ON COMPLETION PRESERVE ENABLE DO
  BEGIN
  END";
<?php endif;?>

  public function up()
  {
<?php if ($action === 'drop' || $action === 'delete'): ?>
    $this->db->createCommand($this->DROP_SQL)->execute();
<?php else: ?>
    $this->db->createCommand($this->DROP_SQL)->execute();
    $this->db->createCommand($this->CREATE_SQL)->execute();
<?php endif; ?>
  }

  public function down()
  {
<?php if ($action === 'drop' || $action === 'delete'): ?>
    return true;
<?php else: ?>
    $this->db->createCommand($this->DROP_SQL)->execute();
<?php endif;?>
  }
}
