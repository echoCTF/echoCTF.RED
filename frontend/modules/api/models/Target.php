<?php

namespace app\modules\api\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;

class Target extends \app\modules\target\models\Target
{
  public function fields()
  {
    return [
      'id',
      'name',
      'fqdn',
      'ip',
      'status',
      'purpose',
      'description',
      'rootable',
      'timer',
      'writeup_allowed',
      'player_spin',
      'instance_allowed',
      'difficulty',
      'total_findings',
      'total_treasures',
    ];
  }

  public function extraFields()
  {
    return [];
  }
  public function afterFind()
  {
    parent::afterFind();
    $this->total_findings = count($this->findings);
    $this->total_treasures = count($this->treasures);
    if (Yii::$app->user->identity->instance)
      $this->ip = long2ip(Yii::$app->user->identity->instance->ip);
    else if($this->ondemand && $this->ondemand->state==1)
      $this->ip = long2ip($this->ip);
    else if($this->ondemand && $this->ondemand->state!=1)
      $this->ip = long2ip(0);
    else
      $this->ip=long2ip($this->ip);
    }

}
