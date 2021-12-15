<?php

namespace app\modules\settings\models;

/**
 * This is the ActiveQuery class for [[UrlRoute]].
 *
 * @see UrlRoute
 */
class UrlRouteQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UrlRoute[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UrlRoute|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
