<?php
namespace app\components;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * @property bool $eventInactive
 * @property bool $eventBetweenStartEnd
 * @property bool $teamsRequired
 */
class BaseController extends \yii\web\Controller
{
  /**
   * {@inheritdoc}
   */
    public function behaviors()
    {
        return [
          'access' => [
              'class' => \yii\filters\AccessControl::class,
              'rules' => [
                  'adminActions'=>[
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                          return Yii::$app->user->identity->getIsAdmin();
                        },
                  ],
                  'authActions'=>[
                      'allow' => true,
                      'actions'=>['index','view'],
                      'roles' => ['@'],
                  ],
                  'denyAll'=>[
                      'allow' => false,
                  ],
              ],
          ],
          'verbs' => [
            'class' => VerbFilter::class,
            'actions' => [
              'delete' => ['POST'],
              'truncate' => ['POST'],
            ],
          ],
        ];
    }

    public function init()
    {
        parent::init();
    }

}
