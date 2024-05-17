<?php
namespace app\widgets\targetcardactions;

use Yii;
use yii\base\Widget;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class TargetCardActions extends Widget
{
    public $model;
    public $identity;
    public $htmlOptions=['class'=>'card bg-dark solves'];
    public $target_actions;
    public $target_instance;
    public $linkOptions=[
        'data-pjax' => '0',
        'data-method' => 'POST',
      ];
    public $Network;
    public function init()
    {
        parent::init();
        $this->Network=Yii::$app->getModule('network');
    }

    public function run()
    {
        // Register AssetBundle
        //TargetCardActionsAsset::register($this->getView());
        if(!Yii::$app->user->isGuest)
        {
            if($this->target_instance===null)
                $this->target_instance=Yii::$app->user->identity->instance;
            if($this->target_actions===null)
                $this->prep_actions();
        }

        $this->renderDropdown();
        //return $this->render('_actions', ['target_actions' => $this->target_actions]);
    }

    public function prep_actions()
    {
        if($this->identity->player_id===intval(Yii::$app->user->id))
        {

            $this->prep_instance_actions();
            $this->prep_ondemand_actions();
        }
    }

    public function renderDropdown()
    {

        if($this->target_actions!==null)
        {
          echo '<div class="dropdown target-actions">'.
            '<a href="#" data-toggle="dropdown" class="dropdown-toggle text-secondary"><i class="fas fa-cogs" style="font-size: 1.3em;"></i><b class="caret"></b></a>'.\yii\bootstrap\Dropdown::widget([
                    'encodeLabels'=>false,
                    'options'=>[],
                    'items' =>$this->target_actions,
                ]).'</div>';
        }
    }
    public function prep_instance_actions()
    {
        if(intval(Yii::$app->db->createCommand('select count(*) from server')->queryScalar())==0) return;
        // If the target is pending powerup then dont show any actions
        if($this->model->status!=='online' || ($this->model->network && !$this->Network->checkTarget($this->model)))
            return;
        if(!$this->model->instance_allowed)
            return;

        if(!Yii::$app->user->identity->isVip)
        {
            $linkOptions=$this->linkOptions;
            unset($linkOptions['data-method']);
            $linkOptions['data-swType']='success';
            $linkOptions['data-showCancelButton']="false";
            $linkOptions['data-confirm']=\Yii::t('app','You have requested to spawn a new instance of this target but you currently dont have an active subscription. Subscribe to activate this feature.');
            $this->target_actions[]=[
                'label' => \Yii::t('app','<b><i class="fas fa-play"></i>&nbsp; Spawn a private instance</b>'),
                'url' => Url::to(['/subscription/default/index']),
                'options'=>['style'=>'white-space: nowrap;'],
                'linkOptions'=>$linkOptions
            ];
        }
        elseif($this->target_instance === NULL)
        {
            $this->target_actions[]=[
                    'label' => \Yii::t('app','<b><i class="fas fa-play"></i>&nbsp; Spawn a private instance</b>'),
                    'url' => Url::to(['/target/default/spawn', 'id'=>$this->model->id]),
                    'options'=>['style'=>'white-space: nowrap;'],
                    'linkOptions'=>ArrayHelper::merge($this->linkOptions,['data-confirm'=>\Yii::t('app','You are about to spawn a private instance of this target. Once booted, this instance will only be accessible by you and its IP will become visible here.')])
                ];
        }
        elseif($this->target_instance->target_id!==$this->model->id)
        {
            $this->target_actions[]=[
                'label' => \Yii::t('app','<b><i class="fas fa-play"></i>&nbsp; Spawn a private instance</b>'),
                'url' => Url::to(['/target/default/spawn', 'id'=>$this->model->id]),
                'options'=>['style'=>'white-space: nowrap;'],
                'linkOptions'=>ArrayHelper::merge($this->linkOptions,['data-confirm'=>\Yii::t('app','You are about to spawn a private instance of this target. However, you already have one instance running for '.$this->target_instance->target->name.'. Do you want to schedule the existing instance to be destroyed in order to be able to spawn a new one?')])
            ];
        }
        elseif($this->target_instance->target_id===$this->model->id && $this->target_instance->reboot<2)
        {
            $this->target_actions[]=[
                'label' => \Yii::t('app','<b><i class="fas fa-sync"></i>&nbsp; Restart your instance</b>'),
                'url' => Url::to(['/target/default/spin', 'id'=>$this->model->id]),
                'options'=>['style'=>'white-space: nowrap;'],
                'linkOptions'=>ArrayHelper::merge($this->linkOptions,['data-confirm'=>\Yii::t('app','You are about to restart your instance. You will receive a notification once the operation is complete.')])
            ];
            $this->target_actions[]=[
                    'label' => \Yii::t('app','<b><i class="fas fa-power-off"></i>&nbsp; Shut your instance</b>'),
                    'url' => Url::to(['/target/default/shut', 'id'=>$this->model->id]),
                    'options'=>['style'=>'white-space: nowrap;'],
                    'linkOptions'=>ArrayHelper::merge($this->linkOptions,['data-confirm'=>\Yii::t('app','You are about to shutdown your private instance of this target. This process takes up to a minute to complete. You will get a notification once it is completed.')])
                ];
        }
    }
    public function prep_ondemand_actions()
    {
        if($this->target_instance!==null && $this->target_instance->target_id==$this->model->id)
            return;
        if($this->model->ondemand && $this->model->ondemand->state<0 && $this->model->spinable)
        {
            $this->target_actions[]=[
                    'label' => \Yii::t('app','<b><i class="fas fa-plug"></i>&nbsp; Power up this target</b>'),
                    'url' => Url::to(['/target/default/spin', 'id'=>$this->model->id]),
                    'options'=>['style'=>'white-space: nowrap;'],
                    'linkOptions'=>ArrayHelper::merge($this->linkOptions,['data-confirm'=>\Yii::t('app','You are about to power up this target. Once booted, everyone will be able to access it.')])
                ];
        }
        elseif($this->model->player_spin===true && $this->model->spinable)
        {
            $this->target_actions[]=[
                    'label' => \Yii::t('app','<b><i class="fas fa-sync"></i>&nbsp; Restart target</b>'),
                    'url' => Url::to(['/target/default/spin', 'id'=>$this->model->id]),
                    'options'=>['style'=>'white-space: nowrap;'],
                    'linkOptions'=>$this->linkOptions,
                ];
        }
    }
}
