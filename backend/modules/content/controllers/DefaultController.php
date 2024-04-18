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
          Yii::$app->session->setFlash('success', Yii::t('app','Frontpage Scenario updated'));
        }

        return $this->render('sysconfig_content', [
            'model' => $model,
            'hint'=>Yii::t('app','Enter your desired html code to be displayed on the frontpage for guest visitors.')
        ]);
    }

    /**
     * Updates menu items sysconfig key.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionMenuItems()
    {
        $model=Sysconfig::findOneNew('menu_items');
        if(Yii::$app->request->isPost)
        {
          $items=[];
          foreach(Yii::$app->request->post('item') as $item)
          {
            if(trim($item['name'])!== "")
              $items[]=$item;
          }
          if(($model->val=json_encode($items)) && $model->save())
            Yii::$app->session->setFlash('success', Yii::t('app','Menu items updated'));
        }

        return $this->render('menu_items', [
            'model' => $model,
            'hint'=>Yii::t('app','Add or remove menu items from the frontend.')
        ]);
    }

    /**
     * Updates Writeup Rules sysconfig key.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionWriteupRules()
    {
        $model=Sysconfig::findOneNew('writeup_rules');

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
          Yii::$app->session->setFlash('success', Yii::t('app','Writeup rules updated'));
        }

        return $this->render('sysconfig_content', [
            'model' => $model,
            'hint'=>Yii::t('app','Enter your desired html code to be displayed as writeup instructions.')
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
          Yii::$app->session->setFlash('success', Yii::t('app','Offense Scenario updated'));
        }

        return $this->render('sysconfig_content', [
            'model' => $model,
            'hint'=>Yii::t('app','Enter your desired html code to be displayed on default page of the logged in offense players.')
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
          Yii::$app->session->setFlash('success', Yii::t('app','Defense Scenario updated'));
        }

        return $this->render('sysconfig_content', [
            'model' => $model,
            'hint'=>Yii::t('app','Enter your desired html code to be displayed on default page of the logged in defense players.')
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
          Yii::$app->session->setFlash('success', Yii::t('app','Footer Logos content updated'));
        }

        return $this->render('sysconfig_content', [
            'model' => $model,
            'hint'=>Yii::t('app','Enter your desired html code to be displayed on the footer of every page.')
        ]);
    }

    /**
     * Updates CSS content sysconfig key.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionCssOverride()
    {
        $model=Sysconfig::findOneNew('css_override');

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
          Yii::$app->session->setFlash('success', Yii::t('app','CSS overrides content updated'));
        }

        return $this->render('sysconfig_content', [
            'model' => $model,
            'hint'=>Yii::t('app','Enter CSS files or URLs to be loaded (one per-line). Content starting with <kbd>/* ... */</kbd> will be included as raw CSS code.'),
        ]);
    }

    /**
     * Updates JS content sysconfig key.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionJsOverride()
    {
        $model=Sysconfig::findOneNew('js_override');

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
          Yii::$app->session->setFlash('success', Yii::t('app','JS overrides content updated'));
        }

        return $this->render('sysconfig_content', [
            'model' => $model,
            'hint'=>Yii::t('app','Enter Javascript files or URLs to be loaded (one per-line). Content starting with <kbd>/* ... */</kbd> will be included as raw javascript code.'),
        ]);
    }

}
