<?php
namespace app\components;

use Yii;
use yii\filters\AccessControl;
/**
 * @property bool $eventInactive
 * @property bool $eventBetweenStartEnd
 * @property bool $teamsRequired
 */
class BaseController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    'eventActive'=>[
                      'allow' => false,
                      'matchCallback' => function () {
                        return $this->eventInactive;
                      },
                      'denyCallback' => function () {
                        throw new \yii\web\HttpException(403,'The event is not active.');
                      }
                    ],
                    'eventStartEnd'=>[
                       'allow' => false,
                       'matchCallback' => function () {
                         return $this->eventBetweenStartEnd;
                       },
                       'denyCallback' => function() {
                         Yii::$app->session->setFlash('info', 'This area is disabled until the competition starts');
                         return  \Yii::$app->getResponse()->redirect(['/profile/me']);
                       }
                   ],
                   'teamsAccess'=>[
                      'allow' => false,
                      'matchCallback' => function () {
                        return $this->teamsRequired;
                      },
                      'denyCallback' => function() {
                        return  \Yii::$app->getResponse()->redirect(['/team/default/index']);
                      }
                   ],
                   'disabledRoute'=>[
                       'allow' => false,
                       'matchCallback' => function ($rule, $action) {
                         return Yii::$app->DisabledRoute->disabled($action);
                       },
                       'denyCallback' => function() {
                         throw new \yii\web\HttpException(404,'This area is disabled.');
                       },
                   ],
                ],
            ],
        ];
    }

    public function init()
    {
        parent::init();
    }

    protected function getEventInactive()
    {
      return \Yii::$app->sys->event_active===false;
    }

    protected function getEventBetweenStartEnd()
    {
      return \Yii::$app->sys->event_start!==false && (time()<\Yii::$app->sys->event_start || time()>\Yii::$app->sys->event_end);
    }

    protected function getTeamsRequired()
    {
      if(\Yii::$app->sys->team_required===false)
      {
         return false;
      }

      if(\Yii::$app->user->identity->teamPlayer===null)
      {
        \Yii::$app->session->setFlash('warning', 'You need to join a team before being able to access this area.');
        return true;
      }
      if(\Yii::$app->user->identity->teamPlayer->approved!==1)
      {
        \Yii::$app->session->setFlash('warning', 'You need to have your team membership approved before being able to access this area.');
        return true;
      }
      return false;
    }
}
