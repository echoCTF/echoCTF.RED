<?php
namespace app\models;

use Yii;
use app\widgets\Menu as RCEmenu;

class Menu
{
    public static function getMenu() {
        $menu=RCEmenu::widget(
            [
              'options'=>['class'=>'nav'],
              'itemOptions'=>['class'=>'nav-item'],
              'linkTemplate'=>'<a href="{url}" class="nav-link">{icon} {label}</a>',
                'items' =>
                [
                    ['label' => \Yii::t('app','Home'), 'icon'=>'home', 'url' => ['/site/index'], 'visible'=>Yii::$app->user->isGuest ],
                    ['label' => \Yii::t('app','Dashboard'), 'icon'=>'dashboard', 'url' => ['/dashboard/index'], 'visible'=>!Yii::$app->user->isGuest && Yii::$app->DisabledRoute->enabled('/dashboard/index')],
                    ['label' => \Yii::t('app','Targets'), 'icon'=>'dns', 'url' => ['/target/default/index'], 'visible'=>!Yii::$app->user->isGuest && Yii::$app->DisabledRoute->enabled('/target/default/index')],
                    ['label' => \Yii::t('app','Challenges'), 'icon'=>'extension', 'url' => ['/challenge/default/index'], 'visible'=>!Yii::$app->user->isGuest && Yii::$app->DisabledRoute->enabled('challenge/default/index'), 'active'=>\Yii::$app->controller->module->id == "challenge"],
                    ['label' => \Yii::t('app','Teams'), 'icon'=>'people', 'url' => ['/team/default/index'], 'visible'=>(!Yii::$app->user->isGuest && array_key_exists('team',Yii::$app->modules) && Yii::$app->sys->teams!==false), 'active'=>\Yii::$app->controller->module->id === "team"],
                    ['label' => \Yii::t('app','Networks'), 'icon'=>'cloud','url' => ['/network/default/index'],'visible'=>!Yii::$app->user->isGuest && Yii::$app->DisabledRoute->enabled('network/default/index'),'active'=>\Yii::$app->controller->module->id=="network"],
                    ['label' => \Yii::t('app','Subscriptions'), 'icon'=>'<img src="/images/vip.svg" style="max-width: 28px">','url' => ['/subscription/default/index'],'visible'=>!Yii::$app->user->isGuest && Yii::$app->DisabledRoute->enabled('subscription/default/index') && \Yii::$app->sys->subscriptions_menu_show===true,'active'=>\Yii::$app->controller->module->id=="subscription"],
                    ['label' => \Yii::t('app','Leaderboards'), 'icon'=>'format_list_numbered', 'url' => ['/game/leaderboards/index'], 'visible'=>!Yii::$app->user->isGuest && Yii::$app->DisabledRoute->enabled('/game/leaderboards/index')],
                    ['label' => \Yii::t('app','Tutorials'), 'icon'=>'developer_board','url' => ['/tutorial/default/index'],'visible'=>!Yii::$app->user->isGuest && Yii::$app->DisabledRoute->enabled('tutorial/default/index'),'active'=>\Yii::$app->controller->module->id=="tutorial"],
                    ['label' => \Yii::t('app','Help'), 'icon'=>'help','url' => ['/help/default/index'],'visible'=>Yii::$app->DisabledRoute->enabled('help/default/index'),'active'=>\Yii::$app->controller->module->id=="help"],
//                    ['label' => \Yii::t('app','Rules'),'icon'=>'list_alt', 'url' => ['/help/rule/index'], 'visible'=>Yii::$app->DisabledRoute->enabled('help/rule/index')],
//                    ['label' => \Yii::t('app','Instructions'), 'icon'=>'info', 'url' => ['/help/instruction/index'], 'visible'=>Yii::$app->DisabledRoute->enabled('help/instruction/index')],
//                    ['label' => \Yii::t('app','FAQ'), 'icon'=>'help', 'url' => ['/help/faq/index'], 'visible'=>Yii::$app->DisabledRoute->enabled('help/faq/index')],
                    ['label' => \Yii::t('app','Changelog'), 'icon'=>'assignment','url' => ['/site/changelog'], 'visible'=>Yii::$app->DisabledRoute->enabled('site/changelog')],
                ]
            ]
        );
        return $menu;
    }

}
