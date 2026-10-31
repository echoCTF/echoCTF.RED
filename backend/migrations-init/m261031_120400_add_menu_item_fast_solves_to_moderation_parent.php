<?php

use yii\db\Migration;

class m261031_120400_add_menu_item_fast_solves_to_moderation_parent extends Migration
{
    public $parentLabel = 'Moderation';
    public $itemLabel = 'Fast Solves';
    public $itemUrl = '/moderation/default/fast-solves';

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