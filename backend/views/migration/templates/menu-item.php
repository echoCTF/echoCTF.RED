<?php
/* @var $className string */
/* @var $namespace string|null */
/* @var $itemLabel string */
/* @var $parentLabel string */
/* @var $itemUrl string */

echo "<?php\n";
if (!empty($namespace)) {
    echo "\nnamespace $namespace;\n";
}
?>

use yii\db\Migration;

class <?= $className ?> extends Migration
{
    public $parentLabel = '<?= addslashes($parentLabel) ?>';
    public $itemLabel = '<?= addslashes($itemLabel) ?>';
    public $itemUrl = '<?= addslashes($itemUrl) ?>';

    public function safeUp()
    {
        $parentId = (new \yii\db\Query())
            ->select('id')
            ->from('mui_menu')
            ->where(['like', 'label', $this->parentLabel])
            ->andWhere(['parent_id' => null])
            ->scalar();

        if (!$parentId) {
            throw new \yii\base\Exception('Parent menu "' . $this->parentLabel . '" not found, fix lookup.');
        }

        $sortOrder = (new \yii\db\Query())
            ->from('mui_menu')
            ->where(['parent_id' => $parentId])
            ->count();

        $this->insert('mui_menu', [
            'label' => $this->itemLabel,
            'url' => $this->itemUrl,
            'visibility' => 'admin',
            'parent_id' => $parentId,
            'sort_order' => $sortOrder,
            'enabled' => 1,
        ]);
    }

    public function safeDown()
    {
        $this->delete('mui_menu', [
            'url' => $this->itemUrl,
        ]);
    }
}