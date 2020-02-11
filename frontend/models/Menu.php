<?php
namespace app\models;

use Yii;
use app\widgets\Menu as RCEmenu;

class Menu
{
    static function getMenu() {
        $menu = RCEmenu::widget(
            [
              'options'=>['class'=>'nav'],
              'itemOptions'=>['class'=>'nav-item'],
              'linkTemplate'=>'<a href="{url}" class="nav-link">{icon} {label}</a>',
                'items' =>
                [
                    ['label' => 'Home', 'icon'=>'home','url' => ['/site/index'],'visible'=>Yii::$app->user->isGuest],
                    ['label' => 'Dashboard', 'icon'=>'dashboard','url' => ['/dashboard/index'],'visible'=>!Yii::$app->user->isGuest],
                    //['label' => 'Networks', 'icon'=>'bug_report','url' => ['/network/default/index'],'visible'=>!Yii::$app->user->isGuest,'active'=>\Yii::$app->controller->module->id=="network"],
                    ['label' => 'Challenges', 'icon'=>'extension','url' => ['/challenge/default/index'],'visible'=>!Yii::$app->user->isGuest,'active'=>\Yii::$app->controller->module->id=="challenge"],
                    //['label' => 'Tutorials', 'icon'=>'developer_board','url' => ['/tutorial/default/index'],'visible'=>!Yii::$app->user->isGuest,'active'=>\Yii::$app->controller->module->id=="tutorial"],
                    ['label' => 'FAQ', 'icon'=>'help', 'url' => ['/help/faq/index']],
                    ['label' => 'Rules','icon'=>'list_alt', 'url' => ['/help/rule/index']],
                    ['label' => 'Instructions', 'icon'=>'info','url' => ['/help/instruction/index']],
                    ['label' => 'Changelog', 'icon'=>'assignment','url' => ['/site/changelog']],
                ]
            ]
        );
        return $menu;
    }

}
