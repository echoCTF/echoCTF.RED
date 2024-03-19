<?php

namespace app\modules\gameplay\controllers;

use Yii;
use app\modules\gameplay\models\Hint;
use app\modules\gameplay\models\HintSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * HintController implements the CRUD actions for Hint model.
 */
class HintController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all Hint models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new HintSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Hint model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Give a hint to existing active users.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionGive($id)
    {
        // fetch hint
        $hint=$this->findModel($id);
        $db=Yii::$app->db;
        $transaction=$db->beginTransaction();
        try
        {
//          if($hint->finding)
//          {
//            // fetch player_finding
//          }
//          if($hint->treasure)
//          {
//            // fetch player_treasure
//          }
//
//          if($hint->question)
//          {
//            // fetch player_question
//          }
          if($hint->player_type==='both')
          {
            foreach(['offense','defense'] as $val)
            {
              $db->createCommand('INSERT INTO player_hint (player_id, hint_id) SELECT id,:hint_id FROM player WHERE active=1 and `type`=:ptype ON DUPLICATE KEY UPDATE player_id=values(player_id)')
              ->bindValue(':hint_id', $hint->id)
              ->bindValue(':ptype', $val)
              ->execute();
            }
          }
          else
            $db->createCommand('INSERT INTO player_hint (player_id, hint_id) SELECT id,:hint_id FROM player WHERE active=1 and `type`=:ptype ON DUPLICATE KEY UPDATE player_id=values(player_id)')
                    ->bindValue(':hint_id', $hint->id)
                    ->bindValue(':ptype', $hint->player_type)
                    ->execute();
          $transaction->commit();
        }
        catch(\Exception $e)
        {
            $transaction->rollBack();
            \Yii::$app->getSession()->setFlash('error', Yii::t('app','Failed to give hint to users'));
            return $this->redirect(['view', 'id'=>$id]);
        }
        catch(\Throwable $e)
        {
            $transaction->rollBack();
            \Yii::$app->getSession()->setFlash('error', Yii::t('app','Failed to give hint to users'));
            return $this->redirect(['view', 'id'=>$id]);
        }
        \Yii::$app->getSession()->setFlash('success', Yii::t('app','Hint was sent to all active users.'));
        return $this->redirect(['view', 'id'=>$id]);

    }

    /**
     * Creates a new Hint model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new Hint();

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Hint model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model=$this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Hint model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Hint model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Hint the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(($model=Hint::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app','The requested page does not exist.'));
    }
}
