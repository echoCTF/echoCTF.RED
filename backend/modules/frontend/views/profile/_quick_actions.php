<div class="dropdown">
<?php
use yii\bootstrap5\ButtonDropdown;

$player_actions = [
  '<div class="text-center"><b>Player Actions</b></div>',
  ['label' => 'View player', 'url' => ['player/view', 'id' => $model->player_id]],
  ['label' => 'Update player', 'url' => ['player/update', 'id' => $model->player_id]],
  [
    'label' => 'Set Delete status for player', 'url' => ['player/set-deleted', 'id' => $model->player_id], 'linkOptions' => [
      'data' => [
        'confirm' => Yii::t('app', 'Are you sure you want to set the deleted status for the player?'),
        'method' => 'post',
      ]
    ]
  ],
  [
    'label' => 'Delete player', 'url' => ['player/delete', 'id' => $model->player_id], 'linkOptions' => [
      'data' => [
        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
        'method' => 'post',
      ]
    ]
  ],
  [
    'label' => 'Reset auth_key', 'url' => ['player/reset-authkey', 'id' => $model->player_id], 'linkOptions' => [
      'class' => 'text-danger',
      'title' => 'Reset player auth_key (force logout)',
      'data' => [
        'confirm' => Yii::t('app', 'Are you sure you want to reset the player auth_key?'),
        'method' => 'post',
      ]
    ]
  ]
];
$frontend_shortcuts = [
  '<div class="text-center"><b>Frontend Actions</b></div>',
  ['label' => 'Download player ovpn', 'url' => ['/frontend/player/ovpn', 'id' => $model->player_id]],
  ['label' => 'Player profile', 'url' => "//" . Yii::$app->sys->offense_domain . '/profile/' . $model->id, 'linkOptions' => ['target' => '_blank']],
  ['label' => 'Activation URL', 'url' => "//" . Yii::$app->sys->offense_domain . '/verify-email?token=' . $model->owner->verification_token, 'linkOptions' => ['target' => '_blank'], 'visible' => $model->owner->verification_token != null],
  ['label' => 'Password Reset URL', 'url' => "//" . Yii::$app->sys->offense_domain . '/reset-password?token=' . $model->owner->password_reset_token, 'linkOptions' => ['target' => '_blank'], 'visible' => $model->owner->password_reset_token != null],
];

$profile_actions = [
  '<div class="text-center"><b>Profile Actions</b></div>',
  ['label' => 'Update profile', 'url' => ['update', 'id' => $model->id]],
  [
    'label' => 'Delete profile', 'url' => ['delete', 'id' => $model->id], 'linkOptions' =>
    [
      'data' => [
        'confirm' => Yii::t('app', 'Are you sure you want to delete this profile?'),
        'method' => 'post',
      ],
    ]
  ],
];

if (\Yii::$app->sys->player_require_approval === true)
{
  if ($model->owner->approval == 0 || $model->owner->approval==3)
  {
    $player_actions[] =  [
      'label' => 'Approve Player', 'url' => ['player/approve', 'id' => $model->player_id], 'linkOptions' => [
        'data' => [
          'confirm' => Yii::t('app', 'Are you sure you want to approve this player?'),
          'method' => 'post',
        ]
      ]
    ];
  }
  if ($model->owner->approval == 0 || $model->owner->approval == 1)
  {
    $player_actions[] =  [
      'label' => 'Reject Player', 'url' => ['player/reject', 'id' => $model->player_id], 'linkOptions' => [
        'data' => [
          'confirm' => Yii::t('app', 'Are you sure you want to reject this player?'),
          'method' => 'post',
        ]
      ]
    ];
  }

}
echo ButtonDropdown::widget([
  'label' => 'Quick actions',
  'options' => ['encodeLabels' => false],
  'dropdown' => [
    'items' => array_merge($frontend_shortcuts, ['<div class="dropdown-divider"></div>',], $player_actions, ['<div class="dropdown-divider"></div>'], $profile_actions),
  ]
]);
if (\Yii::$app->sys->player_require_approval === true)
{
  echo "<b>Status: </b>",$model->owner::APPROVAL[$model->owner->approval],"\n";
}
?>
</div>
