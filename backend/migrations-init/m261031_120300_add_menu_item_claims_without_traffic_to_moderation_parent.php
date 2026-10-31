<?php

use yii\db\Migration;

class m261031_120300_add_menu_item_claims_without_traffic_to_moderation_parent extends Migration
{
    public $parentLabel = 'Moderation';
    public $itemLabel = 'Claims Without Traffic';
    public $itemUrl = '/moderation/default/claims-without-traffic';

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