<?php

namespace app\widgets;

use Yii;
use app\modules\frontend\models\Profile;

/**
 * Profile Link widget renders link to a player profile based on email, profile_id, player_id, username
 * Requires at least one of the keys to be provided.
 *
 * @author Pantelis Roditis <proditis@echothrust.com>
 */
class ProfileLink extends \yii\bootstrap5\Widget
{
  /**
   * @var string the email (optional)
   */
  public $email;
  /**
   * @var string the profile_id (optional)
   */
  public $profile_id;

  /**
   * @var string the player_id (optional)
   */
  public $player_id;

  /**
   * @var string the username (optional)
   */
  public $username;

  /**
   * @var boolean include actions with the link?
   */
  public $actions = false;

  /**
   * {@inheritdoc}
   */
  public function run()
  {
    $params = [];
    $query = Profile::find()->joinWith('owner');
    if ($this->email) {
      $params['email'] = $this->email;
      $query->andFilterWhere(['player.email' => $this->email]);
    } elseif ($this->profile_id) {
      $params['profile_id'] = $this->profile_id;
      $query->andFilterWhere(['id' => $this->profile_id]);
    } elseif ($this->player_id) {
      $params['player_id'] = $this->player_id;
      $query->andFilterWhere(['player_id' => $this->player_id]);
    } elseif ($this->username) {
      $params['username'] = $this->username;
      $query->andFilterWhere(['player.username' => $this->username]);
    }

    if (empty($params)) {
      throw new \yii\base\InvalidConfigException('ProfileLink widget requires one of: email, profile_id, player_id, or username.');
    }
    if (($model = $query->one()) === null) {
      throw new \yii\base\InvalidConfigException('ProfileLink widget no player profile found.');
    }
    $links[] = \yii\helpers\Html::a($this->username, ['/frontend/profile/view-full', 'id' => $model->id],['target'=>'_blank',]);
    $links[] = \yii\helpers\Html::a(
      '<i class="bi bi-trash"></i>',
      ['/frontend/player/delete', 'id' => $model->player_id],
      [
        'title' => 'Delete this user',
        'data-pjax' => '0',
        'data-method' => 'POST',
        'data' => ['confirm' => "Are you sure you want to delete this user?"]
      ]
    );

    if($this->actions)
      return implode(" ",$links);
    return $links[0];
  }
}
