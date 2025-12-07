<?php

namespace app\controllers;

use Yii;
use app\models\DbMenu as Menu;
use app\models\DbMenuSearch as MenuSearch;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

class MenuController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return ArrayHelper::merge(parent::behaviors(), []);
  }

  public function actionIndex()
  {
    $searchModel = new MenuSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  public function actionCreate()
  {
    $model = new Menu();

    if ($model->load(Yii::$app->request->post()) && $model->save())
      return $this->redirect(['index']);

    return $this->render('create', ['model' => $model]);
  }

  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post()) && $model->save())
      return $this->redirect(['index']);

    return $this->render('update', ['model' => $model]);
  }

  public function actionDelete($id)
  {
    $this->findModel($id)->delete();
    return $this->redirect(['index']);
  }

  public function actionToggle($id)
  {
    $model = $this->findModel($id);
    $model->enabled = !$model->enabled;
    if ($model->save(false, ['enabled']))
      Yii::$app->session->setFlash('success', \Yii::t('app', 'Menu item {item} toggled to {val}', ['item' => $model->label, 'val' => intval($model->enabled)]));
    else
      Yii::$app->session->setFlash('error', \Yii::t('app', 'Failed to toggle menu item {item}', ['item' => $model->label]));

    return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
  }

  public function actionTree()
  {
    $items = Menu::find()
      ->orderBy(['parent_id' => SORT_ASC, 'sort_order' => SORT_ASC])
      ->asArray()
      ->all();

    return $this->render('tree', [
      'items' => $items
    ]);
  }

  public function actionSaveTree()
  {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $tree = Yii::$app->request->post('tree');
    if (!$tree) return ['success' => false];

    $this->saveTreeRecursive($tree, null);

    return ['success' => true];
  }

  private function saveTreeRecursive($items, $parentId)
  {
    foreach ($items as $index => $item) {
      Menu::updateAll(
        ['parent_id' => $parentId, 'sort_order' => $index],
        ['id' => $item['id']]
      );

      if (isset($item['children']))
        $this->saveTreeRecursive($item['children'], $item['id']);
    }
  }

  protected function findModel($id)
  {
    if (($model = Menu::findOne($id)) !== null)
      return $model;

    throw new NotFoundHttpException();
  }
}
