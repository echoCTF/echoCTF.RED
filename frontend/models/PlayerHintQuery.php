<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[PlayerHint]].
 *
 * @see PlayerHint
 */
class PlayerHintQuery extends \yii\db\ActiveQuery
{
    public function pending()
    {
        return $this->andWhere('[[status]]=1');
    }
    public function forTarget(int $target_id)
    {
      $this->select('player_hint.*');
      $this->join('LEFT JOIN', 'hint', 'hint.id=player_hint.hint_id');
      $this->andWhere("( hint.finding_id IN (SELECT id FROM finding WHERE target_id=$target_id)  OR hint.treasure_id IN (SELECT id FROM treasure WHERE target_id=$target_id))");
      return $this;
    }

    public function forPlayer(int $player_id)
    {
        return $this->andWhere(['player_id'=>$player_id]);
    }
    public function forAjax()
    {
        return $this->select('player_hint.*,hint.title,hint.message')->joinWith(['hint']);

    }

    /**
     * {@inheritdoc}
     * @return PlayerHint[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PlayerHint|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
