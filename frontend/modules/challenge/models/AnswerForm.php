<?php

namespace app\modules\challenge\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 * @property Question $question
 */
class AnswerForm extends Model
{
    public $answer;
    public $points;
    protected $_question;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['answer'], 'required'],
            [['answer'], 'string','max'=>128],
            [['answer'], 'exist',
              'targetClass' => Question::class,
              'targetAttribute' => ['answer'=>'code']]
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'answer' => 'Answer',
        ];
    }

    public function give($challenge_id)
    {
      // first check if it is a valid answer
      $this->_question=Question::find()->where(['challenge_id'=>$challenge_id, 'code'=>$this->answer])->one();
      if(!($this->_question instanceof Question))
      {
        $this->addError('answer', \Yii::t('app','Invalid answer'));
        return false;
      }

      if($this->_question->answered !== null)
      {
        $this->addError('answer', \Yii::t('app','You have already answered this question.'));
        return false;
      }

      $pq=new PlayerQuestion;
      $pq->player_id=(int) Yii::$app->user->id;
      $pq->question_id=$this->_question->id;
      if($pq->save())
      {
        $pq->refresh();
        $this->points=$pq->points;
        return true;
      }
      else
      {
        $this->addError('answer', \Yii::t('app','Failed to save the given answer. Contact the administrators if the problem persists.'));
        return false;
      }
    }
    public function getQuestion()
    {
      return $this->_question;
    }
}
