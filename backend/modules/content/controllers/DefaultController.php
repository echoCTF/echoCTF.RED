<?php

namespace app\modules\content\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\modules\settings\models\Sysconfig;
use yii\helpers\ArrayHelper;

/**
 * Default controller for the `content` module
 */
class DefaultController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Updates frontpage content sysconfig key.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionFrontpageScenario()
    {
        $model=Sysconfig::findOneNew('frontpage_scenario');

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
          Yii::$app->session->setFlash('success', 'Frontpage Scenario updated');
        }

        return $this->render('sysconfig_content', [
            'model' => $model,
        ]);
    }

    /**
     * Updates offense scenario content sysconfig key.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionOffenseScenario()
    {
        $model=Sysconfig::findOneNew('offense_scenario');

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
          Yii::$app->session->setFlash('success', 'Offense Scenario updated');
        }

        return $this->render('sysconfig_content', [
            'model' => $model,
        ]);
    }

    /**
     * Updates defense scenario content sysconfig key.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDefenseScenario()
    {
        $model=Sysconfig::findOneNew('defense_scenario');

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
          Yii::$app->session->setFlash('success', 'Defense Scenario updated');
        }

        return $this->render('sysconfig_content', [
            'model' => $model,
        ]);
    }

    /**
     * Updates footer logos content sysconfig key.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionFooterLogos()
    {
        $model=Sysconfig::findOneNew('footer_logos');

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
          Yii::$app->session->setFlash('success', 'Footer Logos content updated');
        }

        return $this->render('sysconfig_content', [
            'model' => $model,
        ]);
    }

}
