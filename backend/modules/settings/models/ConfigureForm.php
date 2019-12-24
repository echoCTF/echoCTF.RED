<?php
namespace app\modules\settings\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class ConfigureForm extends Model
{
    public $trust_user_ip;
    public $mac_auth;
    public $teams;
    public $require_activation;
    public $disable_registration;
    public $strict_activation;
    public $player_profile;
    public $join_team_with_token;
    public $event_name;
    public $footer_logos;
    public $award_points;
    public $offense_home;
    public $defense_home;
    public $moderator_home;
    public $offense_domain;
    public $defense_domain;
    public $moderator_domain;
    public $challenge_home;
    public $offense_vether_network;
    public $offense_vether_netmask;
    public $defense_vether_network;
    public $defense_vether_netmask;
    public $offense_registered_tag;
    public $offense_bridge_if;
    public $offense_eth_if;
    public $defense_registered_tag;
    public $defense_bridge_if;
    public $defense_eth_if;
    public $vpngw;
    public $team_manage_members;
    public $registerForm_academic;
    public $registerForm_fullname;
    public $offense_scenario;
    public $defense_scenario;
    public $frontpage_scenario;
    public $dashboard_is_home;
    public $mail_from;
    public $mail_fromName;
    public $mail_host;
    public $mail_port;
    public $online_timeout;
    public $spins_per_day;
    public $keys=[ 'trust_user_ip',
            'mac_auth',
            'teams',
            'require_activation',
            'disable_registration',
            'strict_activation',
            'player_profile',
            'join_team_with_token',
            'event_name',
            'footer_logos',
            'award_points',
            'offense_home',
            'defense_home',
            'moderator_home',
            'offense_domain',
            'defense_domain',
            'moderator_domain',
            'challenge_home',
            'offense_vether_network',
            'offense_vether_netmask',
            'defense_vether_network',
            'defense_vether_netmask',
            'offense_registered_tag',
            'offense_bridge_if',
            'offense_eth_if',
            'defense_registered_tag',
            'defense_bridge_if',
            'defense_eth_if',
            'vpngw',
            'team_manage_members',
            'registerForm_academic',
            'registerForm_fullname',
            'offense_scenario',
            'defense_scenario',
            'frontpage_scenario',
            'dashboard_is_home',
            'mail_from',
            'mail_fromName',
            'mail_host',
            'mail_port',
            'online_timeout',
            'spins_per_day',
        ];




    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['moderator_home',
              'challenge_home',
              'defense_home',
              'offense_home',
              'offense_bridge_if',
              'offense_eth_if',
              'defense_bridge_if',
              'defense_eth_if',
              'moderator_domain',
              'offense_domain',
              'defense_domain',
              'offense_vether_network',
              'offense_vether_netmask',
              'defense_vether_network',
              'defense_vether_netmask',
              'offense_registered_tag',
              'defense_registered_tag',
              'footer_logos',
              'vpngw',
              'offense_scenario',
              'defense_scenario',
              'frontpage_scenario',
              'mail_from',
              'mail_fromName',
              'mail_host',
              'mail_port',
            ], 'string'],
            [[ 'trust_user_ip',
                    'mac_auth',
                    'teams',
                    'require_activation',
                    'disable_registration',
                    'strict_activation',
                    'player_profile',
                    'join_team_with_token',
                    'event_name',
                    'award_points',
                    'team_manage_members',
                    'mail_from',
                    'mail_fromName',
                    'mail_host',
                    'mail_port',
                    'frontpage_scenario',
                ], 'required'],
            [['online_timeout','spins_per_day'],'integer'],
            [['online_timeout'],'default','value'=>900 ],
            [['spins_per_day'],'default','value'=> 2 ],
            [['dashboard_is_home','registerForm_academic','registerForm_fullname','team_manage_members','trust_user_ip', 'mac_auth', 'teams', 'require_activation', 'disable_registration', 'strict_activation','player_profile', 'join_team_with_token'], 'boolean'],

        ];
    }
    public function attributeLabels()
    {
        return [
          'trust_user_ip' => 'Trust user IP',
          'mac_auth' => 'MAC Authentication',
          'teams' => 'Teams',
          'require_activation' => 'Require activation',
          'disable_registration' => 'Disable registration',
          'strict_activation' => 'Strict player activations',
          'player_profile' => 'Player profile',
          'join_team_with_token' => 'Join teams with token',
          'event_name' => 'Event name',
          'footer_logos' => 'Footer logos',
          'award_points' => 'Award points',
          'offense_home' => 'Offense home',
          'defense_home' => 'Defense home',
          'moderator_home' => 'Moderator home',
          'offense_domain' => 'Offense domain',
          'defense_domain' => 'Defense domain',
          'moderator_domain' => 'Moderator domain',
          'challenge_home' => 'Challenge home',
          'offense_vether_network' => 'Offense vether network',
          'offense_vether_netmask' => 'Offense vether netmask',
          'defense_vether_network' => 'Defense vether network',
          'defense_vether_netmask' => 'Defense vether netmask',
          'offense_registered_tag' => 'Offense registered tag',
          'offense_bridge_if' => 'Offense bridge interface',
          'offense_eth_if' => 'Offense ethernet interface',
          'defense_registered_tag' => 'Defense registered tag',
          'defense_bridge_if' => 'Defense bridge interface',
          'defense_eth_if' => 'Defense ethernet interface',
          'vpngw' => 'VPN Gateway',
          'team_manage_members' => 'Team Manage Members',
          'registerForm_academic' => 'Registration form ask academic',
          'registerForm_fullname' => 'Register form ask fullname',
          'dashboard_is_home' => 'Dashboard page is home',
          'mail_from'=>'Mail From',
          'mail_fromName'=>'Mail From Name',
          'mail_host'=>'Mail Host',
          'mail_port'=>'Mail Port',
          'online_timeout' => 'Timeout for user online key to expire',
          'spins_per_day'=>'Spins allowed per day',
        ];
    }

    public function init()
    {
      parent::init();
      foreach($this->keys as $id)
      {
        $sysconfig=Sysconfig::findOne($id);
        if($sysconfig)
          $this->{$id}=$sysconfig->val;
      }
    }
    public function save()
    {
      foreach($this->keys as $id)
      {
        $sysconfig=Sysconfig::findOne($id);
        if(!$sysconfig)
        {
          $sysconfig=new Sysconfig;
          $sysconfig->id=$id;
        }
        $sysconfig->val=$this->{$id};
        $sysconfig->save();
      }
        return true;
    }

}
