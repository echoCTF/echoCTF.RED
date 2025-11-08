<?php

/**
 * Profile Actions Widget
 */

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ButtonDropdown;

class ProfileActionsWidget extends Widget
{
  public $profile;
  public function init()
  {
    parent::init();
  }

  public function run()
  {
    if (\Yii::$app->user->identity->sSL && $this->profile->isMine)
      $profile_actions = $this->profile->vpnItems;

    $profile_actions['badge'] = ['encode' => false, 'label' => "<i class='fas fa-id-badge'></i>&nbsp; Your badge URL", 'url' => Url::to(['profile/badge', 'id' => $this->profile->id], true), 'linkOptions' => ['class' => 'copy-to-clipboard', 'swal-data' => 'Copied to clipboard!']];
    $profile_actions['edit'] = ['encode' => false, 'label' => "<i class='fas fa-user-edit'></i>&nbsp; Edit your profile settings", 'url' => ['profile/settings'], 'linkOptions' => ['alt' => 'Edit profile and account settings']];
    $profile_actions['profileurl'] = ['encode' => false, 'label' => '<i class="fas fa-id-card"></i>&nbsp; Your profile URL', 'url' => Url::to(['profile/index', 'id' => $this->profile->id], 'https'), 'linkOptions' => ['class' => 'copy-to-clipboard', 'swal-data' => 'Copied to clipboard!']];
    $profile_actions['inviteurl'] = ['encode' => false, 'label' => '<i class="fas fa-link"></i>&nbsp; Your invite URL', 'url' => Url::to(['profile/invite', 'id' => $this->profile->id], true), 'linkOptions' => ['class' => 'copy-to-clipboard', 'swal-data' => 'Copied to clipboard!']];

    if (\Yii::$app->sys->api_bearer_enable === true) {
      if (\Yii::$app->user->identity->apiToken === null) {
        $profile_actions['generate-token'] = ['encode' => false, 'label' => "<i class='fas fa-terminal'></i>&nbsp; Generate API Token", 'url' => Url::to(['profile/generate-token'], true), 'linkOptions' => ['class' => 'text-danger', 'data-swType' => 'question', 'data' => ['confirm' => 'You are about to generate a new API token! Are you sure?', 'method' => 'POST']]];
      } else {
        $profile_actions['copy-token'] = ['encode' => false, 'label' => "<i class='fas fa-terminal'></i>&nbsp; Copy API Token", 'url' => $this->profile->owner->apiToken->token, 'linkOptions' => ['class' => 'copy-to-clipboard', 'swal-data' => 'API token copied to clipboard!']];
      }
    }
    if (\Yii::$app->user->identity->sSL && (time() - strtotime(\Yii::$app->user->identity->sSL->ts)) >= 3600)
      $profile_actions['revoke'] = ['encode' => false, 'label' => "<i class='fas fa-id-card'></i>&nbsp; Regenerate VPN Keys (revoke)", 'url' => Url::to(['profile/revoke'], true), 'linkOptions' => ['class' => 'text-danger', 'data-swType' => 'question', 'data' => ['confirm' => 'You are about to revoke your old keys and generate a new pair!', 'method' => 'POST']]];

    if (Yii::$app->user->identity->onVPN && Yii::$app->user->identity->disconnectQueue === null)
      $profile_actions['disconnect'] = ['encode' => false, 'label' => "<i class='fas fa-shield-virus'></i>&nbsp; Disconnect your VPN", 'url' => Url::to(['profile/disconnect'], true), 'linkOptions' => ['class' => 'text-danger', 'data-swType' => 'question', 'data' => ['confirm' => 'You are about to disconnect your current VPN connection! You will receive another notification once the process is completed!', 'method' => 'POST']]];

    if (\Yii::$app->sys->player_request_delete_allow === true)
      $profile_actions['delete'] = ['encode' => false, 'label' => "<i class='fas fa-user-slash'></i>&nbsp; Delete your account", 'url' => Url::to(['profile/delete'], true), 'linkOptions' => ['class' => 'text-danger', 'data-swType' => 'error', 'data' => ['confirm' => 'You are about to delete your account! This is irreversible and will cause loss of all your progress. If you currently have an active subscription, the deletion will happen when it ends.', 'method' => 'POST']]];

    if (\Yii::$app->sys->profile_card_disabled_actions !== false && explode(",", \Yii::$app->sys->profile_card_disabled_actions) !== []) {
      $disabled_keys = explode(",", \Yii::$app->sys->profile_card_disabled_actions);
      foreach ($disabled_keys as $dkey) {
        if (array_key_exists(trim($dkey), $profile_actions))
          unset($profile_actions[trim($dkey)]);
      }
    }

    return ButtonDropdown::widget([
      'label' => '<i class="fas fa-users-cog"></i>&nbsp; Profile Actions',
      'containerOptions' => ['class' => 'row d-flex'],
      'options' => ['class' => 'btn-primary text-bold btn-block', 'style' => 'color: black'],
      'encodeLabel' => false,
      'dropdown' => [
        'items' => $profile_actions,
      ],
    ]);
  }
}
