<?php

namespace app\modules\content\models;

/**
 * This is the ActiveQuery class for [[EmailTemplate]].
 *
 * @see EmailTemplate
 */
class EmailTemplateQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return EmailTemplate[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return EmailTemplate|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
