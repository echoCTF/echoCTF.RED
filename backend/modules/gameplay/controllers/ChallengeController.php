<?php

namespace app\modules\gameplay\controllers;

use Yii;
use app\modules\gameplay\models\Challenge;
use app\modules\gameplay\models\ChallengeSearch;
use app\modules\gameplay\models\Question;
use app\modules\gameplay\models\QuestionSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * ChallengeController implements the CRUD actions for Challenge model.
 */
class ChallengeController extends \app\components\BaseController
{
  /**
   * {@inheritdoc}
   */
    public function behaviors()
    {
      return ArrayHelper::merge(parent::behaviors(),[]);
    }

    /**
     * Lists all Challenge models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel=new ChallengeSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Challenge model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $query=Question::find()->joinWith('challenge');

        $query->select('question.*,(SELECT COUNT(question_id) FROM player_question WHERE question.id=player_question.question_id) as answered');
        // add conditions that should always apply here

        $dataProvider=new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->andFilterWhere([
            'question.challenge_id' => $id,
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['weight' => SORT_ASC,'id'=>SORT_ASC],
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'challengename' => [
                      'asc' => ['challengename' => SORT_ASC],
                      'desc' => ['challengename' => SORT_DESC],
                  ],
                  'answered' => [
                      'asc' => ['answered' => SORT_ASC],
                      'desc' => ['answered' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'questionProvider'=>$dataProvider,
        ]);
    }

    /**
     * Creates a new Challenge model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model=new Challenge();

        if($model->load(Yii::$app->request->post()) && $model->save())
        {
            $model->file=UploadedFile::getInstance($model, 'file');
            try
            {
              if($model->file)
              {
                if(trim($model->filename)==='')
                {
                    $model->filename=$model->id;
                    $model->updateAttributes(['filename'=>$model->id]);
                }
                $model->file->saveAs(Yii::getAlias(Yii::$app->sys->challenge_home).'/'.$model->filename);
              }
              Yii::$app->session->addFlash('success', Yii::t('app','Challenge [{name}] created',['name'=>Html::encode($model->name)]));
              Yii::$app->session->addFlash('warning', Yii::t('app','Don\'t forget to create a question for the challenge.'));
            }
            catch(\Exception $e)
            {
              Yii::$app->session->setFlash('error', Yii::t('app','Failed to create challenge [{name}]',['name'=>Html::encode($model->name)]));
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Challenge model.
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
            $model->file=UploadedFile::getInstance($model, 'file');
            if($model->file !== null)
            {
                if(trim($model->filename)==='')
                {
                    $model->filename=$model->id;
                    $model->updateAttributes(['filename'=>$model->id]);
                }
                $model->file->saveAs(Yii::getAlias(Yii::$app->sys->challenge_home).'/'.$model->filename);
            }
            Yii::$app->session->addFlash('success', Yii::t('app','Challenge [{name}] updated',['name'=>Html::encode($model->name)]));
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Challenge model.
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
     * Finds the Challenge model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Challenge the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if(($model=Challenge::findOne($id)) !== null)
        {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app','The requested page does not exist.'));
    }
}
