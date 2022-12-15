<?php
namespace app\components;


use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class Sysconfig extends Component
{
  public $keyCache;
  private $prefetchKeys=[
      'sysconfig:disabled_routes',
      'sysconfig:player_disabled_routes',
      'sysconfig:teams',
      'sysconfig:admin_ids',
      'sysconfig:team_required',
      'sysconfig:members_per_team',
      'sysconfig:team_manage_members',
      'sysconfig:require_activation',
      'sysconfig:disable_registration',
      'sysconfig:approved_avatar',
      'sysconfig:player_profile',
      'sysconfig:profile_visibility',
      'sysconfig:event_name',
      'sysconfig:event_active',
      'sysconfig:event_start',
      'sysconfig:event_end',
      'sysconfig:twitter_account',
      'sysconfig:twitter_hashtags',
      'sysconfig:registrations_start',
      'sysconfig:registrations_end',
      'sysconfig:challenge_home',
      'sysconfig:offense_registered_tag',
      'sysconfig:defense_registered_tag',
      'sysconfig:offense_domain',
      'sysconfig:defense_domain',
      'sysconfig:moderator_domain',
      'sysconfig:vpngw',
      'sysconfig:dashboard_is_home',
      'sysconfig:default_homepage',
      'sysconfig:mail_from',
      'sysconfig:mail_fromName',
      'sysconfig:mail_host',
      'sysconfig:mail_port',
      'sysconfig:mail_username',
      'sysconfig:mail_password',
      'sysconfig:mail_useFileTransport',
      'sysconfig:online_timeout',
      'sysconfig:spins_per_day',
      'sysconfig:leaderboard_visible_before_event_start',
      'sysconfig:leaderboard_visible_after_event_end',
      'sysconfig:leaderboard_show_zero',
      'sysconfig:time_zone',
      'sysconfig:dn_countryName',
      'sysconfig:dn_stateOrProvinceName',
      'sysconfig:dn_localityName',
      'sysconfig:dn_organizationName',
      'sysconfig:dn_organizationalUnitName',
    ];
  public function init()
  {
    if(!(\Yii::$app->cache instanceof \yii\caching\MemCache))
      throw new \LogicException('Memcache not initialized.');

    if(file_exists(Yii::getAlias('@app/config/sysconfig.php')))
    {
      $this->keyCache=require Yii::getAlias('@app/config/sysconfig.php');
    }
    $raw=Yii::$app->cache->memcache->get('sysconfig_json');
    $decoded=json_decode($raw);
    if(json_last_error()===JSON_ERROR_NONE)
    {
      foreach($decoded as $obj)
      {
        $this->keyCache['sysconfig:'.$obj->id]=$obj->val;
      }
    }
    //if(\Yii::$app->cache->memcache instanceof \MemCache)
    //  $this->keyCache=Yii::$app->cache->memcache->get($this->prefetchKeys);
  }
  public function __get($attribute)
  {
    if(is_array($this->keyCache) && array_key_exists('sysconfig:'.$attribute,$this->keyCache))
    {
      $val=$this->keyCache['sysconfig:'.$attribute];
    }
    else
    {
      $val=Yii::$app->cache->memcache->get('sysconfig:'.$attribute);
      $this->keyCache['sysconfig:'.$attribute]=$val;
    }
    // key not found
    if($val === false || $val === "0")
    {
      return false;
    }
    elseif($val === "1")
    {
      return true;
    }
    return $val;
  }

  public function __set($attribute,$value)
  {
    if(!(\Yii::$app->cache instanceof \yii\caching\MemCache))
      throw new \LogicException('Memcache not initialized.');

    $val=Yii::$app->cache->memcache->set($attribute,$value);
    $this->keyCache[$attribute]=$value;
    return $val;
  }

}
