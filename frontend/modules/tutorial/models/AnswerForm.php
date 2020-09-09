<?php

namespace app\modules\tutorial\models;

use Yii;
use yii\base\Model;

/**
 * AnswerForm is the model behind the Tutorial task answer form.
 * @property TutorialTask $task
 */
class AnswerForm extends Model
{
    public $answer;
    public $points;
    protected $_task;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['answer'], 'required'],
            [['answer'], 'exist',
              'targetClass' => TutorialTask::class,
              'targetAttribute' => ['answer'=>'answer']]
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

    public function give($tutorial_id)
    {
      // first check if it is a valid answer
      $this->_task=TutorialTask::find()->where(['tutorial_id'=>$tutorial_id, 'answer'=>$this->answer])->one();
      if(!($this->_task instanceof TutorialTask))
      {
        $this->addError('answer', 'Invalid answer');
        return false;
      }

      if($this->_task->answered !== null)
      {
        $this->addError('answer', 'You have already answered this task.');
        return false;
      }

      $pq=new PlayerQuestion;
      $pq->player_id=(int) Yii::$app->user->id;
      $pq->task_id=$this->_task->id;
      if($pq->save())
      {
        $pq->refresh();
        $this->points=$pq->points;
        return true;
      }
      else
      {
        $this->addError('answer', 'Failed to save the given answer. Contact the administrators if the problem persists.');
        return false;
      }
    }
    public function getPlayerTutorialTask()
    {
      return $this->_task;
    }
}
