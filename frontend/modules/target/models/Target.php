<?php

namespace app\modules\target\models;

use Yii;
use app\models\PlayerTreasure;
use app\models\PlayerFinding;
use app\modules\game\models\Headshot;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "target".
 *
 * @property int $points
 * @property int $countHeadshots
 * @property array $treasureCategories
 * @property bool $spinAllowed
 * @property bool $spinDenied
 *
 */
class Target extends TargetAR
{
    const DEFAULT_LOGO='/images/default_target.png';
    public $difficulties=[
      "beginner",
      "basic",
      "intermediate",
      "advanced",
      "expert",
      "guru",
      "insane",
    ];

    public function getTreasureCategories()
    {
      $categories=[];
      foreach($this->treasures as $t)
      {
        if(isset($categories[$t->category]))
          $categories[$t->category]++;
        else
          $categories[$t->category]=1;
      }
      return $categories;
    }

    public function getTreasureCategoriesFormatted()
    {
      $categories=[];
      foreach($this->treasureCategories as $category => $cnt)
      {
        if($cnt > 1)
          $categories[]=sprintf("%d:%s", $cnt, $category);
        else
          $categories[]=sprintf("%s", $category);
      }
      return implode(', ', $categories);

    }

    public function getSchedule()
    {
      if(intval($this->active) === 1 && $this->status === 'powerdown')
        return sprintf('Target scheduled for powerdown at %s', $this->scheduled_at);
      elseif(intval($this->active) === 0 && $this->status === 'powerup')
        return sprintf('Target scheduled for powerup at %s', $this->scheduled_at);
    }

    /*
     * Get the text representation of the official target difficulty rating
     */
    public function getDifficultyText(int $difficulty=null)
    {
      if($difficulty===null)
        return $this->difficulties[(int) $this->difficulty];
      else if($difficulty===-1)
        return 'unrated';
      return $this->difficulties[(int) $difficulty];
    }

    /*
     * Get the total points awarded by the target
     */
    public function getPoints()
    {
      $sum_points=0;
      foreach($this->treasures as $tr)
        $sum_points+=$tr->points;

      foreach($this->findings as $tr)
        $sum_points+=$tr->points;
      return $sum_points;
    }

    /*
     * Get headshots count on the target
     */
    public function getCountHeadshots()
    {
      return $this->getHeadshots()->count();
    }

    public function getFormattedExtras()
    {
      $scheduled=null;
      if(intval($this->active) === 1 && $this->status === 'powerdown')
        $scheduled=sprintf('<abbr title="Scheduled to powedown at %s"><i class="glyphicon glyphicon-hand-down"></i></abbr>', $this->scheduled_at);
      elseif(intval($this->active) === 0 && $this->status === 'powerup')
        $scheduled=sprintf('<abbr title="Scheduled to powerup %s"><i class="glyphicon glyphicon-hand-up"></i></abbr>', $this->scheduled_at);
      return sprintf("<center><abbr title='Flags'><i class='material-icons'>flag</i>%d</abbr> / <abbr title='Service'><i class='material-icons'>whatshot</i>%d</abbr> / <abbr title='Headshots'><i class='material-icons'>memory</i>%d</abbr> %s</center>", count($this->treasures), count($this->findings), $this->countHeadshots, $scheduled);
    }

    /*
     * Checks if the target is spinnable by the current player.
     */
    public function getSpinable()
    {
      return $this->spinAllowed && !$this->spinDenied;
    }
    public function getSpinDenied()
    {
      if($this->player_spin===false)
      {
        return true;
      }

      // Not active or already queued
      if($this->spinQueue !== null || $this->active === false)
      {
        return true;
      }
// dont check for progress
      if(Yii::$app->user->identity->profile->last->vpn_local_address === null && intval(self::find()->player_progress(Yii::$app->user->id)->where(['t.id'=>$this->id])->one()->player_findings)<1 && intval(self::find()->player_progress(Yii::$app->user->id)->where(['t.id'=>$this->id])->one()->player_treasures)<1)
        return true;

      if($this->status!=='online')
        return true;

      return false;
    }

    public function getSpinAllowed()
    {

      if(intval(Yii::$app->user->identity->profile->spins->counter) < intval(Yii::$app->user->identity->profile->spins->perday))
      {
        return true;// user is not allowed spins for the day.
      }
      return false;
    }
    /*
     * Get Full Logo image for target to be used by <img> and related tags
     */
    public function getFullLogo()
    {
      if(file_exists(Yii::getAlias("@webroot/images/targets/".$this->name.".png")))
      {
        return '/images/targets/'.$this->name.'.png';
      }

      return self::DEFAULT_LOGO;
    }

    /*
     * Get Logo image for target to be used by <img> and related tags
     */
    public function getLogo()
    {
      if(file_exists(Yii::getAlias("@webroot/images/targets/_".$this->name.".png")))
      {
        return '/images/targets/_'.$this->name.'.png';
      }

      return self::DEFAULT_LOGO;
    }

    /**
     * Get thumbnail image for target to be used by <img> and related tags
     */
    public function getThumbnail()
    {
      if(file_exists(Yii::getAlias("@webroot/images/targets/_".$this->name."-thumbnail.png")))
      {
        return '/images/targets/_'.$this->name.'-thumbnail.png';
      }

      return self::DEFAULT_LOGO;
    }
    /**
     * Get the displayable IP for the target
     */
    public function getDisplayIp()
    {
      if(Yii::$app->user->identity->instance !== NULL && Yii::$app->user->identity->instance->target_id===$this->id)
      {
        $msg=Yii::t('app',"The IP of your private instance.");
        if(Yii::$app->user->identity->instance->ip===null)
        {
          $msg=Yii::t('app',"Your instance is being powered up, please wait...");
        }

        return \yii\helpers\Html::tag('abbr',long2ip(Yii::$app->user->identity->instance->ip),['style'=>'padding-top: 10px; padding-bottom: 10px',"class"=>'text-danger','data-toggle'=>'tooltip','title'=>$msg]);
      }
      elseif($this->on_ondemand && $this->ondemand_state===-1)
      {
        return \yii\helpers\Html::tag('abbr','0.0.0.0',['style'=>'padding-top: 10px; padding-bottom: 10px','data-toggle'=>'tooltip','title'=>Yii::t('app',"System currently powered down. Go to the target page to power it up.")]);
      }
      return $this->ipoctet;
    }

    /**
     * Return the current IP or hostname if system not powered
     */
    public function getIpOrName():string
    {
      if(Yii::$app->user->identity->instance !== NULL && Yii::$app->user->identity->instance->target_id===$this->id)
      {
        return long2ip(Yii::$app->user->identity->instance->ip);
      }
      elseif($this->on_ondemand && $this->ondemand_state===-1)
      {
        return $this->name;
      }
      return $this->ipoctet;
    }

}
