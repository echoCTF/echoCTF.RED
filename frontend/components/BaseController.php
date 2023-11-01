<?php

namespace app\components;

use Yii;
use app\overloads\yii\filters\AccessControl;

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
          'adminBypass' => [
            'allow' => true,
            'matchCallback' => function () {
              if (!\Yii::$app->user->isGuest && \Yii::$app->user->identity->isAdmin)
                return true;
            }
          ],

          'denyByIP' => [
            'allow' => false,
            'ips' => explode(',', \Yii::$app->sys->bannedIPs),
          ],
          'eventActive' => [
            'allow' => false,
            'matchCallback' => function () {
              return $this->eventInactive;
            },
            'denyCallback' => function () {
              Yii::$app->session->setFlash('info', 'The competition is not active');
              return  \Yii::$app->getResponse()->redirect([Yii::$app->sys->default_homepage]);
            }
          ],
          'eventStartEnd' => [
            'allow' => false,
            'matchCallback' => function () {
              return $this->eventBetweenStartEnd;
            },
            'denyCallback' => function () {
              if(\Yii::$app->sys->event_start !== false && time() < \Yii::$app->sys->event_start)
                Yii::$app->session->setFlash('info', \Yii::t('app', 'This area is disabled until the event starts'));
              if(\Yii::$app->sys->event_end !== false && time() > \Yii::$app->sys->event_end)
                Yii::$app->session->setFlash('info', \Yii::t('app', 'This area is disabled after the end of the event'));
              return  \Yii::$app->getResponse()->redirect([Yii::$app->sys->default_homepage]);
            }
          ],

          'eventStart' => [
            'allow' => false,
            'matchCallback' => function () {
              return \Yii::$app->sys->event_start !== false && time() < \Yii::$app->sys->event_start;
            },
            'denyCallback' => function () {
              Yii::$app->session->setFlash('info', \Yii::t('app', 'This area is disabled until the competition starts'));
              return  \Yii::$app->getResponse()->redirect([Yii::$app->sys->default_homepage]);
            }
          ],
          'eventEnd' => [
            'allow' => false,
            'matchCallback' => function () {
              return \Yii::$app->sys->event_end !== false && time() > \Yii::$app->sys->event_end;
            },
            'denyCallback' => function () {
              Yii::$app->session->setFlash('info', \Yii::t('app', 'This area is disabled after the competition ends'));
              return  \Yii::$app->getResponse()->redirect([Yii::$app->sys->default_homepage]);
            }
          ],
          'teamsAccess' => [
            'allow' => false,
            'matchCallback' => function () {
              return $this->teamsRequired;
            },
            'denyCallback' => function () {
              return  \Yii::$app->getResponse()->redirect(['/team/default/index']);
            }
          ],
          'disabledRoute' => [
            'allow' => false,
            'matchCallback' => function ($rule, $action) {
              return Yii::$app->DisabledRoute->disabled($action);
            },
            'denyCallback' => function () {
              Yii::$app->session->setFlash('warning', \Yii::t('app', 'This action is disabled globally (or just for you), sorry.'));
              return  \Yii::$app->getResponse()->redirect(Yii::$app->request->referrer ?: [Yii::$app->sys->default_homepage]);
            },
          ],
        ],
      ],
    ];
  }

  protected function getEventInactive()
  {
    return \Yii::$app->sys->event_active === false;
  }

  protected function getEventBetweenStartEnd()
  {
    return \Yii::$app->sys->event_start !== false && (time() < \Yii::$app->sys->event_start || time() > \Yii::$app->sys->event_end);
  }

  protected function getTeamsRequired()
  {
    if (\Yii::$app->sys->team_required === false || \Yii::$app->user->isGuest) {
      return false;
    }

    if (\Yii::$app->user->identity->teamPlayer === null) {
      \Yii::$app->session->setFlash('warning', \Yii::t('app', 'You need to join a team before being able to access this area.'));
      return true;
    }
    if (\Yii::$app->user->identity->teamPlayer->approved !== 1) {
      \Yii::$app->session->setFlash('warning', \Yii::t('app', 'You need to have your team membership approved before being able to access this area.'));
      return true;
    }
    return false;
  }

  public function beforeAction($action)
  {
    if (parent::beforeAction($action)) {
      if ($this->enableCsrfValidation && Yii::$app->getErrorHandler()->exception === null && !$this->request->validateCsrfToken()) {
        Yii::$app->session->setFlash('error', Yii::t('yii', \Yii::t('app', 'Unable to verify your CSRF token, please try again.')));
        Yii::$app->response->redirect(Yii::$app->request->referrer ?: [Yii::$app->sys->default_homepage])->send();
        return false;
      }
      return true;
    }

    return false;
  }
}
