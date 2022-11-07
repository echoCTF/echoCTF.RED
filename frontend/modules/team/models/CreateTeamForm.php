<?php
namespace app\modules\team\models;

use Yii;
use yii\base\Model;
use yii\behaviors\AttributeTypecastBehavior;
use app\modules\team\models\Team;
/**
 * Signup form
 */
class CreateTeamForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $name;
    public $description;
    public $uploadedAvatar;
    public $logo;
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => ['name', 'description'],
            self::SCENARIO_UPDATE => ['name', 'description', 'uploadedAvatar'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
      return [
          [['name','description'], 'trim'],
          ['name', 'required'],
          ['name', 'unique', 'targetClass' => 'app\modules\team\models\Team', 'message' => \Yii::t('app','A team with this name already exists.')],
          [['name'], 'string', 'length' => [3, 32]],
          [['uploadedAvatar'], 'file',  'extensions' => 'png', 'mimeTypes' => 'image/png','maxSize' =>  512000, 'tooBig' => \Yii::t('app','File larger than expected, limit is {sizeLimit}',['sizeLimit'=>'500KB'])],
      ];
    }

    /**
     * Creates a team
     *
     * @return bool whether the team creation was successful
     */
    public function create()
    {
      if(!$this->validate())
      {
          return false;
      }
      $transaction = Yii::$app->db->beginTransaction();
      try {
        $team=new Team(['scenario' => CreateTeamForm::SCENARIO_CREATE]);
        $team->academic=Yii::$app->user->identity->academic;
        $team->name=$this->name;
        $team->description=$this->description;
        $team->owner_id=Yii::$app->user->id;
        if($team->save())
        {
          $tp=new TeamPlayer();
          $tp->team_id=$team->id;
          $tp->player_id=Yii::$app->user->id;
          $tp->approved=1;
          if($tp->save())
          {
            $transaction->commit();
            return true;
          }
        }
      }
      catch (\Exception $e)
      {
        $transaction->rollback();
      }
      return false;
    }

    public function attributeLabels()
    {
        return [
          'name'=>\Yii::t('app','A name for your team'),
          'description'=>\Yii::t('app','A short description for your team'),
          'avatar'=>\Yii::t('app','An image to be used as team avatar'),
        ];
    }

}
