<?php
/**
 * Check email address MX Records for denied servers.
 */
namespace app\components\validators;

use yii\base\Model;
use yii\validators\Validator;
use yii\helpers\ArrayHelper;

class PlayerEmailValidator extends Validator
{
    protected function validateValue($value)
    {
      $model = new Email();
      $model->attributes=['email'=>$value];
      if(!$model->validate())
        return [implode(", ",$model->getErrors('email')),[]];
      return null;
    }

}

class Email extends Model
{
    public $email;

    public function rules()
    {
      return [
        [['email'], 'filter', 'filter' => 'trim'],
        [['email'], 'required'],
        ['email', 'email'],
        ['email', 'string', 'max' => 255],
        [['email'], 'filter', 'filter' => 'strtolower'],
        // check for banned
        ['email', 'unique', 'targetClass' => '\app\modules\frontend\models\BannedPlayer', 'message' => 'This email is banned.'],
        ['email', function ($attribute, $params) {
            $count = \Yii::$app->db->createCommand('SELECT COUNT(*) FROM banned_player WHERE :email LIKE email')
              ->bindValue(':email', $this->email)
              ->queryScalar();

            if (intval($count) !== 0)
              $this->addError($attribute, 'This email is banned.',);
          }
        ],
        ['email', 'email', 'checkDNS' => true, 'message' => 'This domain does not resolve.', 'skipOnEmpty' => true, 'skipOnError' => false],
        ['email',    '\app\components\validators\VerifymailValidator', 'when' => function ($model) {
          return (bool)\Yii::$app->sys->signup_ValidatemailValidator;
        }, 'skipOnEmpty' => true, 'skipOnError' => false],
        ['email',    '\app\components\validators\StopForumSpamValidator',       'max' => \Yii::$app->sys->signup_StopForumSpamValidator, 'when' => function ($model) {
          return \Yii::$app->sys->signup_StopForumSpamValidator !== false;
        }, 'skipOnEmpty' => true, 'skipOnError' => false],
        ['email', '\app\components\validators\MXServersValidator', 'mxonly' => true, 'when' => function ($model) {
          return \Yii::$app->sys->signup_MXServersValidator !== false;
        }, 'skipOnEmpty' => true, 'skipOnError' => false],

        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email Address',
        ];
    }
}