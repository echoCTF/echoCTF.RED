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
                    ['label' => 'Home', 'icon'=>'home', 'url' => ['/site/index'], 'visible'=>Yii::$app->user->isGuest ],
                    ['label' => 'Dashboard', 'icon'=>'dashboard', 'url' => ['/dashboard/index'], 'visible'=>!Yii::$app->user->isGuest && Yii::$app->DisabledRoute->enabled('/dashboard/index')],
                    ['label' => 'Networks', 'icon'=>'bug_report','url' => ['/network/default/index'],'visible'=>!Yii::$app->user->isGuest && Yii::$app->DisabledRoute->enabled('network/default/index'),'active'=>\Yii::$app->controller->module->id=="network"],
                    ['label' => 'Challenges', 'icon'=>'extension', 'url' => ['/challenge/default/index'], 'visible'=>!Yii::$app->user->isGuest && Yii::$app->DisabledRoute->enabled('challenge/default/index'), 'active'=>\Yii::$app->controller->module->id == "challenge"],
                    ['label' => 'Tutorials', 'icon'=>'developer_board','url' => ['/tutorial/default/index'],'visible'=>!Yii::$app->user->isGuest && Yii::$app->DisabledRoute->enabled('tutorial/default/index'),'active'=>\Yii::$app->controller->module->id=="tutorial"],
                    ['label' => 'Rules','icon'=>'list_alt', 'url' => ['/help/rule/index'], 'visible'=>Yii::$app->DisabledRoute->enabled('help/rule/index')],
                    ['label' => 'Instructions', 'icon'=>'info', 'url' => ['/help/instruction/index'], 'visible'=>Yii::$app->DisabledRoute->enabled('help/instruction/index')],
                    ['label' => 'FAQ', 'icon'=>'help', 'url' => ['/help/faq/index'], 'visible'=>Yii::$app->DisabledRoute->enabled('help/faq/index')],
                    ['label' => 'Changelog', 'icon'=>'assignment','url' => ['/site/changelog'], 'visible'=>Yii::$app->DisabledRoute->enabled('site/changelog')],
                ]
            ]
        );
        return $menu;
    }

}
