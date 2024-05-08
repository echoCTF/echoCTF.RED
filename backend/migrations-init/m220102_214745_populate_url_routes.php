<?php

use yii\db\Migration;

/**
 * Class m220102_214745_populate_url_routes
 */
class m220102_214745_populate_url_routes extends Migration
{
    public $url_rules=[
      'login' => 'site/login',
      'logout' => 'site/logout',
      'register'=>'site/register',
      'request-password-reset'=>'site/request-password-reset',
      'reset-password' => 'site/reset-password',
      'resend-verification-email'=>'site/resend-verification-email',
      'verify-email'=>'site/verify-email',
      'changelog' => 'site/changelog',
      'site/captcha'=>'site/captcha',
      'dashboard' => 'dashboard/index',
      'challenges' => 'challenge/default/index',
      'challenge/<id:\d+>' => 'challenge/default/view',
      'challenge/<id:\d+>/download' => 'challenge/default/download',
      'challenge/<id:\d+>/rate' => 'game/default/rate-solver',
      'targets' => 'target/default/index',
      'targets/search' => 'target/default/search',
      'target/<id:\d+>/rate' => 'game/default/rate-headshot',
      'target/<id:\d+>' => 'target/default/view',
      'target/<id:\d+>/badge' => 'target/default/badge',
      'target/<id:\d+>/spin'=>'target/default/spin',
      'target/<id:\d+>/spawn'=>'target/default/spawn',
      'target/<id:\d+>/shut'=>'target/default/shut',
      'target/<target_id:\d+>/writeup/read/<id:\d+>' => 'target/writeup/read',
      'target/<target_id:\d+>/writeup/rate/<id:\d+>' => 'game/default/rate-writeup',
      'target/<id:\d+>/writeups/enable' => 'target/writeup/enable',
      'target/<id:\d+>/writeup/submit' => 'target/writeup/submit',
      'target/<id:\d+>/writeup/view' => 'target/writeup/view',
      'target/<id:\d+>/writeup/update' => 'target/writeup/update',
      'claim'=>'target/default/claim',
      'profile/<id:\d+>' => 'profile/index',
      'profile/<id:\d+>/badge' => 'profile/badge',
      'profile/<id:\d+>/invite' => 'profile/invite',
      'profile/me'=>'profile/me',
      'profile/ovpn/<id>'=>'profile/ovpn',
      'profile/settings'=>'profile/settings',
      'profile/revoke'=>'profile/revoke',
      'help' => 'help/default/index',
      'faq' => 'help/faq/index',
      'rules' => 'help/rule/index',
      'instructions' => 'help/instruction/index',
      'credits' => 'help/credits/index',
      'help/faq' => 'help/faq/index',
      'help/rules' => 'help/rule/index',
      'help/instructions' => 'help/instruction/index',
      'help/experience' => 'help/experience/index',
      'terms_and_conditions'=>'legal/terms-and-conditions',
      'legal/terms-and-conditions'=>'legal/terms-and-conditions',
      'privacy_policy'=>'legal/privacy-policy',
      'legal/privacy-policy'=>'legal/privacy-policy',
      'target/<id:\d+>/vs/<profile_id:\d+>'=>'target/default/versus',
      'target/<id:\d+>/versus/<profile_id:\d+>'=>'target/default/versus',
      'tutorials' => 'tutorial/default/index',
      'tutorial/<id:\d+>' => 'tutorial/default/view',
      'leaderboards' => 'game/leaderboards/index',
      'badge/<profile_id:\d+>/headshot/<target_id:\d+>' => 'game/badge/headshot',
      'teams' => 'team/default/index',
      'team/create' => 'team/default/create',
      'team/update' => 'team/default/update',
      'team/mine' => 'team/default/mine',
      'team/<token>' => 'team/default/view',
      'team/join/<token>' => 'team/default/join',
      'team/invite/<token>' => 'team/default/invite',
      'team/renew/<token>' => 'team/default/renew',
      'team/approve/<id:\d+>' => 'team/default/approve',
      'team/reject/<id:\d+>' => 'team/default/reject',
      'networks' => 'network/default/index',
      'network/<id:\d+>' => 'network/default/view',
      'api/headshots' => 'api/headshot/index',
      'api/notification' => 'api/notification/index',
      'subscriptions' => 'subscription/default/index',
      'subscription/success'=>'subscription/default/success',
      'subscription/create-checkout-session'=>'subscription/default/create-checkout-session',
      'subscription/checkout-session'=>'subscription/default/checkout-session',
      'subscription/customer-portal'=>'subscription/default/customer-portal',
      'subscription/redirect-customer-portal'=>'subscription/default/redirect-customer-portal',
      'subscription/cancel'=>'subscription/default/cancel-subscription',
      'subscription/webhook'=>'subscription/default/webhook',
      'subscription/inquiry'=>'subscription/default/inquiry',
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $weight=10;
      foreach($this->url_rules as $key => $val)
        $this->upsert('url_route',['source'=>$key,'destination'=>$val,'weight'=>$weight+=10]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      foreach($this->url_rules as $key => $val)
        $this->delete('url_route',['source'=>$key]);
    }

}
