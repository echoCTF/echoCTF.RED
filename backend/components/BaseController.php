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
                            return \Yii::$app->user->identity->isAdmin;
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

    public static function renderPhpContent($_content_, $_params_ = [])
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        try {
            eval($_content_);
            return ob_get_clean();
        } catch (\Exception $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        } catch (\Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }
}
