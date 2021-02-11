<?php

namespace app\modules\challenge\models;

/**
 * This is the ActiveQuery class for [[Challenge]].
 *
 * @see Challenge
 */
class ChallengeQuery extends \yii\db\ActiveQuery
{

    public function timed()
    {
      return $this->andWhere(['t.timer'=>1]);
    }

    public function active()
    {
      return $this->andWhere(['t.active'=>1]);
    }
    public function public()
    {
      return $this->andWhere(['t.public'=>1]);
    }

    public function player_progress($player_id)
    {
      $this->alias('t');
      $this->select(['t.*,count(question.id) as total_questions,count(player_question.question_id) as player_answers']);
      $this->join('LEFT JOIN', 'question', 'question.challenge_id=t.id');
      $this->join('LEFT JOIN', 'player_question', 'player_question.question_id=question.id and player_question.player_id='.$player_id);
      $this->groupBy('t.id');
      return $this;
    }

    /**
     * {@inheritdoc}
     * @return Challenge[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Challenge|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
