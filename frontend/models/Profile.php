<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\behaviors\AttributeTypecastBehavior;
use app\modules\game\models\Headshot;
use app\modules\target\models\Target;
use app\modules\target\models\Writeup;

/**
 * This is the model class for table "profile".
 *
 * @property string $id
 * @property int $player_id
 * @property string $visibility
 * @property string $bio
 * @property string $country Player Country
 * @property string $avatar Profile avatar
 * @property string $discord Discord handle
 * @property string $twitter Twitter handle
 * @property string $github Github handle
 * @property string $youtube Youtube handle
 * @property string $twitch Twitch handle
 * @property string $htb HTB avatar
 * @property boolean $terms_and_conditions
 * @property boolean $mail_optin
 * @property boolean $gdpr
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $visible
 * @property boolean $approved_avatar
 * @property string $avtr
 * @property boolean $isMine
 *
 * @property Owner $owner
 * @property Score $score
 * @property Rank $rank
 * @property HeadshotsCount $headshotsCount
 * @property Experience $experience
 * @property TotalTreasures $totalTreasures
*/
class Profile extends ProfileAR
{
  public $visibilities=[
      'private'=>'Private',
      'public'=>'Public',
      'ingame'=>'In Game',
    ];

    public function getVisible(): bool
    {

      if($this->visibility === 'public') return true;
      if($this->visibilityAllowed) return true;
      return !$this->visibilityDenied;
    }

    public function getVisibilityAllowed(): bool
    {
      if(!Yii::$app->user->isGuest)
      {
        if(intval(Yii::$app->user->id) === intval($this->player_id)) return true;
        if(Yii::$app->user->identity->isAdmin) return true;
      }
      return false;
    }

    public function getVisibilityDenied(): bool
    {
      if(Yii::$app->sys->player_profile === false) return true;

      if($this->visibility === 'private') return true;

      return false;
    }


    /**
     * Get profile link based on player permissions,
     */
    public function getLink()
    {

      if(intval(Yii::$app->user->id) === intval($this->player_id))
        return Html::a(Html::encode($this->owner->username), ['/profile/me']);
      else if($this->visible === true)
        return Html::a(Html::encode($this->owner->username), ['/profile/index', 'id'=>$this->id], ['data-pjax'=>0]);
      return Html::encode($this->owner->username);
    }

    public function getLinkto()
    {

      if(intval(Yii::$app->user->id) === intval($this->player_id)) return Url::to(['/profile/me']);
      else if($this->visible === true) return Url::to(['/profile/index', 'id'=>$this->id]);
      return null;
    }

    public function getExperience()
    {
      return Experience::find()->where("{$this->score->points} BETWEEN min_points AND max_points");
    }

    public function getTotalTreasures()
    {
      return Yii::$app->db->createCommand('SELECT count(*) FROM player_treasure WHERE player_id=:player_id')->bindValue(':player_id', $this->player_id)->queryScalar();
    }

    public function getTotalFindings()
    {
      return Yii::$app->db->createCommand('SELECT count(*) FROM player_finding WHERE player_id=:player_id')->bindValue(':player_id', $this->player_id)->queryScalar();
    }

    public function getHeadshotsCount():int {
      return (int) $this->hasMany(Headshot::class, ['player_id' => 'player_id'])->count();
    }

    public function getIsMine():bool
    {
      if(Yii::$app->user->isGuest)
        return false;
      if(Yii::$app->user->id === $this->player_id)
        return true;
      return false;
    }

    public function getTwitterHandle()
    {
      if($this->twitter != "")
      {
        return $this->twitter{0} === '@' ? $this->twitter : '@'.$this->twitter;
      }
      return $this->owner->username;
    }

    public function getAvtr()
    {
      if($this->approved_avatar || $this->isMine)
        return $this->avatar;
      return '../default_avatar.png';
    }

    public function getBraggingRights()
    {
      if($this->rank)
      {
        $msg=sprintf("I am at the %s place with %d pts", $this->rank->ordinalPlace, $this->score->points);
        if($this->headshotsCount > 0)
        {
          $msg.=sprintf(', and %d headshots', $this->headshotsCount);
        }
      }
      else
        $msg=sprintf("I have just joined echoCTF.RED!");
      return $msg;
    }


}
