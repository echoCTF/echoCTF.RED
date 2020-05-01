<?php

namespace app\modules\game;

/**
 * game module definition class
 * @property Badge[] $badges
 * @property Headhost[] $headshots
 *
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace='app\modules\game\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function getBadges()
    {
      return models\Badge::find();
    }
    public function getHeadshots()
    {
      return models\Headshot::find();
    }

}
