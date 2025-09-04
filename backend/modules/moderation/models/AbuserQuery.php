<?php

namespace app\modules\moderation\models;

/**
 * This is the ActiveQuery class for [[Abuser]].
 *
 * @see Abuser
 */
class AbuserQuery extends \yii\db\ActiveQuery
{
  public function init()
  {
    parent::init();

    if ($this->select === null) {
      $this->select(['abuser.*', 'TS_AGO(abuser.created_at) as created_at_ago','TS_AGO(abuser.updated_at) as updated_at_ago']);
    }
  }

  /**
   * {@inheritdoc}
   * @return Abuser[]|array
   */
  public function all($db = null)
  {
    return parent::all($db);
  }

  /**
   * {@inheritdoc}
   * @return Abuser|array|null
   */
  public function one($db = null)
  {
    return parent::one($db);
  }
}
