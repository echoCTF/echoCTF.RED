<?php

use yii\db\Migration;

/**
 * Class m000000_000001_system_settings
 */
class m000000_000001_system_settings extends Migration
{
  public $sysconfigs = [
    /**
     * General event details
     */
    ['id' => "event_active", 'val' => "1"],
    ['id' => "event_name", 'val' => "echoCTF"],
    ['id' => "event_description", 'val' => "Our Awesome echoCTF"],
    ['id' => 'time_zone', 'val' => 'Europe/Athens'],
    ['id' => 'event_start', 'val' => '1758261600'], // "2025-09-19 08:00:00"
    ['id' => 'event_end', 'val' => '1758283200'], // "2025-09-19 14:00:00"
    ['id' => 'registrations_start', 'val' => '1751328000'], // "2022-07-01 00:00:00"
    ['id' => 'registrations_end', 'val' => '1755734400'], // "2022-08-11 18:00:00"
    ['id' => "moderator_domain", 'val' => "mui.example.com"],
    ['id' => "offense_domain", 'val' => "ctf.example.com"],
    ['id' => "vpngw", 'val' => "vpn.example.com"],
    /**
     * Frontend settings
     */
    ['id' => 'hide_timezone', 'val' => "1"],
    ['id' => "twitter_account", 'val' => "example"],
    ['id' => "twitter_hashtags", 'val' => "MyHashtag,echoCTF"],
    ['id' => "leaderboard_show_zero", 'val' => "0"],
    ['id' => "leaderboard_visible_after_event_end", 'val' => "1"],
    ['id' => "leaderboard_visible_before_event_start", 'val' => "0"],
    ['id' => 'frontpage_scenario', 'val' => 'Welcome to our lovely event... Edit from backend Content => Frontpage Scenario'],
    ['id' => "event_end_notification_title", 'val' => "🎉 Our awesome echoCTF finished 🎉"],
    ['id' => "event_end_notification_body", 'val' => "The awesome echoCTF is over 🎉🎉🎉 Congratulations to you and your team 👏👏👏 Thank you for participating!!!"],
    ['id' => 'menu_items', 'val' => '[{"name":"<i class=\"fab fa-discord text-discord\"><\/i><p class=\"text-discord\">Join our Discord!<\/p>","link":"https:\/\/discord.gg\/"}]'],
    /**
     * Mail related settings
     */
    ['id' => "mail_useFileTransport", 'val' => "1"], // Set to 0 to send emails
    ['id' => "mail_from", 'val' => "dontreply@example.com"],
    ['id' => "mail_fromName", 'val' => "echoCTF Registration Bot"],
    ['id' => 'dsn', 'val' => 'gmail+smtp://dontreply@example.com:myappkey@default?local_domain=ctf.example.com'],
    /**
     * SSL and VPN settings
     */
    ['id' => "dn_commonName", 'val' => "ROOT CA"],
    ['id' => "dn_countryName", 'val' => "GR"],
    ['id' => "dn_localityName", 'val' => "Athens"],
    ['id' => "dn_organizationalUnitName", 'val' => "echoCTF"],
    ['id' => "dn_organizationName", 'val' => "echoCTF"],
    ['id' => "dn_stateOrProvinceName", 'val' => "Greece"],
    /**
     * Team settings
     */
    ['id' => "teams", 'val' => "1"],
    ['id' => "members_per_team", 'val' => "5"],
    ['id' => "team_manage_members", 'val' => "1"],
    ['id' => "team_required", 'val' => "1"],
    ['id' => 'team_visible_instances', 'val' => "1"],
    /**
     * Player settings
     */
    ['id' => "approved_avatar", 'val' => "1"],
    ['id' => "player_profile", 'val' => "1"],
    ['id' => "profile_visibility", 'val' => "public"],
    ['id' => "require_activation", 'val' => "0"],
    ['id' => 'player_require_identification', 'val' => "0"],
    ['id' => 'all_players_vip', 'val' => "1"],
    ['id' => 'player_require_approval', 'val' => "0"],
    ['id' => 'profile_discord', 'val' => "1"],
    ['id' => 'profile_echoctf', 'val' => "1"],
    ['id' => 'profile_github', 'val' => "1"],
    ['id' => 'profile_settings_fields', 'val' => 'avatar,bio,country,discord,echoctf,email,fullname,github,pending_progress,twitter,username,visibility'],
    /**
     * Configuration settings
     */
    ['id' => 'academic_grouping', 'val' => '0'],
    ['id' => "challenge_home", 'val' => "uploads/"],
    ['id' => "dashboard_is_home", 'val' => "1"],
    ['id' => "default_homepage", 'val' => "/dashboard/index"],
    ['id' => "spins_per_day", 'val' => "70"],
    ['id' => "target_days_new", 'val' => "1"],
    ['id' => "target_days_updated", 'val' => "0"],
    ['id' => 'pf_state_limits', 'val' => '(max 10000, source-track rule, max-src-nodes 5, max-src-states 1000, max-src-conn 50)'],
  ];
  public $disabled_routes = [
    //        ['route'=>'/challenge%'],
    //        ['route'=>'/network%'],
    //        ['route'=>'/site/changelog%'],
    //        ['route'=>'/tutorial%'],
    //        ['route'=>'/help/experience%'],
    //        ['route'=>'/help/credit%'],
  ];
  public $delete_url_routes = [
    //      ['id'=>8 ],// changelog
    //      ['id'=>52 ],// tutorials
    //      ['id'=>53 ],// tutorial/<id>
    //      ['id'=>70 ],// subs
    //      ['id'=>71 ],// subs
    //      ['id'=>72 ],// subs
    //      ['id'=>73 ],// subs
    //      ['id'=>74 ],// subs
    //      ['id'=>75 ],// subs
    //      ['id'=>76 ],// subs
    //      ['id'=>77 ],// subs
    //      ['id'=>78 ],// subs
  ];

  public $upsert_url_routes = [];

  public $news = [
    [
      'id' => 1,
      'title' => 'Welcome to our echoCTF',
      'category' => '<img src="/images/news/category/newspaper.svg" width="25px"/>',
      'body' => '<p class="lead">Welcome to our echoCTF to compete against other teams and secure your place on the top of the leaderboard.</p>',
    ]
  ];

  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    foreach ($this->news as $entry)
      $this->upsert('news', $entry, true);

    // delete not needed url routes
    foreach ($this->delete_url_routes as $route) {
      $this->delete('url_route', $route);
    }
    // delete urls to be added so that we dont
    // end up with duplicates
    foreach ($this->upsert_url_routes as $route) {
      $this->delete('url_route', $route);
    }

    // delete not needed url routes
    foreach ($this->sysconfigs as $entry) {
      $this->upsert('sysconfig', $entry);
    }

    // update existing url routes
    foreach ($this->upsert_url_routes as $route) {
      $this->upsert('url_route', $route, true);
    }

    // add/modify disabled routes
    foreach ($this->disabled_routes as $route) {
      $this->upsert('disabled_route', $route, true);
    }

    $this->delete("hint", ['id' => -1]);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {}
}
