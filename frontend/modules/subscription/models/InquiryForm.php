<?php
namespace app\modules\subscription\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class InquiryForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            ['email', 'email'],
            ['email', 'default','value'=>Yii::$app->user->identity->email],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    public function defaults()
    {
      $this->email=\Yii::$app->user->identity->email;
      $this->name=\Yii::$app->user->identity->fullname;
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param  string  $email the target email address
     * @return boolean whether the email was sent
     */
    public function sendInquiry()
    {
      $inq=new Inquiry();
      $inq->player_id=\Yii::$app->user->id;
      $inq->answered=false;
      $inq->category='subscription';
      $inq->email=$this->email;
      $inq->name=$this->name;
      $inq->body=$this->body;
      $inq->serialized=json_encode($this);
      $inq->updated_at=new \yii\db\Expression('NOW()');
      $inq->created_at=new \yii\db\Expression('NOW()');
      $inq->save();
    }
}
