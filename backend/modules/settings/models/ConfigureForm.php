<?php
namespace app\modules\settings\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * Login form
 */
class ConfigureForm extends Model
{
    public $teams;
    public $members_per_team;
    public $team_manage_members;
    public $require_activation;
    public $disable_registration;
    public $approved_avatar;
    public $player_profile;
    public $profile_visibility;
    public $event_name;
    public $event_active;
    public $event_start;
    public $event_end;
    public $registrations_start;
    public $registrations_end;
    public $footer_logos;
    public $challenge_home;
    public $offense_registered_tag;
    public $defense_registered_tag;
    public $offense_domain;
    public $defense_domain;
    public $moderator_domain;
    public $vpngw;
    public $offense_scenario;
    public $defense_scenario;
    public $frontpage_scenario;
    public $dashboard_is_home;
    public $default_homepage;
    public $mail_from;
    public $mail_fromName;
    public $mail_host;
    public $mail_port;
    public $mail_username;
    public $mail_password;
    public $online_timeout;
    public $spins_per_day;
    public $keys=[
            'teams',
            'team_manage_members',
            'members_per_team',
            'require_activation',
            'approved_avatar',
            'disable_registration',
            'player_profile',
            'profile_visibility',
            'event_name',
            'event_active',
            'event_start',
            'event_end',
            'registrations_start',
            'registrations_end',
            'footer_logos',
            'challenge_home',
            'offense_registered_tag',
            'defense_registered_tag',
            'vpngw',
            'offense_scenario',
            'defense_scenario',
            'offense_domain',
            'defense_domain',
            'moderator_domain',
            'frontpage_scenario',
            'dashboard_is_home',
            'default_homepage',
            'mail_from',
            'mail_fromName',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'online_timeout',
            'spins_per_day',
        ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['offense_registered_tag',
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
              'mail_username',
              'mail_password',
              'profile_visibility',
              'default_homepage',
              'offense_domain',
              'defense_domain',
              'moderator_domain',
            ], 'string'],
            [['offense_registered_tag',
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
              'mail_username',
              'mail_password',
              'profile_visibility',
              'default_homepage',
              'offense_domain',
              'defense_domain',
              'moderator_domain',
              'event_start',
              'event_end',
              'registrations_start',
              'registrations_end'
            ], 'trim'],
            [['teams',
              'require_activation',
              'disable_registration',
              'player_profile',
              'profile_visibility',
              'event_name',
              'event_active',
              'mail_from',
              'mail_fromName',
              'frontpage_scenario',
              'approved_avatar',
              'team_manage_members'
          ], 'required'],
          ['profile_visibility','default','value'=>'ingame'],
          [['online_timeout', 'spins_per_day','members_per_team'], 'integer'],
          [['online_timeout'], 'default', 'value'=>900],
          [['spins_per_day'], 'default', 'value'=> 2],
          [['event_start','event_end','registrations_start','registrations_end'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
          [['dashboard_is_home','event_active', 'teams', 'team_manage_members','require_activation', 'disable_registration', 'player_profile', 'approved_avatar'], 'boolean'],

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
          'profile_visibility' => 'Player profile visibility',
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
          'approved_avatar' => 'Approved Avatar',
          'offense_vether_network' => 'Offense vether network',
          'offense_vether_netmask' => 'Offense vether netmask',
          'defense_vether_network' => 'Defense vether network',
          'defense_vether_netmask' => 'Defense vether netmask',
          'offense_registered_tag' => 'Offense registered tag',
          'defense_registered_tag' => 'Defense registered tag',
          'vpngw' => 'VPN Gateway',
          'team_manage_members' => 'Team Manage Members',
          'registerForm_academic' => 'Registration form ask academic',
          'registerForm_fullname' => 'Register form ask fullname',
          'dashboard_is_home' => 'Dashboard page is home',
          'default_homepage' => 'Default Homepage',
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

    public function beforeSave()
    {
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
