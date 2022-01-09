<?php

namespace app\modelscli;

/**
 * This is the ActiveQuery class for [[VpnTemplate]].
 *
 * @see VpnTemplate
 */
class VpnTemplateQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return VpnTemplate[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return VpnTemplate|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
