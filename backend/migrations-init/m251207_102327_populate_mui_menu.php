<?php

use yii\db\Migration;

class m251207_102327_populate_mui_menu extends Migration
{
  public $items = [
    [
      'label' => '<i class="bi bi-globe2"></i> Content',
      'url' => ['/content/default/index'],
      'icon' => 'fas fa-money-check-alt',
      'visibility' => 'admin',
      'items' => [
        ['label' => 'News', 'url' => ['/content/news/index'], 'visibility' => 'admin'],
        ['label' => 'Writeup rules', 'url' => ['/content/default/writeup-rules'], 'visibility' => 'admin',],
        '<div class="dropdown-header">Help Sections</div>',
        ['label' => 'FAQ', 'url' => ['/content/faq/index'], 'visibility' => 'user',],
        ['label' => 'Rules', 'url' => ['/content/rule/index'], 'visibility' => 'user',],
        ['label' => 'Objectives', 'url' => ['/content/objective/index'], 'visibility' => 'user',],
        ['label' => 'Credits', 'url' => ['/content/credits/index'], 'visibility' => 'user',],
        ['label' => 'Instructions', 'url' => ['/content/instruction/index'], 'visibility' => 'user',],
        '<div class="dropdown-header">Overrides</div>',
        ['label' => 'CSS Override', 'url' => ['/content/default/css-override'], 'visibility' => 'user',],
        ['label' => 'JS Override', 'url' => ['/content/default/js-override'], 'visibility' => 'user',],
        ['label' => 'Layout Override', 'url' => ['/content/layout-override/index'], 'visibility' => 'user',],
        '<div class="dropdown-header">Static</div>',
        ['label' => 'Menu items', 'url' => ['/content/default/menu-items'], 'visibility' => 'user',],
        ['label' => 'Frontpage Scenario', 'url' => ['/content/default/frontpage-scenario'], 'visibility' => 'user',],
        ['label' => 'Offense Scenario', 'url' => ['/content/default/offense-scenario'], 'visibility' => 'user',],
        ['label' => 'Defense Scenario', 'url' => ['/content/default/defense-scenario'], 'visibility' => 'user',],
        ['label' => 'Footer logos', 'url' => ['/content/default/footer-logos'], 'visibility' => 'user',],
        ['label' => 'Pages', 'url' => ['/content/pages/index'], 'visibility' => 'admin'],

        '<div class="dropdown-header">Templates</div>',
        ['label' => 'Email Templates', 'url' => ['/content/email-template/index'], 'visibility' => 'user',],
        ['label' => 'VPN Templates', 'url' => ['/content/vpn-template/index'], 'visibility' => 'user',],
      ]
    ],
    [
      'label' => '<i class="bi bi-credit-card"></i> Sales',
      'url' => ['/sales/default/index'],
      'icon' => 'fas fa-money-check-alt',
      'visibility' => 'admin',
      'items' => [
        ['label' => 'Sales Dashboard', 'url' => ['/sales/default/index'], 'visibility' => 'admin'],
        ['label' => 'Customers', 'url' => ['/sales/player-customer/index'], 'visibility' => 'admin',],
        ['label' => 'Subscriptions', 'url' => ['/sales/player-subscription/index'], 'visibility' => 'admin',],
        ['label' => 'Player Products', 'url' => ['/sales/player-product/index'], 'visibility' => 'admin',],
        ['label' => 'Products', 'url' => ['/sales/product/index'], 'visibility' => 'admin',],
        ['label' => 'Prices', 'url' => ['/sales/price/index'], 'visibility' => 'admin',],
        ['label' => 'Product Networks', 'url' => ['/sales/product-network/index'], 'visibility' => 'admin',],
        ['label' => 'Payment History', 'url' => ['/sales/player-payment-history/index'], 'visibility' => 'admin',],
        ['label' => 'Webhook', 'url' => ['/sales/stripe-webhook/index'], 'visibility' => 'admin',],
      ]
    ],
    [
      'label' => '<i class="fas fa-tachometer-alt"></i> Speed',
      'url' => ['/speedprogramming/default/index'],
      'icon' => 'fas fa-money-check-alt',
      'visibility' => 'admin',
      'enabled' => '0',
      'items' => [
        ['label' => 'Problems', 'url' => ['/speedprogramming/speed-problem/index'], 'visibility' => 'admin',],
        ['label' => 'Solutions', 'url' => ['/speedprogramming/default/index'], 'visibility' => 'admin',],
      ]
    ],
    [
      'label' => '<i class="bi bi-bar-chart-fill"></i> Activity',
      'url' => ['/activity/default/index'],
      'visibility' => 'user',
      'items' => [
        ['label' => 'Notifications', 'url' => ['/activity/notification/index'], 'visibility' => 'user',],
        ['label' => 'Stream', 'url' => ['/activity/stream/index'], 'visibility' => 'admin',],
        ['label' => 'Team Scores', 'url' => ['/activity/team-score/index'], 'visibility' => 'admin',],
        ['label' => 'Player Scores', 'url' => ['/activity/player-score/index'], 'visibility' => 'admin',],
        ['label' => 'Player vs Target Progress', 'url' => ['/activity/player-vs-target/index'], 'visibility' => 'user',],
        ['label' => 'Target Player State', 'url' => ['/activity/target-player-state/index'], 'visibility' => 'user',],
        ['label' => 'Player vs Challenge Progress', 'url' => ['/activity/player-vs-challenge/index'], 'visibility' => 'user',],
        ['label' => 'Headshots', 'url' => ['/activity/headshot/index'], 'visibility' => 'admin',],
        ['label' => 'Challenge Solvers', 'url' => ['/activity/challenge-solver/index'], 'visibility' => 'admin',],
        ['label' => 'Writeups', 'url' => ['/activity/writeup/index'], 'visibility' => 'user',],
        ['label' => 'Writeup Ratings', 'url' => ['/activity/writeup-rating/index'], 'visibility' => 'user',],
        ['label' => 'Player Activated Writeups', 'url' => ['/activity/player-target-help/index'], 'visibility' => 'user',],
        ['label' => 'WS Tokens', 'url' => ['/activity/ws-token/index'], 'visibility' => 'admin',],
        ['label' => 'Reports', 'url' => ['/activity/report/index'], 'visibility' => 'user',],
        ['label' => 'Inquiries', 'url' => ['/activity/inquiry/index'], 'visibility' => 'admin',],
        ['label' => 'Sessions', 'url' => ['/activity/session/index'], 'visibility' => 'admin',],
        '<div class="dropdown-header">Pending</div>',
        ['label' => 'Spin Queue', 'url' => ['/activity/spin-queue/index'], 'visibility' => 'user',],
        ['label' => 'Player Disconnect Queue', 'url' => ['/activity/player-disconnect-queue/index'], 'visibility' => 'user',],
        '<div class="dropdown-header">VPN</div>',
        ['label' => 'Player VPN History', 'url' => ['/activity/player-vpn-history/index'], 'visibility' => 'user',],
        ['label' => 'Spin History', 'url' => ['/activity/spin-history/index'], 'visibility' => 'user',],
        ['label' => 'Player Disconnect History', 'url' => ['/activity/player-disconnect-queue-history/index'], 'visibility' => 'user',],
        '<div class="dropdown-header">Statistical</div>',
        ['label' => 'Player Monthly Scores', 'url' => ['/activity/player-score-monthly/index'], 'visibility' => 'user',],
        ['label' => 'Player Counters NF', 'url' => ['/activity/player-counter-nf/index'], 'visibility' => 'user',],
        '<div class="dropdown-header">Player Related</div>',
        ['label' => 'Player Hints', 'url' => ['/activity/player-hint/index'], 'visibility' => 'admin',],
        ['label' => 'Player Question Answers', 'url' => ['/activity/player-question/index'], 'visibility' => 'admin',],
        ['label' => 'Player Badges', 'url' => ['/activity/player-badge/index'], 'visibility' => 'admin',],
        ['label' => 'Player Treasures', 'url' => ['/activity/player-treasure/index'], 'visibility' => 'admin',],
        ['label' => 'Player Findings', 'url' => ['/activity/player-finding/index'], 'visibility' => 'admin',],
      ],
    ],

    [
      'label' => '<i class="bi bi-buildings-fill"></i> SmartCity',
      'url' => ['/smartcity/default/index'],
      'visibility' => 'user',
      'enabled' => 0,
      'items' => [
        ['label' => 'Infrastructure', 'url' => ['/smartcity/infrastructure/index'], 'visibility' => 'user',],
        ['label' => 'Infrastructure Targets', 'url' => ['/smartcity/infrastructure-target/index'], 'visibility' => 'user',],
        ['label' => 'Treasure Actions', 'url' => ['/smartcity/treasure-action/index'], 'visibility' => 'user',],
      ],
    ],
    [
      'label' => '<i class="fa fa-gavel"></i> Moderation',
      'url' => ['/moderation/default/index'],
      'visibility' => 'admin',
      'items' => [
        ['label' => 'Abusers', 'url' => ['/moderation/abuser/index'], 'visibility' => 'user,admin',],
        ['label' => 'Zero pts with activated help', 'url' => ['/moderation/default/index'], 'visibility' => 'user,admin',],
        ['label' => 'Stream with Lag', 'url' => ['/moderation/default/stream-lag'], 'visibility' => 'user,admin',],
        ['label' => 'Duplicate Signup IPs', 'url' => ['/moderation/default/duplicate-signup-ips'], 'visibility' => 'user,admin',],
        ['label' => 'Check Spammy Domains', 'url' => ['/moderation/default/check-spammy'], 'linkOptions' => ['data' => ['confirm' => 'This operation takes time to complete when a lot of players are in your system. Are you sure?']], 'visibility' => 'user,admin',],
      ]
    ],
    [
      'label' => '<i class="bi bi-people-fill"></i> Frontend',
      'url' => ['/frontend/default/index'],
      'visibility' => 'user',
      'items' => [
        ['label' => 'Players', 'url' => ['/frontend/player/index'], 'visibility' => 'user',],
        ['label' => 'Profiles', 'url' => ['/frontend/profile/index'], 'visibility' => 'user',],
        ['label' => 'Player Metadata', 'url' => ['/frontend/player-metadata/index'], 'visibility' => 'user',],
        ['label' => 'Player Tokens', 'url' => ['/frontend/player-token/index'], 'visibility' => 'user',],
        ['label' => 'Player Tokens History', 'url' => ['/frontend/player-token-history/index'], 'visibility' => 'user',],
        ['label' => 'Player Last', 'url' => ['/frontend/player-last/index'], 'visibility' => 'user',],
        ['label' => 'Player SSL', 'url' => ['/frontend/player-ssl/index'], 'visibility' => 'user',],
        ['label' => 'Player Spins', 'url' => ['/frontend/player-spin/index'], 'visibility' => 'user',],
        ['label' => 'Player Relations', 'url' => ['/frontend/player-relation/index'], 'visibility' => 'user',],
        '<div class="dropdown-divider"></div>',
        ['label' => 'Teams', 'url' => ['/frontend/team/index'], 'visibility' => 'user',],
        ['label' => 'Team Players', 'url' => ['/frontend/teamplayer/index'], 'visibility' => 'user',],
        ['label' => 'Team Invites', 'url' => ['/frontend/team-invite/index'], 'visibility' => 'user',],
        ['label' => 'Teams Audit', 'url' => ['/frontend/team-audit/index'], 'visibility' => 'user',],
        ['label' => 'Banned Players', 'url' => ['/frontend/banned-player/index'], 'visibility' => 'user',],
        '<div class="dropdown-divider"></div>',
        ['label' => 'Certificate Revocation List', 'url' => ['/frontend/crl/index'], 'visibility' => 'user',],
      ],
    ],
    [
      'label' => '<i class="bi bi-hdd-network-fill"></i> Infrastructure',
      'url' => ['/infrastructure/default/index'],
      'visibility' => 'admin',
      'items' => [
        ['label' => 'Networks', 'url' => ['/infrastructure/network/index'], 'visibility' => 'admin',],
        ['label' => 'Targets', 'url' => ['/infrastructure/target/index'], 'visibility' => 'admin',],
        ['label' => 'Ondemand', 'url' => ['/infrastructure/target-ondemand/index'], 'visibility' => 'admin',],
        ['label' => 'Target metadata', 'url' => ['/infrastructure/target-metadata/index'], 'visibility' => 'admin',],
        ['label' => 'Target State', 'url' => ['/infrastructure/target-state/index'], 'visibility' => 'admin',],
        ['label' => 'Target Instances', 'url' => ['/infrastructure/target-instance/index'], 'visibility' => 'admin',],
        ['label' => 'Network Targets', 'url' => ['/infrastructure/network-target/index'], 'visibility' => 'admin',],
        ['label' => 'Network Target Schedule', 'url' => ['/infrastructure/network-target-schedule/index'], 'visibility' => 'admin',],
        ['label' => 'Network Players', 'url' => ['/infrastructure/network-player/index'], 'visibility' => 'admin',],
        ['label' => 'Variables', 'url' => ['/infrastructure/target-variable/index'], 'visibility' => 'admin',],
        ['label' => 'Volumes', 'url' => ['/infrastructure/target-volume/index'], 'visibility' => 'admin',],
        ['label' => 'Servers', 'url' => ['/infrastructure/server/index'], 'visibility' => 'admin',],
        ['label' => 'Private Networks', 'url' => ['/infrastructure/private-network/index'], 'visibility' => 'admin',],
        ['label' => 'Private Network Targets', 'url' => ['/infrastructure/private-network-target/index'], 'visibility' => 'admin',],
        ['label' => 'Target Instance Audit', 'url' => ['/infrastructure/target-instance-audit/index'], 'visibility' => 'admin',],
      ],
    ],
    [
      'label' => '<i class="bi bi-flag-fill"></i> Gameplay',
      'url' => ['/gameplay'],
      'visibility' => 'admin',
      'items' => [
        ['label' => 'Findings', 'url' => ['/gameplay/finding/index'], 'visibility' => 'admin',],
        ['label' => 'Treasures', 'url' => ['/gameplay/treasure/index'], 'visibility' => 'admin',],
        ['label' => 'Challenges', 'url' => ['/gameplay/challenge/index'], 'visibility' => 'admin',],
        ['label' => 'Questions', 'url' => ['/gameplay/question/index'], 'visibility' => 'admin',],
        ['label' => 'Hints', 'url' => ['/gameplay/hint/index'], 'visibility' => 'admin',],
        ['label' => 'Achievements', 'url' => ['/gameplay/achievement/index'], 'visibility' => 'admin',],
        ['label' => 'Badges', 'url' => ['/gameplay/badge/index'], 'visibility' => 'admin',],
        ['label' => 'Tutorials', 'url' => ['/gameplay/tutorial/index'], 'visibility' => 'admin', 'enabled' => 0],
        ['label' => 'Tutorial Target', 'url' => ['/gameplay/tutorial-target/index'], 'visibility' => 'admin', 'enabled' => 0],
        ['label' => 'Tutorial Tasks', 'url' => ['/gameplay/tutorial-task/index'], 'visibility' => 'admin', 'enabled' => 0],
        ['label' => 'Tutorial Task Dependencies', 'url' => ['/gameplay/tutorial-task-dependency/index'], 'visibility' => 'admin', 'enabled' => 0],
        ['label' => 'Credentials', 'url' => ['/gameplay/credential/index'], 'visibility' => 'admin',],
      ],
    ],
    [
      'label' => '<i class="bi bi-house-gear-fill"></i> Settings',
      'url' => ['/settings'],
      'visibility' => 'user',
      'items' => [
        '<div class="dropdown-header">Player settings</div>',
        ['label' => 'Avatars', 'url' => ['/settings/avatar/index'], 'visibility' => 'user',],
        ['label' => 'Experience', 'url' => ['/settings/experience/index'], 'visibility' => 'user',],
        ['label' => 'Countries', 'url' => ['/settings/country/index'], 'visibility' => 'admin',],
        ['label' => 'Languages', 'url' => ['/settings/language/index'], 'visibility' => 'admin',],
        '<div class="dropdown-header">System settings</div>',
        ['label' => 'URL Routes', 'url' => ['/settings/url-route/index'], 'visibility' => 'admin',],
        ['label' => 'Disabled Routes', 'url' => ['/settings/disabled-route/index'], 'visibility' => 'admin',],
        ['label' => 'Player Disabled Routes', 'url' => ['/settings/player-disabledroute/index'], 'visibility' => 'admin',],
        ['label' => 'Banned MX Servers', 'url' => ['/settings/banned-mx-server/index'], 'visibility' => 'admin',],
        ['label' => 'OpenVPN', 'url' => ['/settings/openvpn/index'], 'visibility' => 'admin',],
        ['label' => 'MUI Menu', 'url' => ['/menu/index'], 'visibility' => 'admin',],
        '<div class="dropdown-header">Configuration</div>',
        ['label' => 'Sysconfigs', 'url' => ['/settings/sysconfig/index'], 'visibility' => 'admin',],
        ['label' => 'Configure', 'url' => ['/settings/sysconfig/configure'], 'visibility' => 'admin',],
        '<div class="dropdown-header">Backend</div>',
        ['label' => 'Users', 'url' => ['/settings/user/index'], 'visibility' => 'admin',],
      ],
    ],
    [
      'label' => '<i class="bi bi-tools"></i> Administer',
      'url' => ['/administer'],
      'visibility' => 'admin',
      'items' => [
        ['label' => 'Main', 'url' => ['/administer/default/index'], 'visibility' => 'admin',],
        ['label' => 'Events', 'url' => ['/administer/events/index'], 'visibility' => 'admin',],
      ],
    ],

  ];
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $root = 0;
    foreach ($this->items as $menu) {
      $this->insert('mui_menu', ['label' => $menu['label'], 'url' => $menu['url'][0], 'visibility' => $menu['visibility'], 'sort_order' => $root++, 'enabled' => intval(@$menu['enabled'] ?? 1)]);
      $id = Yii::$app->db->getLastInsertID();
      $child = 0;
      foreach ($menu['items'] as $item) {
        if (is_array($item))
          $this->insert('mui_menu', ['label' => $item['label'], 'url' => $item['url'][0], 'visibility' => $item['visibility'], 'parent_id' => $id, 'sort_order' => $child++, 'enabled' => intval(@$item['enabled'] ?? (@$menu['enabled'] ?? 1))]);
        else
          $this->insert('mui_menu', ['label' => $item, 'visibility' => 'admin', 'parent_id' => $id, 'sort_order' => $child++]);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->truncateTable('mui_menu');
  }
}
