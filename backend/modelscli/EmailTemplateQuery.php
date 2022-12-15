<?php

namespace app\modelscli;

/**
 * This is the ActiveQuery class for [[EmailTemplate]].
 *
 * @see EmailTemplate
 */
class EmailTemplateQuery extends \yii\db\ActiveQuery
{
    public function last($minutes=3600)
    {
        return $this->andWhere("updated_at>=NOW() - INTERVAL $minutes MINUTE");
    }

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
